<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;

class EmailLogController extends Controller
{
    /**
     * Monitoring status pengiriman email (berhasil/gagal/pending) untuk
     * semua Mailable di aplikasi ini - approval/penolakan/konfirmasi
     * registrasi, permintaan pembayaran, lupa password, dan sertifikat.
     */
    public function index()
    {
        $emailLogs = EmailLog::when(request()->q, function ($query) {
                $q = request()->q;
                $query->where('to_email', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%");
            })
            ->when(request()->status, function ($query) {
                $query->where('status', request()->status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = EmailLog::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return inertia('Admin/EmailLogs/Index', [
            'emailLogs' => $emailLogs,
            'summary' => [
                'total' => (int) $summary->sum('total'),
                'sent' => (int) ($summary->get(EmailLog::STATUS_SENT)->total ?? 0),
                'pending' => (int) ($summary->get(EmailLog::STATUS_PENDING)->total ?? 0),
                'failed' => (int) ($summary->get(EmailLog::STATUS_FAILED)->total ?? 0),
            ],
        ]);
    }
}
