<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MidtransTransactionExport implements FromCollection, WithMapping, WithHeadings
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->transactions;
    }

    public function map($transaction): array
    {
        return [
            $transaction->registration->name ?? '-',
            "'" . ($transaction->registration->nip ?? '-'),
            $transaction->registration->agency ?? '-',
            $transaction->order_id,
            $transaction->transaction_id,
            $transaction->gross_amount,
            $transaction->payment_type,
            $transaction->transaction_status,
            optional($transaction->transaction_time)->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIP',
            'Instansi',
            'Order ID',
            'Transaction ID',
            'Nominal',
            'Metode Bayar',
            'Status Transaksi',
            'Waktu Transaksi',
        ];
    }
}
