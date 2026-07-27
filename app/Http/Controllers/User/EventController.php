<?php

namespace App\Http\Controllers\User;

use App\Models\Event;
use App\Models\Member;
use Barryvdh\DomPDF\PDF;
use App\Models\Certificate;
use App\Models\DetailEvent;
use App\Mail\SendEmailEvent;
use Illuminate\Http\Request;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->guard('member')->check()) {
            $events = Event::whereNot('title', 'Media')
                ->when(request()->q, function ($query) {
                    $query->where('title', 'like', '%' . request()->q . '%');
                })
                ->latest()
                ->paginate(3);

            //append query string to pagination links
            $events->appends(['q' => request()->q]);

            return inertia('User/Events/Index', [
                'events' => $events
            ]);
        } else {
            return redirect()->route('login');
        }
    }

    public function join($id, Request $request)
    {

        if (auth()->guard('member')->check()) {
            $event = Event::findOrFail($id);

            if ($event->status !== 'active') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Pendaftaran untuk kegiatan ini sudah ditutup',
                    ], 403);
                }

                return redirect()->route('user.events.index')->with('error', 'Pendaftaran untuk kegiatan ini sudah ditutup.');
            }

            if ($event->file == "Y") {
                $request->validate([
                    'document' => 'required',
                ]);
                // Store the file using Laravel's file storage system
                $document = $request->file('document')->storePublicly('/documents');
            }


            $detailEvent = DetailEvent::firstOrCreate(
                [
                    'event_id' => $id,
                    'member_id' => auth()->guard('member')->user()->id,
                ],
                [
                    'title' => "peserta",
                    'status' => "approved",
                    'desc' => $document ?? null,
                    'duration' => $event->duration * 60000 ?? null,
                ]
            );

            if ($detailEvent->wasRecentlyCreated) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Berhasil join',
                        'detail_event_id' => $detailEvent->id
                    ]);
                }

                return redirect()->route('user.events.index');
            } else {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Sudah terdaftar',
                        'detail_event_id' => $detailEvent->id
                    ]);
                }

                return redirect()->route('user.events.index');
            }
        } else {
            //redirect
            return redirect()->route('user.events.index');
        }
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
    public function show(Event $event)
    {
        if (auth()->guard('member')->check()) {
            $memberId = auth()->guard('member')->user()->id;
            $status = $memberId ? 1 : 0;

            if ($memberId) {
                $detailEvent = DetailEvent::where('event_id', $event->id)->where('member_id', $memberId)->first();
                if ($detailEvent) {
                    // Jika detailEvent ditemukan, periksa statusnya
                    $status = 1; // Sudah terdaftar
                    $hadir = $detailEvent->status == 'hadir' ? 1 : 0; // Hadir atau tidak
                } else {
                    // Jika detailEvent tidak ditemukan, berarti belum terdaftar
                    $status = 0; // Belum terdaftar
                    $hadir = 0; // Tidak hadir
                }
            }

            return inertia('User/Events/Show', [
                'event' => $event,
                'status' => $status,
                'detailEvent' => $detailEvent,
                'hadir' => $hadir ?? 0,
            ]);
        } else {
            return redirect()->route('login');
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

    public function absen($id)
    {
        if (auth()->guard('member')->check()) {

            $event = Event::findOrFail($id);

            if ($event->absen !== 'Y') {
                return redirect()->route('user.events.index')->with('error', 'Absensi untuk kegiatan ini belum dibuka.');
            }

            $detailEvent = DetailEvent::where('event_id', $id)->where('member_id', auth()->guard('member')->user()->id)->first();
            if ($detailEvent) {
                $detailEvent->update([
                    'status' => 'hadir',
                ]);
            }
            return redirect()->route('user.events.index');
        } else {
            return redirect()->route('login');
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

    public function certificatesIndex()
    {
        if (auth()->guard('member')->check()) {
            $datas = Certificate::with('event')
                ->where('nip', auth()->guard('member')->user()->nip)
                ->when(request()->q, function ($query) {
                    $query->where('category', 'like', '%' . request()->q . '%')
                        ->orWhereHas('event', function ($eventQuery) {
                            $eventQuery->where('title', 'like', '%' . request()->q . '%');
                        });
                })
                ->latest()
                ->paginate(10);

            //append query string to pagination links
            $datas->appends(['q' => request()->q]);

            return inertia('User/Certificates/Index', [
                'datas' => $datas
            ]);
        } else {
            return redirect()->route('login');
        }
    }

    public function certificateView($id)
    {
        if (auth()->guard('member')->check()) {

            $data = Certificate::with('event')->findOrFail($id);
            // Generate QR Code
            $qrLink = $data->qr_code;
            QrCode::format('png')->size(300)->generate($qrLink);
            $qr = QrCode::generate($qrLink);
            return view('reports.certificates.certificate', compact('data', 'qr'));
        } else {
            return redirect()->route('login');
        }
    }

    public function info(Event $event)
    {
        if (auth()->guard('member')->check()) {

            return inertia('User/Events/Info', [
                'event' => $event,

            ]);
        } else {
            return redirect()->route('login');
        }
    }
}
