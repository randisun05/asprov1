<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\SendEmailForgetPassword;
use App\Models\Achievement;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\DetailEvent;
use App\Models\DocumentDigital;
use App\Models\Event;
use App\Models\Management;
use App\Models\Member;
use App\Models\Post;
use App\Models\ProfileDataMain;
use App\Models\ProfileDataPosition;
use App\Models\ReactDetail;
use App\Models\Registration;
use App\Models\RegistrationGroup;
use App\Models\TemplateCertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Log;
use F9WebLtd\QrCode\Facades\QrCode;

class PublicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $events = Event::whereNot('title', 'media')->latest()->take(3)->get();
        $analisdone = Registration::where('position', 'Analis SDM Aparatur')->where('status', 'approved')->count();
        $analisproses = Registration::where('position', 'Analis SDM Aparatur')->where('status', 'submission')->where('emailstatus', 0)->count();
        $analispaid = Registration::where('position', 'Analis SDM Aparatur')
            ->where('emailstatus', '>', 0)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->count();
        $pranatadone = Registration::where('position', 'Pranata SDM Aparatur')->where('status', 'approved')->count();
        $pranataproses = Registration::where('position', 'Pranata SDM Aparatur')->where('status', 'submission')->where('emailstatus', '0')->count();
        $pranatapaid = Registration::where('position', 'Pranata SDM Aparatur')->where('emailstatus', '>', 0)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->count();
        $showPopup = Management::where('item', 'popup')->sum('status');
        $datas = Management::where('item', 'popup')->where('status', '1')->get();
        $agencydone = Registration::distinct()->count('agency');

        return view('Index', [
            "events" => $events,
            'analisdone' => $analisdone,
            'analisproses' => $analisproses,
            'pranatadone' => $pranatadone,
            'pranataproses' => $pranataproses,
            'agencydone' => $agencydone,
            'pranatapaid' => $pranatapaid,
            'analispaid' => $analispaid,
            'showPopup' => $showPopup,
            'datas' => $datas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function maintenance()
    {
        return inertia('Public/Website/Maintenance/Index', [
            'title' => "Maintenance",

        ]);
    }


    public function about()
    {
        $data = Management::where('item', 'siapakita')->first();
        return inertia('Public/Website/About/About', [
            'title' => "Siapa Kita?",
            'data' => $data
        ]);
    }

    public function ketuaUmum()
    {

        $data = Management::where('item', 'ketuaumum')->first();
        return inertia('Public/Website/About/KetuaUmum', [
            'title' => "Ketua Umum",
            'data' => $data
        ]);
    }

    public function visiMisi()
    {
        $data = Management::where('item', 'visimisi')->first();
        return inertia('Public/Website/About/VisiMisi', [
            'title' => "Visi Misi",
            'data' => $data
        ]);
    }

    public function strukturOrganisasi()
    {
        $data = Management::where('item', 'strukturorganisasi')->get();
        return inertia('Public/Website/About/StrukturOrganisasi', [
            'title' => "Struktur Organisasi",
            'data' => $data
        ]);

        //  return inertia('Public/Website/Maintenance/Index', [
        //     'title' => "Struktur Organisasi",
        //     'data' => $data
        // ]);
    }

    public function sejarah()
    {
        $data = Management::where('item', 'sejarah')->first();
        return inertia('Public/Website/About/Sejarah', [
            'title' => "Sejarah Terbentuknya Aspro SDMA",
            'data' => $data
        ]);
    }

    public function peraturanOrganisasi()
    {
        $datas = Management::when(request()->q, function ($query) {
            $query->where('body', 'like', '%' . request()->q . '%');
        })
            ->where('item', 'peraturan')
            ->where('status', '1')
            ->latest()
            ->paginate(6);

        $datas->appends(['q' => request()->q]);

        return inertia('Public/Website/About/PeraturanOrganisasi', [
            'title' => "Peraturan Organisasi",
            'datas' => $datas
        ]);
    }

    public function hubunganMasyarakat()
    {
        $data = Management::where('item', 'proker')->where('sub', 'humas')->first();
        return inertia('Public/Website/Program/HubunganMasyarakat', [
            'title' => "Bidang Hubungan Masyarakat dan Kerja Sama",
            'data' => $data
        ]);
    }

    public function hukumAdvokasi()
    {
        $data = Management::where('item', 'proker')->where('sub', 'hukum')->first();
        return inertia('Public/Website/Program/HukumAdvokasi', [
            'title' => "Bidang Hukum dan Advokasi",
            'data' => $data
        ]);
    }

    public function keanggotaan()
    {
        $data = Management::where('item', 'proker')->where('sub', 'anggota')->first();
        return inertia('Public/Website/Program/Keanggotaan', [
            'title' => "Bidang Keanggotaan dan Organisasi",
            'data' => $data

        ]);
    }

    public function pengembangan()
    {
        $data = Management::where('item', 'proker')->where('sub', 'pengembangan')->first();
        return inertia('Public/Website/Program/Pengembangan', [
            'title' => "Bidang Pengembangan Kapasitas Insani",
            'data' => $data
        ]);
    }

    public function sumberPendanaan()
    {
        $data = Management::where('item', 'proker')->where('sub', 'pendanaan')->first();
        return inertia('Public/Website/Program/SumberPendanaan', [
            'title' => "Bidang Sumber Pendanaan Organisasi",
            'data' => $data
        ]);
    }

    public function beritaView(Post $post)
    {

        // Get the related category of the post
        $user = auth()->guard('member')->user();

        if ($user) {

            $exist = ReactDetail::where('post_id', $post->id)->where('member_id', $user->id)->where('react_id', '3')->first();

            if (!$exist) {
                $react = ReactDetail::create([
                    'post_id' => $post->id,
                    'member_id' => $user->id,
                    'react_id' => '3',
                    'status' => '1',
                    'type' => 'post'
                ]);
            }
        }

        $category = $post->category;
        $member = $post->member;

        return inertia('Public/Website/Posts/Show', [
            'title' => $post->title,
            'post' => $post,
            'category' => $category,
            'member' => $member
        ]);
    }

    public function berita()
    {
        return inertia('Public/Website/Maintenance/Index', [
            'title' => "Maintenance",
        ]);
    }

    public function artikel()
    {
        return inertia('Public/Website/Maintenance/Index', [
            'title' => "ASDMA Menulis",
        ]);
    }

    public function kontak()
    {
        $data = Management::where('item', 'kontak')->first();
        return inertia('Public/Website/About/Kontak-kami', [
            'title' => "Kontak Kami",
            'data' => $data
        ]);
    }

    public function dataAnggota()
    {

        //      $dataCountsByPosition = ProfileDataPosition::whereIn('position', ['Analis SDM Aparatur', 'Pranata SDM Aparatur'])
        //     ->groupBy('position')
        //     ->select('position', DB::raw('count(*) as total'))
        //     ->get()
        //     ->pluck('total', 'position');

        // $dataCountsByLevel = ProfileDataPosition::whereIn('level', [
        //         'Terampil', 'Mahir', 'Penyelia', 'Ahli Pertama', 'Ahli Muda', 'Ahli Madya', 'Ahli Utama'
        //     ])
        //     ->groupBy('level')
        //     ->select('level', DB::raw('count(*) as total'))
        //     ->get()
        //     ->pluck('total', 'level')
        //     ->sortBy(function ($value, $key) {
        //         $order = ['Terampil','Mahir','Penyelia','Ahli Pertama','Ahli Muda','Ahli Madya','Ahli Utama'];
        //         return array_search($key, $order);
        //     });

        //     $countsPerMonth = [];
        //     $accumulatedCounts = [];
        //     $totalCount = 0;

        //     $startYear = 2024;
        //     $currentYear = date('Y');  // Tahun saat ini (misalnya: 2025)
        //     $currentMonth = date('n'); // Bulan saat ini (misalnya: 2 untuk Februari)

        //     for ($year = $startYear; $year <= $currentYear; $year++) {
        //         // Tentukan batas bulan (Desember untuk tahun sebelumnya, bulan saat ini untuk tahun berjalan)
        //         $endMonth = ($year == $currentYear) ? $currentMonth : 12;

        //         for ($month = 1; $month <= $endMonth; $month++) {
        //             $monthlyCount = ProfileDataPosition::with('main')
        //                 ->whereYear('created_at', $year)
        //                 ->whereMonth('created_at', $month)
        //                 ->count();

        //             $totalCount += $monthlyCount;
        //             if ($totalCount > 0) { // Hanya simpan jika ada data
        //                 $key = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT);
        //                 $countsPerMonth[$key] = $monthlyCount;
        //                 $accumulatedCounts[$key] = $totalCount;
        //             }
        //         }
        //     }



        $now = Carbon::now();
        $now->setLocale('id');
        $formattedDate = $now->translatedFormat('d F Y');

        //    $datas = Management::when(request()->q, function($query) {
        //        $query->where('body', 'like', '%' . request()->q . '%');
        //    })
        //    ->where('item', 'dataanggota')
        //    ->latest()
        //    ->paginate(6);

        //     $datas->appends(['q' => request()->q]);

        return inertia('Public/Website/Posts/DataAnggota', [
            'title' => "Data Keanggotaan" . " per tanggal " . $formattedDate,
            // 'datas' => $datas,
            // 'dataCountsByPosition' => $dataCountsByPosition,
            // 'dataCountsByLevel' => $dataCountsByLevel,
            // 'countsPerMonth' => $countsPerMonth,
            // 'accumulatedCounts' => $accumulatedCounts,

        ]);
    }

    public function dataAnggotaChart()
    {
        $dataCountsByPosition = ProfileDataPosition::whereIn('position', ['Analis SDM Aparatur', 'Pranata SDM Aparatur'])
            ->groupBy('position')
            ->select('position', DB::raw('count(*) as total'))
            ->get()
            ->pluck('total', 'position');

        $dataCountsByLevel = ProfileDataPosition::whereIn('level', [
            'Terampil',
            'Mahir',
            'Penyelia',
            'Ahli Pertama',
            'Ahli Muda',
            'Ahli Madya',
            'Ahli Utama'
        ])
            ->groupBy('level')
            ->select('level', DB::raw('count(*) as total'))
            ->get()
            ->pluck('total', 'level')
            ->sortBy(function ($value, $key) {
                $order = ['Terampil', 'Mahir', 'Penyelia', 'Ahli Pertama', 'Ahli Muda', 'Ahli Madya', 'Ahli Utama'];
                return array_search($key, $order);
            });

        $countsPerMonth = [];
        $accumulatedCounts = [];
        $accumulatedCountsByPosition = [];

        $totalCount = 0;
        $positionTotals = [];

        $startYear = 2024;
        $currentYear = date('Y');
        $currentMonth = date('n');

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $endMonth = ($year == $currentYear) ? $currentMonth : 12;

            $startMonth = ($year == 2024) ? 4 : 1;
            for ($month = $startMonth; $month <= $endMonth; $month++) {
                $key = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT);
                // Total per bulan
                $monthlyCount = ProfileDataPosition::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();

                $totalCount += $monthlyCount;
                $countsPerMonth[$key] = $monthlyCount;
                $accumulatedCounts[$key] = $totalCount;

                // Per posisi
                $monthlyCountsByPosition = ProfileDataPosition::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereIn('position', ['Analis SDM Aparatur', 'Pranata SDM Aparatur'])
                    ->groupBy('position')
                    ->select('position', DB::raw('count(*) as total'))
                    ->get()
                    ->pluck('total', 'position');

                foreach ($monthlyCountsByPosition as $position => $count) {
                    if (!isset($accumulatedCountsByPosition[$position])) {
                        $accumulatedCountsByPosition[$position] = [];
                    }

                    $accumulatedCountsByPosition[$position][$key] = $count;
                }
            }
        }


        // 1. Data Berdasarkan Jenis Kelamin (Gender) via SQL Substring
        $dataCountsByGender = ProfileDataMain::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = [
                    'L' => 'Laki-laki',
                    'P' => 'Perempuan',
                ];
                return [$label[$item->gender] ?? $item->gender => $item->total];
            });


        // 2. Data Berdasarkan Tipe (Pusat vs Daerah)
        // Cek Title: Jika 'BKN Pusat' -> Instansi Pusat, Else -> Instansi Daerah
        $dataCountsByType = DB::table('profile_data_positions')
            ->leftJoin('instansis', 'profile_data_positions.agency', '=', 'instansis.title')
            ->select(DB::raw("CASE
            WHEN instansis.type = 'BKN Pusat' OR instansis.type IS NULL THEN 'Instansi Pusat'
            ELSE 'Instansi Daerah'
        END as kategori_tipe"), DB::raw('count(*) as total'))
            ->groupBy('kategori_tipe')
            ->pluck('total', 'kategori_tipe')
            ->sortBy(function ($value, $key) {
                $order = ['Instansi Pusat', 'Instansi Daerah'];
                return array_search($key, $order);
            });

        // 3. Penyebaran Wilayah (Group By instansis.type dengan Custom Order)
        $regionOrder = [
            'BKN Pusat',
            'Kanreg I BKN',
            'Kanreg II BKN',
            'Kanreg III BKN',
            'Kanreg IV BKN',
            'Kanreg V BKN',
            'Kanreg VI BKN',
            'Kanreg VII BKN',
            'Kanreg VIII BKN',
            'Kanreg IX BKN',
            'Kanreg X BKN',
            'Kanreg XI BKN',
            'Kanreg XII BKN',
            'Kanreg XIII BKN',
            'Kanreg XIV BKN'
        ];

        $dataCountsByRegion = DB::table('profile_data_positions')
            ->leftJoin('instansis', 'profile_data_positions.agency', '=', 'instansis.title')
            ->select(
                // Jika tidak ada di tabel instansis, paksa masuk ke 'Instansi Pusat'
                DB::raw("IFNULL(instansis.type, 'BKN Pusat') as region_type"),
                DB::raw('count(*) as total')
            )
            ->groupBy('region_type')
            ->get()
            ->sortBy(function ($item) use ($regionOrder) {
                foreach ($regionOrder as $key => $orderedType) {
                    if (stripos($item->region_type, $orderedType) !== false) {
                        return $key;
                    }
                }
                return 99;
            })
            ->values()
            ->pluck('total', 'region_type');


        return inertia('Public/Website/Posts/ChartAnggota', [
            'dataCountsByPosition' => $dataCountsByPosition,
            'dataCountsByLevel' => $dataCountsByLevel,
            'countsPerMonth' => $countsPerMonth,
            'accumulatedCounts' => $accumulatedCounts,
            'accumulatedCountsByPosition' => $accumulatedCountsByPosition,
            'dataCountsByGender' => $dataCountsByGender,
            'dataCountsByType' => $dataCountsByType,
            'dataCountsByRegion' => $dataCountsByRegion,
        ]);
    }


    public function faq()
    {
        $datas = Management::when(request()->q, function ($query) {
            $query->where('body', 'like', '%' . request()->q . '%');
        })
            ->where('item', 'faq')
            ->latest()
            ->paginate(6);
        $datas->appends(['q' => request()->q]);
        return inertia('Public/Website/FAQ/faq', [
            'title' => "FAQ",
            'datas' => $datas
        ]);
    }

    public function forgetPassword()
    {
        return inertia('User/Auth/Forget', []);
    }

    public function emailforgetPassword(Request $request)
    {

        $request->validate([
            'nip' => 'required',
            'email' => 'required|email', // Tambahkan validasi email
        ], [
            'nip.required' => 'Nip harus diisi',
            'email.required' => 'Email terdaftar harus diisi',
            'email.email' => 'Format email tidak valid', // Pesan untuk validasi email
        ]);

        $data = Member::where('nip', $request->nip)->first();

        // Periksa apakah data ditemukan dan email sesuai dengan yang dimasukkan pengguna
        if ($data && $data->email === $request->email) {
            // Generate a UUID for the password code
            $passwordCode = \Illuminate\Support\Str::uuid()->toString();
            $data->update([
                'code-password' => $passwordCode,
                'code_password_expires_at' => now()->addMinutes(60),
            ]);

            Mail::to($data->email)->send(new SendEmailForgetPassword($data));
            return back()->with('success', 'Email telah dikirimkan untuk reset password.');
        }

        // Jika data tidak ditemukan atau email tidak sesuai
        return redirect()->back()->with('error', 'Data tidak sesuai.');
    }

    public function IndexforgetPassword(Member $member, $id)
    {
        $member = $member->where('code-password', $id)->first();

        if (!$member || $member->{'code-password'} === null || !$this->resetCodeStillValid($member)) {
            return redirect()->route('user.login')->with('error', 'Link telah ditutup.');
        }

        return inertia('User/Auth/Index', [
            'member' => $member
        ]);
    }

    public function ResetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]+$/',
                'confirmed',
                // At least one lowercase, one uppercase, one number, and one special character
            ],
        ], [
            'password.regex' => 'Password terdiri dari kombinasi huruf kapaital, huruf kecil, angka dan karakter spesial, contoh:A5proSDM@',
            'password.required' => 'Password baru harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak sama',
            'oldpassword.min:8' => 'Password baru minimal 8 karakter'
        ]);

        $member = Member::where('code-password', $id)->first();

        if (!$member || !$this->resetCodeStillValid($member)) {
            return redirect()->route('user.login')->with('error', 'Link reset password sudah tidak berlaku, silakan minta link baru.');
        }

        $member->update([
            'password' => Hash::make($request->password),
            'code-password' => null,
            'code_password_expires_at' => null,
        ]);

        return redirect()->route('user.login')->with('success', 'Password berhasil direset.');
    }

    /**
     * A code-password issued before this expiry column existed has a null
     * expiry - treat those as still valid rather than locking out members
     * whose reset email predates the migration.
     */
    private function resetCodeStillValid(Member $member): bool
    {
        return $member->code_password_expires_at === null
            || now()->lt($member->code_password_expires_at);
    }

    public function profileView($qr_link)
    {
        $data = Member::where('qr_link', $qr_link)->first();
        $events = DetailEvent::with('event')->where('member_id', $data->id)->get();
        $certificates = Certificate::whereIn('event_id', $events->pluck('event.id'))
            ->where('nip', $data->nip)
            ->get()
            ->keyBy('event_id'); // Mengubah collection menjadi associative array dengan 'event_id' sebagai key


        $achievments = Achievement::where('member_id', $data->id)->get();
        // Tambahkan certificates ke setiap event secara manual
        $events = $events->map(function ($event) use ($certificates) {
            $event->certificate = $certificates->get($event->event->id); // Ambil satu sertifikat berdasarkan event_id
            return $event;
        });

        // $events = DetailEvent::with('event')->where('member_id', '10')->get();
        // $achievments = Achievement::where('member_id', '10')->get();

        return inertia('Public/Profile/Index', [
            'title' => 'Profile Anggota',
            'data' => $data,
            'achievments' => $achievments,
            'events' => $events,
            'certificates' => $certificates
        ]);
    }

    public function downloadSertifikat($qr_link)
    {

        $data = Member::where('qr_link', $qr_link)->first();

        $data = Certificate::with('event')->where('link', $id)->first();

        $data = Certificate::with('event')->findOrFail($id);
        // Generate QR Code
        $qrLink = $data->qr_code;
        QrCode::format('png')->size(300)->generate($qrLink);
        $qr = QrCode::generate($qrLink);
        return view('Reports.Certificates.Certificate', compact('data', 'qr'));
    }

    public function documentVerif($id)
    {

        $docu = DocumentDigital::where('document', 'documents/' . $id . '_ttd.pdf')->first();
        if (!$docu) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        };
        $ttd = Member::where('nip', $docu->nipttd)->first('name');
        $paraf = Member::where('nip', $docu->nipparaf)->first('name');

        return inertia('Public/Website/DocuDigi/Index', [
            'title' => 'Verifikasi Produk Aspro SDMA',
            'docu' => $docu,
            'ttd' => $ttd,
            'paraf' => $paraf
        ]);
    }


    public function certificateView($id)
    {

        $data = Certificate::with('event')->where('link', $id)->first();

        return inertia('Public/Website/Events/Certificate', [
            'title' => 'Verifikasi Sertifikat Kegiatan Aspro SDMA',
            'data' => $data,

        ]);
    }

    public function certificatesShow($id)
    {

        $data = Certificate::with('event')->findOrFail($id);
        $template = TemplateCertificate::where('id', $data->template)->first();
        $nomor = substr($data->no_certificate, 0, 4);
        $storagePath = storage_path('app/public/sertifikat');

        // Generate QR Code
        $qrLink = $data->qr_code;
        QrCode::format('png')->size(300)->generate($qrLink);
        // Generate QR Code (variable $qr removed as it was unused)
        QrCode::generate($qrLink);

        // Build the command

        $command = "python3 " . escapeshellarg(base_path('resources/py/certificate.py')) .
            // " " . escapeshellarg('template=' . 'storage/documents/' . $data->template) .
            " " . escapeshellarg(public_path('storage/' . $template->image)) .
            " " . escapeshellarg('nomor=' . $data->no_certificate) .
            " " . escapeshellarg('nama=' . $data->name) .
            " " . escapeshellarg('qr=' . $qrLink) .
            " " . escapeshellarg('file=' . 'sertifikat-' . $nomor . '-' . $data->name . '.pdf') .
            " " . escapeshellarg('path=' . $storagePath);

        $output = shell_exec($command);

        if ($output === null) {
            //\Log::error("Python Error: " . $output);
            return back()->with('error', 'Gagal menghasilkan sertifikat.');
            //  return response()->json(['error' => 'Command execution failed.'], 500);
        }

        $data->update([
            'doc' => 'sertifikat/' . 'sertifikat-' . $nomor . '-' . $data->name . '.pdf'
        ]);

        return response()->download(public_path('storage/' . $data->doc), 'sertifikat-' . $nomor . '-' . $data->name . '.pdf')->deleteFileAfterSend(true);

        //  return response()->json(['success' => 'Certificate generated successfully.']);

        // Return success response
        //  return redirect()->route('admin.events.certificates.index', $event)->with('success', 'Sertifikat berhasil dihasilkan');

    }


    public function certificateSearch()
    {
        $data = [];

        return inertia('Public/Website/Posts/CertificateSearch', [
            'title' => 'Cari Sertifikat Kegiatan Aspro SDMA',
            'data' => $data,
        ]);
    }

    public function certificateFilter()
    {
        $datas = Certificate::when(request()->q, function ($query) {
            $query->where('nip', request()->q);
        })
            ->get();

        return inertia('Public/Website/Posts/CertificateSearch', [
            'title' => 'Cari Sertifikat Kegiatan Aspro SDMA',
            'datas' => $datas,
        ]);
    }

    public function indexCertificate(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return redirect()->back()->withErrors(['message' => 'Event tidak ditemukan.']);
        }

        $nip = $request->q; // Ambil NIP dari input
        $currentCertificate = null;
        $allCertificates = [];

        if ($nip) {
            // Cari sertifikat di event ini
            $currentCertificate = Certificate::where('event_id', $event->id)
                ->where('nip', $nip)
                ->first();

            // Cari riwayat sertifikat di event lain
            $allCertificates = Certificate::with('event')
                ->where('nip', $nip)
                ->latest()
                ->get();
        }

        return inertia('Public/Website/Events/CertificateEvent', [
            'title' => 'Cek Sertifikat - ' . $event->title,
            'event' => $event,
            'currentCertificate' => $currentCertificate,
            'allCertificates' => $allCertificates,
            'querySearch' => $nip // SANGAT PENTING: kirim balik nilai NIP ini
        ]);
    }

    public function indexAbsen($slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return redirect()->back()->withErrors(['message' => 'Event tidak ditemukan.']);
        }

        if ($event->absen == "N") {
            $title = 'Absensi untuk event ini ditutup/belum dibuka.';
        }

        return inertia('Public/Website/Events/Absen', [
            'title' => $title ?? 'Absensi Kegiatan Aspro SDMA' . ' - ' . $event->title,
            'event' => $event,
        ]);
    }

    public function absen(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nip'      => 'required',
            'name'     => 'required',
            'agency'   => 'required',
        ], [
            'nip.required'    => 'NIP/NIK wajib diisi.',
            'nip.numeric'     => 'NIP/NIK hanya boleh berisi angka.',
            'name.required'   => 'Nama lengkap wajib diisi.',
            'agency.required' => 'Instansi wajib diisi.',
        ]);

        $event = Event::findOrFail($id);

        // Gunakan Transaction untuk keamanan nomor urut
        return DB::transaction(function () use ($request, $event) {

            // Cek duplikasi sertifikat
            $existing = Certificate::where('event_id', $event->id)
                ->where('nip', $request->nip)
                ->first();

            if ($existing) {
                // Mengirimkan error khusus 'message' yang nantinya ditangkap Swal
                return redirect()->back()->withErrors([
                    'message' => 'NIP/NIK ini sudah melakukan absensi untuk event ini!'
                ]);
            }

            // Logika Penomoran Mutakhir
            $targetDate = now();
            $month = $targetDate->format('m');
            $year = $targetDate->format('Y');

            // Ambil nomor tertinggi secara presisi (integer)
            $lastNumber = Certificate::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->map(function ($cert) {
                    return (int) explode('/', $cert->no_certificate)[0];
                })
                ->max() ?? 0;

            $currentNumber = $lastNumber + 1;

            // Safety loop untuk memastikan nomor benar-benar unik
            do {
                $newNumber = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
                $kodeKegiatan = $event->category ?? 'Kombel';
                $nomor = "{$newNumber}/{$kodeKegiatan}/PP Aspro SDMA/{$month}/{$year}";

                if (Certificate::where('no_certificate', $nomor)->exists()) {
                    $currentNumber++;
                } else {
                    break;
                }
            } while (true);

            $link = (string) Str::uuid();

            // Simpan Sertifikat
            Certificate::create([
                'event_id'       => $event->id,
                'no_certificate' => $nomor,
                'category'       => $kodeKegiatan,
                'nip'            => $request->nip,
                'name'           => $request->name,
                'body'           => $event->title,
                'date'           => $targetDate->format('Y-m-d'),
                'template'       => $event->template,
                'status'         => '1',
                'qr_code'        => "https://asprosdma.id/certificates/$link",
                'link'           => $link,
                'doc'            => $request->agency,
            ]);

            return redirect()->back()->with('success', 'Absensi berhasil dan sertifikat telah terbit!');
        });
    }

    public function eventDashboard(Event $event)
    {

        $event_id = $event->id;

        /*
STATS
*/
        $total = DetailEvent::where('event_id', $event_id)->count();

        $active = DetailEvent::where('event_id', $event_id)
            ->whereNotNull('start_at')
            ->whereNull('end_at')
            ->count();

        $finished = DetailEvent::where('event_id', $event_id)
            ->whereNotNull('end_at')
            ->count();

        $pending = DetailEvent::where('event_id', $event_id)
            ->whereNull('start_at')
            ->count();


        /*
FASTEST 5
*/

        $minDuration = ($event->duration * 60) * 0.3;

        $fastest = DetailEvent::with('member')
            ->where('event_id', $event_id)
            ->whereNotNull('end_at')
            ->whereRaw('TIMESTAMPDIFF(SECOND,start_at,end_at) > ?', [$minDuration])
            ->select(
                '*',
                DB::raw('TIMESTAMPDIFF(SECOND,start_at,end_at) as duration')
            )
            ->orderBy('duration', 'asc')
            ->limit(5)
            ->get();


        /*
TOP SCORE
*/

        $topScores = DetailEvent::with('member')
            ->where('event_id', $event_id)
            ->whereNotNull('grade')
            ->select(
                '*',
                DB::raw('TIMESTAMPDIFF(SECOND,start_at,end_at) as duration'),
                DB::raw('SEC_TO_TIME(TIMESTAMPDIFF(SECOND,start_at,end_at)) as duration_format')
            )
            ->orderBy('grade', 'desc')
            ->orderBy('duration', 'asc')
            ->limit(10)
            ->get();


        /*
LIVE ACTIVITY
*/

      $activities = DetailEvent::where('event_id', $event_id)
        ->with('member')
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get()
        ->map(function ($d) {

            if ($d->end_at) {
                $status = "menyelesaikan tryout";
            } elseif ($d->start_at) {
                $status = "sedang mengerjakan tryout";
            } else {
                $status = "mendaftar tryout";
            }

            return [
                'name' => $d->member->name,
                'action' => $status,
                'time' => $d->updated_at
            ];
        });
        /*
progress peserta
*/

      $progressParticipants = DetailEvent::with('member')
        ->where('event_id', $event_id)
        ->whereNotNull('start_at')
        ->whereNull('end_at')
        ->get()
        ->map(function ($p) {

            $answers = Answer::where('detail_event_id', $p->id)->get();

            $total = $answers->count();

            $answered = $answers->where('answer', '!=', 0)->count();

            $progress = $total > 0 ? round(($answered / $total) * 100) : 0;

            return [
                'name' => $p->member->name,
                'progress' => $progress
            ];
        })
        ->filter(fn($p) => $p['progress'] < 100)
        ->sortByDesc('progress')
        ->take(10)
        ->values();


        /*
distribusi nilai
*/

        $grades = DetailEvent::where('event_id', $event_id)
            ->whereNotNull('grade')
            ->pluck('grade');

        $distribution = [
            '0-40' => $grades->whereBetween(null, [0, 40])->count(),
            '40-60' => $grades->whereBetween(null, [41, 60])->count(),
            '60-80' => $grades->whereBetween(null, [61, 80])->count(),
            '80-100' => $grades->whereBetween(null, [81, 100])->count(),
        ];


        /*
average waktu pengerjaan
*/

        $avgTime = DetailEvent::where('event_id', $event_id)
            ->whereNotNull('end_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,start_at,end_at)) as avg'))
            ->first();


        /*
heatmap soal
*/

        $heatmap = $event->questions
        ->map(function ($q) {

            $total = Answer::where('question_id', $q->id)->count();

            $correct = Answer::where('question_id', $q->id)
                ->where('is_correct', 'Y')
                ->count();

            $rate = $total > 0 ? round(($correct / $total) * 100) : 0;

            return [
                'rate' => $rate
            ];

        })
        ->sortBy('rate')
        ->take(5)
        ->values()
        ->map(function($item,$index){

            $item['question'] = $index + 1;

            return $item;

        });


        return inertia('Public/Website/Events/Dashboard', [
            'stats' => [
                'total' => $total,
                'active' => $active,
                'finished' => $finished,
                'pending' => $pending
            ],
            'fastest' => $fastest,
            'topScores' => $topScores,
            'activities' => $activities,
            'progressParticipants' => $progressParticipants,
            'distribution' => $distribution,
            'avgTime' => $avgTime->avg,
            'heatmap' => $heatmap,
            'title' => 'Dashboard Event - ' . $event->title

        ]);
    }
}
