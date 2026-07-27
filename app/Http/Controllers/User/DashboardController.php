<?php

namespace App\Http\Controllers\User;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Post;
use App\Models\Event;
use App\Models\Member;
use App\Models\Jurnal;
use Illuminate\Support\Str;
use App\Models\Merchan;
use App\Models\Achievement;
use App\Models\Certificate;
use App\Models\DetailEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Models\ProfileDataMain;
use App\Models\ProfileDataPosition;
use App\Http\Controllers\Controller;
use Barryvdh\Snappy\Facades\SnappyImage;
use F9WebLtd\QrCode\Facades\QrCode;

class DashboardController extends Controller
{
    public function __invoke()
    {

        if (!auth()->guard('member')->check())
        {
            return redirect()->route('login');
        }


        $main = ProfileDataMain::where('nip',auth()->guard('member')->user()->nip)
        ->first();

        $profile = ProfileDataPosition::with('main')->where('main_id',$main->id)->first();

        $user = Member::where('nip',$main->nip)
        ->first();

        $date = Member::where('nip', $main->nip)->first('created_at');

        $formattedDate = Carbon::parse($date->created_at)->format('d F Y');



        $events = Event::whereNot('title','media')->when(request()->q, function($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })
        ->latest()
        ->paginate(5);

        $merchans = Merchan::when(request()->q, function($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })
        ->latest()
        ->paginate(5);

        $posts = Post::with('member')->when(request()->q, function($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })
        ->where(function($query) {
            $query->where('status', 'approved')
                ->orWhere('status', 'limited');
        })
        ->latest()
        ->paginate(5);

        $events->appends(['q' => request()->q]);
        $merchans->appends(['q' => request()->q]);
        $posts->appends(['q' => request()->q]);

        $summary = [
            'eventsJoined' => DetailEvent::where('member_id', $user->id)->count(),
            'certificates' => Certificate::where('nip', $main->nip)->count(),
            'achievements' => Achievement::where('member_id', $user->id)->count(),
            'memberSince' => $formattedDate,
        ];

        $finance = $this->financeSummary();

        $foto = ProfileDataMain::where('nip', $main->nip)->first('image');

        if (!$user->qr_link) {
            $user->qr_link = (string) Str::uuid();
            $user->save();
        }
        $qrCode = QrCode::format('svg')->size(75)->generate(
            "https://asprosdma.id/identity-verification/" . $user->qr_link
        );

        return inertia('User/Dashboard/Index', [
            'profile' => $profile,
            'events' => $events,
            'merchans' => $merchans,
            'posts' => $posts,
            'user' => $user,
            'foto' => $foto,
            'qrCode' => (string) $qrCode,
            'formattedDate' => $formattedDate,
            'summary' => $summary,
            'finance' => $finance,
        ]);
    }

    /**
     * Organization-wide balance snapshot shown to every member for
     * financial transparency (mirrors the grouping in
     * Admin/User JurnalController::show but only needs the latest totals).
     */
    private function financeSummary(): array
    {
        $thisMonth = Jurnal::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->get();

        return [
            'saldoAkhir' => (int) Jurnal::orderBy('date', 'desc')->orderBy('id', 'desc')->value('saldo'),
            'pemasukanBulanIni' => (int) $thisMonth->where('type', 'debit')->sum('nominal'),
            'pengeluaranBulanIni' => (int) $thisMonth->where('type', 'kredit')->sum('nominal'),
        ];
    }

    public function print(Request $request)
    {
        $profile = $request->input('profile');

        // Render view ke HTML
        $html = view('member_card', compact('profile'))->render();

        // Konversi HTML menjadi gambar PNG
        $image = SnappyImage::loadHTML($html)->setOption('format', 'png')->inline();

        // Kembalikan respons dengan gambar PNG yang langsung diunduh
        return response($image, 200)->header('Content-Type', 'image/png');
    }
}
