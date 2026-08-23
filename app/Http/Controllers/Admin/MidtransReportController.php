<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MidtransTransactionExport;
use App\Http\Controllers\Controller;
use App\Models\MidtransTransaction;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MidtransReportController extends Controller
{
    /**
     * Laporan transparansi: anggota yang terdaftar beserta status/nominal
     * transaksi Midtrans mereka, supaya saldo yang masuk lewat payment
     * gateway bisa direkonsiliasi dan diaudit.
     */
    public function index()
    {
        $transactions = MidtransTransaction::with('registration')
            ->when(request()->q, function ($query) {
                $q = request()->q;
                $query->where('order_id', 'like', "%{$q}%")
                    ->orWhere('transaction_id', 'like', "%{$q}%")
                    ->orWhereHas('registration', function ($registration) use ($q) {
                        $registration->where('name', 'like', "%{$q}%")
                            ->orWhere('nip', 'like', "%{$q}%")
                            ->orWhere('agency', 'like', "%{$q}%");
                    });
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        // ->toBase(): Eloquent Collection overrides only()/except() to filter
        // by model primary key, not array key - keyBy('transaction_status')
        // only changes the array keys, so only() would otherwise silently
        // match nothing here and every summary figure below would read 0.
        $summary = MidtransTransaction::select('transaction_status', DB::raw('count(*) as total'), DB::raw('sum(gross_amount) as amount'))
            ->groupBy('transaction_status')
            ->get()
            ->keyBy('transaction_status')
            ->toBase();

        $settled = $summary->only(['settlement', 'capture']);

        return inertia('Admin/Registration/MidtransReport', [
            'transactions' => $transactions,
            'summary' => [
                'balance' => (int) $settled->sum('amount'),
                'settled_count' => (int) $settled->sum('total'),
                'pending_count' => (int) ($summary->get('pending')->total ?? 0),
                'failed_count' => (int) $summary->only(['deny', 'cancel', 'expire'])->sum('total'),
            ],
        ]);
    }

    public function export()
    {
        $transactions = MidtransTransaction::with('registration')->oldest('created_at')->get();

        return Excel::download(new MidtransTransactionExport($transactions), 'Laporan-Transaksi-Midtrans-' . now()->format('Y-m-d') . '.xlsx');
    }
}
