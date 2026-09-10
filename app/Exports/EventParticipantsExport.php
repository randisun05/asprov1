<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventParticipantsExport implements FromCollection, WithMapping, WithHeadings
{
    protected $datas;

    /**
     * __construct
     *
     * @param  mixed $grade
     * @return void
     */
    public function __construct($datas) {
        $this->datas = $datas;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->datas;
    }



    public function map($datas) : array {
        $selisih = $datas->start_at && $datas->end_at ? $datas->start_at->diffInSeconds($datas->end_at) * 60 : 0;
        return [
            "'" .$datas->member->nip,
            $this->escapeFormula($datas->member->name ?? '-'),
            $this->escapeFormula($datas->member->email ?? '-'),
            $this->escapeFormula($datas->member->agency ?? '-'),
            $this->escapeFormula($datas->member->profileMain->position->position ?? '-'),
            $this->escapeFormula($datas->member->profileMain->position->level ?? '-'),
            $datas->member->created_at,
            $datas->status,
            $datas->start_at,
            $datas->end_at,
            $selisih,
            $datas->grade,
        ] ;
    }

    /**
     * Nama/email/instansi berasal dari data profil anggota yang bisa diisi
     * bebas oleh anggota sendiri, jadi bisa dieksploitasi sama seperti pada
     * MidtransTransactionExport - cegah formula/CSV injection saat admin
     * membuka file export ini di Excel/LibreOffice.
     */
    private function escapeFormula(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    }

    public function headings() : array {
        return [
           'NIP',
           'Nama',
           'Email',
           'Instansi',
           'Jabatan',
           'Jenjang',
           'Tanggal',
           'Status',
              'Mulai',
                'Selesai',
                'Durasi',
                'Nilai',
        ] ;
    }
}
