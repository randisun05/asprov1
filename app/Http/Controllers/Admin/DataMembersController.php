<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\instansi;
use App\Services\PointService;
use App\Exports\RecapExport;
use Illuminate\Http\Request;
use App\Exports\MemberExport;
use App\Models\ProfileDataMain;
use Illuminate\Support\Facades\DB;
use App\Models\ProfileDataPosition;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use F9WebLtd\QrCode\Facades\QrCode;



class DataMembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->role === 'keanggotaan' || auth()->user()->role === 'administrator' || auth()->user()->role === 'pendanaan') {
            $datas = ProfileDataPosition::with('main')
            ->when(request()->q, function($query) {
            $query->whereHas('main', function($query) {
                $query->where('name', 'like', '%' . request()->q . '%')
                      ->orWhere('agency', 'like', '%' . request()->q . '%')
                      ->orWhere('nip', 'like', '%' . request()->q . '%');
            });
            })
            ->latest()
            ->paginate(10);
            $datas->appends(['q' => request()->q]);


            return inertia('Admin/Members/Index', [
                'datas' => $datas,
             ]);
             } else {

            return redirect()->route('admin.dashboard')->with('error','anda tidak memiliki akses ke halaman tersebut');
        }

    }


    public function downloadMemberCard($id)
    {
            $id_user = ProfileDataMain::where('id', $id)->first();
            $profile = Member::where('nip', $id_user->nip)->first();
            $foto = $id_user;

            // Check if the member exists and has a qr_link
            if ($profile && $profile->qr_link) {
                $qrLink = "https://asprosdma.id/identity-verification/" . $profile->qr_link;
            } elseif ($profile && !$profile->qr_link) {
                // Generate a new qr_link if the member does not have one
                $link = (string) \Illuminate\Support\Str::uuid();
                $profile->qr_link = $link;
                $profile->save();
                $qrLink = "https://asprosdma.id/identity-verification/" . $link;
            } else {
                // Handle the case where the member or qr_link is not found
                return response('QR link not found', 404);
            }

            // Generate the QR code using the retrieved qr_link
            $qrCode = QrCode::size(250)->generate($qrLink);

            // Render the view with the profile and qrCode
            return view('Layouts/Components/MemberCard', compact('profile', 'qrCode', 'foto'));

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, PointService $pointService)
    {
        if (auth()->user()->role === 'keanggotaan' || auth()->user()->role === 'administrator' || auth()->user()->role === 'pendanaan') {
            $data = ProfileDataPosition::where('id',$id)->with('main')->first();

            $member = Member::where('nip', $data->main->nip)->first();
            $points = null;
            $pointTransactions = [];

            if ($member) {
                $points = $pointService->getBalance($member);
                $pointTransactions = $member->pointTransactions()->with('event', 'creator')->limit(20)->get();
            }

            return inertia('Admin/Members/Show', [
               'data' => $data,
               'memberId' => $member?->id,
               'points' => $points,
               'pointTransactions' => $pointTransactions,
            ]);
            } else {
            return redirect()->route('admin.dashboard')->with('error','anda tidak memiliki akses ke halaman tersebut');
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (auth()->user()->role === 'keanggotaan' || auth()->user()->role === 'administrator') {
            $data = ProfileDataPosition::where('id',$id)->with('main')->first();
            $instansis = instansi::get();
            return inertia('Admin/Members/Edit', [
            'data' => $data,
            'instansis' => $instansis,
            ]);
            } else {
            return redirect()->route('admin.dashboard')->with('error','anda tidak memiliki akses ke halaman tersebut');
        }

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
        if (auth()->user()->role === 'keanggotaan' || auth()->user()->role === 'administrator') {
        $request->validate([
            'nip' => ['required','string', 'regex:/^\d{18}$/'],
        ],[
            'nip.regex' => 'NIP harus terdiri dari 18 angka.',
        ]);
        $id = ProfileDataPosition::where('id', $id)
        ->first();
        $main = ProfileDataMain::where('id', $id->main_id)
        ->first();
        //update data main
        $main->update([
                'nip' => $request->nip,
                'name' => $request->name,
                'fname' => $request->fname,
                'lname' => $request->lname,
                'leveledu' => $request->leveledu,
                'lastedu' => $request->lastedu,
                'place' => $request->place,
                'dob' => $request->dob,
                'docid' => $request->docid,
                'nodocid' => $request->nodocid,
                'email' => $request->email,
                'contact' => $request->contact,
                'gender' => $request->gender,
                'religion' => $request->religion,
        ]);

        $position = ProfileDataPosition::where('main_id',$main->id)
        ->first();

        $position->update([
            'agency' => $request->agency,
            'type' => $request->type,
            'status' => $request->status,
            'unit' => $request->unit,
            'subunit' => $request->subunit,
            'position' => $request->position,
            'level' => $request->level,
            'location' => $request->location,
            'tmtpos' => $request->tmtpos,
            'golru' => $request->golru,
            'tmtgolru' => $request->tmtgolru,
            'wyear' => $request->wyear,
            'wmonth' => $request->wmonth,
         ]);
         return redirect()->route('admin.members.index')->with('success','data berhasil diupdate');
        } else {
            return redirect()->route('admin.dashboard')->with('error','anda tidak memiliki akses ke halaman tersebut');
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
        //
    }

    public function indexReport()
    {
        if (auth()->user()->role === 'keanggotaan' || auth()->user()->role === 'administrator') {

            $dataCountsByPosition = ProfileDataPosition::groupBy('position',)
            ->whereIn('position', [
                'Analis SDM Aparatur','Pranata SDM Aparatur',
            ])
            ->select('position', DB::raw('count(*) as total'))
            ->get()
            ->pluck('total', 'position');

            $dataCountsByLevel = ProfileDataPosition::groupBy('level')
            ->whereIn('level', [
                'Terampil','Mahir','Penyelia','Ahli Pertama','Ahli Muda','Ahli Madya','Ahli Utama'
            ])
            ->select('level', DB::raw('count(*) as total'))
            ->get()
            ->pluck('total', 'level')
            ->sortBy(function ($value, $key) {
                $order = [
                        'Terampil','Mahir','Penyelia','Ahli Pertama','Ahli Muda','Ahli Madya','Ahli Utama'
                    ];
                    return array_search($key, $order);
                });

            $countsPerMonth = [];
            $accumulatedCounts = [];
            $accumulatedCountsByPosition = [];
            $totalCount = 0;

            $startYear = 2024;
            $currentYear = date('Y');  // Tahun saat ini (misalnya: 2025)
            $currentMonth = date('n'); // Bulan saat ini (misalnya: 2 untuk Februari)

            for ($year = $startYear; $year <= $currentYear; $year++) {
                $endMonth = ($year == $currentYear) ? $currentMonth : 12;

                for ($month = 4; $month <= $endMonth; $month++) { // Start from April (4)
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
        'Kanreg I BKN', 'Kanreg II BKN', 'Kanreg III BKN', 'Kanreg IV BKN',
        'Kanreg V BKN', 'Kanreg VI BKN', 'Kanreg VII BKN', 'Kanreg VIII BKN',
        'Kanreg IX BKN', 'Kanreg X BKN', 'Kanreg XI BKN', 'Kanreg XII BKN',
        'Kanreg XIII BKN', 'Kanreg XIV BKN'
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


            return inertia('Admin/Members/Report', [
                'dataCountsByPosition' => $dataCountsByPosition,
                'dataCountsByLevel' => $dataCountsByLevel,
                'countsPerMonth' => $countsPerMonth,
                'accumulatedCounts' => $accumulatedCounts,
                'accumulatedCountsByPosition' => $accumulatedCountsByPosition,
                'dataCountsByGender' => $dataCountsByGender,
                'dataCountsByType' => $dataCountsByType,
                'dataCountsByRegion' => $dataCountsByRegion,
            ]);
            } else {

            return redirect()->route('admin.dashboard')->with('error','anda tidak memiliki akses ke halaman tersebut');
        }

    }

   public function exportReport()
{
    if (auth()->user()->role === 'keanggotaan' || auth()->user()->role === 'administrator') {

        $datas = ProfileDataPosition::with('main')
            // Tetap gunakan leftJoin agar data 1609 tidak berkurang
            ->leftJoin('instansis', 'profile_data_positions.agency', '=', 'instansis.title')
            ->select(
                'profile_data_positions.*',
                // Jika instansis.type NULL (tidak cocok), maka isi dengan 'Instansi Pusat'
                DB::raw("IFNULL(instansis.type, 'BKN Pusat') as agency_type")
            )
            ->get();

        return Excel::download(new MemberExport($datas), 'data_anggota.xlsx');

    } else {
        return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
    }
}

public function updateMissingGenders()
{
    // Ambil data yang kolom gender-nya masih kosong atau null
    $profiles = ProfileDatamain::whereNull('gender')
        ->orWhere('gender', '')
        ->get();

    $updatedCount = 0;
    $errorCount = 0;

    foreach ($profiles as $profile) {
        $nip = trim($profile->nip);

        // Logika ekstraksi NIP (Karakter ke-15)
        if (strlen($nip) >= 15) {
            $genderCode = substr($nip, 14, 1); // Indeks dimulai dari 0, maka 14 adalah karakter ke-15

            if ($genderCode === '1') {
                $profile->gender = 'L';
            } elseif ($genderCode === '2') {
                $profile->gender = 'P';
            } else {
                $profile->gender = 'L'; // Karakter ke-15 bukan 1 atau 2
            }
        } else {
            $profile->gender = 'L'; // NIP tidak valid/terlalu pendek
        }

        if ($profile->save()) {
            $updatedCount++;
        } else {
            $errorCount++;
        }
    }

    return response()->json([
        'message' => 'Proses update selesai',
        'berhasil_update' => $updatedCount,
        'gagal_atau_error' => $errorCount
    ]);
}

public function recapitulation()
{
    if (auth()->user()->role !== 'keanggotaan' && auth()->user()->role !== 'administrator') {
        return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
    }

    // 1. Ambil data dengan logika yang sama seperti indexReport
    $dataCountsByPosition = ProfileDataPosition::whereIn('position', ['Analis SDM Aparatur','Pranata SDM Aparatur'])
        ->select('position', DB::raw('count(*) as total'))
        ->groupBy('position')->pluck('total', 'position');

    $dataCountsByLevel = ProfileDataPosition::groupBy('level')
        ->select('level', DB::raw('count(*) as total'))->get()->pluck('total', 'level');

    $dataCountsByGender = ProfileDataMain::select('gender', DB::raw('count(*) as total'))
        ->groupBy('gender')->get()->mapWithKeys(function ($item) {
            $label = ['L' => 'Laki-laki', 'P' => 'Perempuan'];
            return [$label[$item->gender] ?? $item->gender => $item->total];
        });

    $dataCountsByType = DB::table('profile_data_positions')
        ->leftJoin('instansis', 'profile_data_positions.agency', '=', 'instansis.title')
        ->select(DB::raw("CASE
            WHEN instansis.type = 'BKN Pusat' OR instansis.type IS NULL THEN 'Instansi Pusat'
            ELSE 'Instansi Daerah'
        END as kategori"), DB::raw('count(*) as total'))
        ->groupBy('kategori')->pluck('total', 'kategori');

    $regionOrder = [
        'BKN Pusat',
        'Kanreg I BKN', 'Kanreg II BKN', 'Kanreg III BKN', 'Kanreg IV BKN',
        'Kanreg V BKN', 'Kanreg VI BKN', 'Kanreg VII BKN', 'Kanreg VIII BKN',
        'Kanreg IX BKN', 'Kanreg X BKN', 'Kanreg XI BKN', 'Kanreg XII BKN',
        'Kanreg XIII BKN', 'Kanreg XIV BKN'
    ];

    $dataCountsByRegion = DB::table('profile_data_positions')
        ->leftJoin('instansis', 'profile_data_positions.agency', '=', 'instansis.title')
        ->select(
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

    // 2. Bungkus dalam array untuk dikirim ke Export Class
    $data = [
        'dataCountsByPosition' => $dataCountsByPosition,
        'dataCountsByLevel'    => $dataCountsByLevel,
        'dataCountsByGender'   => $dataCountsByGender,
        'dataCountsByType'     => $dataCountsByType,
        'dataCountsByRegion'   => $dataCountsByRegion,
        'downloadDate'         => now()->format('d-m-Y H:i')
    ];

    // Susun data dalam Array Dua Dimensi
    $recapData = [
        ['REKAPITULASI DATA ANGGOTA'], // Baris 1
        ['Tanggal Unduh: ' . now()->format('d-m-Y H:i')], // Baris 2
        [''], // Baris Kosong (Harus tetap array)

        ['BERDASARKAN JABATAN', 'TOTAL'], // Header Seksi
    ];

    foreach ($dataCountsByPosition as $key => $val) {
        $recapData[] = [$key, (string)$val];
    }

    $recapData[] = ['']; // Pemisah
    $recapData[] = ['BERDASARKAN JENJANG', 'TOTAL'];
    foreach ($dataCountsByLevel as $key => $val) {
        $recapData[] = [$key, (string)$val];
    }

    $recapData[] = [''];
    $recapData[] = ['BERDASARKAN JENIS KELAMIN', 'TOTAL'];
    foreach ($dataCountsByGender as $key => $val) {
        $recapData[] = [$key, (string)$val];
    }

    $recapData[] = [''];
    $recapData[] = ['BERDASARKAN TIPE INSTANSI', 'TOTAL'];
    foreach ($dataCountsByType as $key => $val) {
        $recapData[] = [$key, (string)$val];
    }

    $recapData[] = [''];
    $recapData[] = ['PENYEBARAN WILAYAH', 'TOTAL'];
    foreach ($dataCountsByRegion as $key => $val) {
        $recapData[] = [$key, (string)$val];
    }

    // 3. Download menggunakan Maatwebsite Excel
    return Excel::download(new RecapExport($recapData), 'rekapitulasi_anggota.xlsx');
}
}
