<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Models\EmailLog;
use App\Models\ProfileDataMain;
use App\Mail\SertifikatEmail;
use App\Services\WhatsappNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BlastCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blast:certificate {event_id : ID kegiatan yang sertifikatnya akan dikirim}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email + WhatsApp berisi sertifikat untuk semua peserta kegiatan tertentu yang belum dikirimi';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // event_id used to be hardcoded to '8' here, meaning this command
        // only ever worked for one specific past event - it's now an
        // argument so it can actually be reused for any event.
        $eventId = $this->argument('event_id');

        $certificates = Certificate::where('event_id', $eventId)
    ->where('is_emailed', '0')
    ->whereNotNull('email') // Memastikan kolom email tidak NULL
    ->get();

    if ($certificates->isEmpty()) {
            $this->info("Semua sertifikat untuk event ini sudah dikirim.");
            return 0;
        }

        $count = 0;

    foreach ($certificates as $index => $cert) {
       try {
                // 2. Kirim email masuk ke antrean (Queue)
                $emailLog = EmailLog::start(SertifikatEmail::class, 'certificate', $cert->email, $cert);
                Mail::to($cert->email)->later(now()->addSeconds($index * 20), (new SertifikatEmail($cert))->withEmailLog($emailLog->id));

                WhatsappNotifier::send(
                    'certificate',
                    ProfileDataMain::where('nip', $cert->nip)->value('contact'),
                    "Halo {$cert->name}, terima kasih telah berpartisipasi dalam kegiatan {$cert->body}. Sertifikat Anda dapat dilihat/diunduh di: {$cert->link}. Terima kasih - Aspro SDMA.",
                    $cert
                );

                // 3. UPDATE STATUS: Ubah dari 0 ke 1 agar tidak terkirim ganda besok
                $cert->update([
                    'is_emailed' => 1
                ]);

                $count++;
                $this->info("{$count}. Antrean dibuat untuk: {$cert->name} ({$cert->email})");

            } catch (\Exception $e) {
                // Catat log jika ada email yang gagal diproses ke queue
                Log::error("Gagal memproses email untuk {$cert->name}: " . $e->getMessage());
                $this->error("Gagal memproses: {$cert->name}");
            }
    }

        $this->info("--- SELESAI ---");
        $this->info("Berhasil menambahkan {$count} email ke dalam antrean (Tabel Jobs).");
        $this->info("Jangan lupa jalankan 'php artisan queue:work' untuk mengirimnya.");
    }
}
