<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\TemplateCertificate;
use F9WebLtd\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Shells out to resources/py/certificate.py to stamp a certificate PDF from
 * its template. Used by both the admin and public certificate-download
 * routes, which previously duplicated this exact command-building logic and
 * only checked shell_exec()'s return value for null - that only catches "the
 * shell couldn't be spawned", not "the Python script ran but failed" (a
 * missing/corrupt template, for example), so a bad template used to leave
 * the certificate's `doc` column pointing at a PDF that was never actually
 * created.
 */
class CertificateGenerator
{
    /**
     * @throws RuntimeException if the template is missing or generation fails
     */
    public function generate(Certificate $certificate): string
    {
        $template = TemplateCertificate::find($certificate->template);

        if (!$template || !$template->image || !Storage::exists($template->image)) {
            throw new RuntimeException('Template sertifikat tidak ditemukan atau sudah dihapus.');
        }

        $nomor = substr($certificate->no_certificate, 0, 4);
        $filename = 'sertifikat-' . $nomor . '-' . $this->sanitizeFilenamePart($certificate->name) . '.pdf';
        $storagePath = storage_path('app/public/sertifikat');
        $outputPath = $storagePath . '/' . $filename;

        QrCode::format('png')->size(300)->generate($certificate->qr_code);

        $command = 'python3 ' . escapeshellarg(base_path('resources/py/certificate.py')) .
            ' ' . escapeshellarg(storage_path('app/public/' . $template->image)) .
            ' ' . escapeshellarg('nomor=' . $certificate->no_certificate) .
            ' ' . escapeshellarg('nama=' . $certificate->name) .
            ' ' . escapeshellarg('qr=' . $certificate->qr_code) .
            ' ' . escapeshellarg('file=' . $filename) .
            ' ' . escapeshellarg('path=' . $storagePath) .
            ' 2>&1';

        exec($command, $outputLines, $exitCode);

        if ($exitCode !== 0 || !file_exists($outputPath)) {
            Log::error('Gagal generate sertifikat', [
                'certificate_id' => $certificate->id,
                'exit_code' => $exitCode,
                'output' => implode("\n", $outputLines),
            ]);

            throw new RuntimeException('Gagal menghasilkan file sertifikat. Silakan periksa template atau hubungi admin.');
        }

        $certificate->update(['doc' => 'sertifikat/' . $filename]);

        return $outputPath;
    }

    /**
     * A '/' in the certificate holder's name (e.g. dirty bulk-import data)
     * would otherwise let the Python script's os.path.join() write outside
     * the intended output directory.
     */
    private function sanitizeFilenamePart(string $value): string
    {
        return preg_replace('/[\/\\\\]+/', '-', $value);
    }
}
