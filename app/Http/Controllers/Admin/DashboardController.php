<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\Event;
use App\Models\Merchan;
use App\Models\PublicPost;
use App\Models\Registration;
use Illuminate\Http\Request;
use App\Models\ProfileDataMain;
use Illuminate\Support\Facades\DB;
use App\Models\ProfileDataPosition;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $registrationData = [
            'total-registrasi' => Registration::count(),
            'telah-dilakukan-verifikasi' => Registration::where('emailstatus', '!=', 0)->count(),
            'upload-bukti-pembayaran' => Registration::whereNotNull('paid')->count(),
            'perbaikan' => Registration::where('status', 'confirm')->count(),
            'selesai' => Registration::where('status', 'approved')->count(),
            'ditolak' => Registration::where('status', 'rejected')->count(),

        ];

        $publicationData = [
            'publikasi' => Post::where('status', 'approved')->count(),
            'kegiatan' => Event::count(),
            'merchan' => Merchan::count(),
        ];

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

                // Mulai dari April 2024, bulan berikutnya mulai dari Januari
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


        return inertia('Admin/Dashboard/Index', [
            'registrationData' => $registrationData,
            'publicationData' => $publicationData,
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
}
