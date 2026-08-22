<?php

namespace App\Http\Controllers\Admin;

use Storage;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use App\Exports\KeuanganReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jurnals = Jurnal::
        when(request()->q, function($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })
        ->orderBy('nomor', 'asc')
        ->paginate(10);

        $jurnals->appends(['q' => request()->q]);

        return inertia('Admin/Jurnal/Index', [
            'jurnals' => $jurnals,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if the user is an administrator
        if (auth()->check() && (auth()->user()->role === 'administrator' || auth()->user()->role === 'pendanaan')) {
                // Validate request including file validation
      $request->validate([
        'title' => 'required|string',
        'nominal' => 'required|numeric',
        'type' => 'required',
        'date' => 'required|date',
        'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

        if ($request->hasFile('bukti')) {
            $bukti = $request->file('bukti')->storePublicly('/jurnal');
            } else {
            $bukti = null;
        }

        // Locked so two concurrent submissions can't read the same "last"
        // row and compute the same nomor/saldo - there's no unique
        // constraint on nomor to catch that, so it would fail silently.
        DB::transaction(function () use ($request, $bukti) {
            DB::table('jurnal_ledger_locks')->lockForUpdate()->first();

            $lastJurnal = Jurnal::orderBy('nomor', 'desc')->first();
            $saldo = $lastJurnal->saldo ?? 0;
            $nomor = $lastJurnal ? $lastJurnal->nomor + 1 : 1;

            Jurnal::create([
                'title' => $request->title,
                'nominal' => $request->nominal,
                'type' => $request->type,
                'saldo' => $request->type === 'kredit' ? $saldo - $request->nominal : $saldo + $request->nominal,
                'nomor' => $nomor,
                'coa' => $request->coa,
                'keterangan' => $request->keterangan,
                'kategori' => 'pusat',
                'date' => $request->date,
                'bukti' => $bukti,
            ]);
        });

     //redirect
     return redirect()->route('admin.jurnals.index')->with('success', 'Transaksi jurnal berhasil ditambahkan.');
        } else {
            return redirect()->route('admin.jurnals.index')->with('error', 'anda tidak memiliki akses ke halaman tersebut');
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
{
    // Ambil semua jurnal, urutkan berdasarkan tanggal
    $jurnals = Jurnal::when(request()->q, function($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })
        ->orderBy('date', 'asc')
        ->get();

    $saldo_akhir = 0;

    // Group berdasarkan bulan dan hitung total pemasukan, pengeluaran, saldo akhir
    $grouped = $jurnals->groupBy(function($item) {
        return \Carbon\Carbon::parse($item->date)->format('Y-m');
    })->map(function($items, $month) use (&$saldo_akhir) {
        $total_pemasukan = $items->where('type', 'debit')->sum('nominal');
        $total_pengeluaran = $items->where('type', 'kredit')->sum('nominal');

        // Hitung saldo akhir bulan ini
        $saldo_akhir += $total_pemasukan - $total_pengeluaran;

        return [
            'month' => $month,
            'total_pemasukan' => $total_pemasukan,
            'total_pengeluaran' => $total_pengeluaran,
            'saldo_akhir' => $saldo_akhir,
            'items' => $items->values(),
        ];
    })->values();

    return inertia('Admin/Jurnal/Show', [
        'jurnals' => $grouped,
    ]);
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {



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

        if (auth()->check() && (auth()->user()->role === 'administrator' || auth()->user()->role === 'pendanaan')) {
            $jurnal = Jurnal::findOrFail($id);
            // Validate request including file validation
            $request->validate([
            'title' => 'required|string',
            'nominal' => 'required|numeric',
            'type' => 'required',
            'date' => 'required|date',
            'bukti' => 'file|mimes:jpg,jpeg,png,pdf|max:2048|nullable',

            ]);

            // If a new file is uploaded, delete the old one
            if ($request->hasFile('bukti')) {
            if ($jurnal->bukti) {
                Storage::delete($jurnal->bukti);
            }
            $bukti = $request->file('bukti')->storePublicly('/jurnal');
            } else {
            $bukti = $jurnal->bukti;
            }

            // Locked so this recalculation can't interleave with a
            // concurrent store()/destroy() touching the same saldo chain.
            DB::transaction(function () use ($request, $jurnal, $bukti) {
                DB::table('jurnal_ledger_locks')->lockForUpdate()->first();

                $jurnal->update([
                'title' => $request->title,
                'nominal' => $request->nominal,
                'type' => $request->type,
                'coa' => $request->coa,
                'keterangan' => $request->keterangan,
                'kategori' => 'pusat',
                'bukti' => $bukti,
                'date' => $request->date,
                ]);

                // Recalculate saldo starting from the updated record
                $jurnals = Jurnal::where('nomor', '>=', $jurnal->nomor)->orderBy('nomor', 'asc')->get();
                $saldo = $jurnal->nomor > 1
                ? optional(Jurnal::where('nomor', '<', $jurnal->nomor)->orderBy('nomor', 'desc')->first())->saldo ?? 0
                : 0;

                foreach ($jurnals as $item) {
                $saldo = $item->type === 'kredit' ? $saldo - $item->nominal : $saldo + $item->nominal;
                $item->update(['saldo' => $saldo]);
                }
            });

            return redirect()->route('admin.jurnals.index')->with('success', 'Transaksi jurnal berhasil diperbarui.');
        } else {
            return redirect()->route('admin.jurnals.index')->with('error', 'anda tidak memiliki akses ke halaman tersebut');
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (auth()->check() && (auth()->user()->role === 'administrator' || auth()->user()->role === 'pendanaan')) {
            $jurnal = Jurnal::findOrFail($id);

            // Locked so this recalculation can't interleave with a
            // concurrent store()/update() touching the same saldo chain.
            DB::transaction(function () use ($jurnal) {
                DB::table('jurnal_ledger_locks')->lockForUpdate()->first();

                // Get the previous transaction's saldo
                $previousSaldo = $jurnal->nomor > 1
                    ? optional(Jurnal::where('nomor', '<', $jurnal->nomor)->orderBy('nomor', 'desc')->first())->saldo ?? 0
                    : 0;

                // Delete the current transaction
                $jurnal->delete();

                // Recalculate saldo for subsequent transactions
                $jurnals = Jurnal::where('nomor', '>', $jurnal->nomor)->orderBy('nomor', 'asc')->get();
                $saldo = $previousSaldo;

                foreach ($jurnals as $item) {
                    $saldo = $item->type === 'kredit' ? $saldo - $item->nominal : $saldo + $item->nominal;
                    $item->update(['saldo' => $saldo]);
                }
            });

        //redirect
        return redirect()->route('admin.jurnals.index')->with('success', 'Transaksi jurnal berhasil dihapus.');
        } else {
            return redirect()->route('admin.jurnals.index')->with('error', 'anda tidak memiliki akses ke halaman tersebut');
        }

    }

    public function exportReport()
    {
        // Check if the user is an administrator
        if (auth()->check() && (auth()->user()->role === 'administrator' || auth()->user()->role === 'pendanaan')) {
            $datas = Jurnal::orderBy('nomor', 'asc')->get();
            return Excel::download(new KeuanganReport($datas), 'laporan_kas.xlsx');
        } else {
            return redirect()->route('admin.jurnals.index')->with('error', 'anda tidak memiliki akses ke halaman tersebut');
        }

    }


}
