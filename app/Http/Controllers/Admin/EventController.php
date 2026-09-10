<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\Member;
use App\Models\EventPoint;
use App\Models\MemberNotification;
use App\Models\Certificate;
use App\Models\DetailEvent;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\CertificateImport;
use App\Models\TemplateCertificate;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EventParticipantsExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\CertificateGenerator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::
             when(request()->q, function($query) {
                 $query->where('title', 'like', '%' . request()->q . '%');
             })
             ->latest()
             ->paginate(10);

        $events->appends(['q' => request()->q]);

        return inertia('Admin/Events/Index', [
            'events' => $events,
         ]);

     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $templates = TemplateCertificate::all();
        return inertia('Admin/Events/Create', [
            'templates' => $templates,
         ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Validate request including file validation
    $request->validate([
        'title' => 'required|string',
        'body' => 'required|',
        'date' => 'required',
        'participant' => 'required',
        'enddate' => 'required',
        'date' => 'required',
        'place' => 'required',
        'image' => '|image:allow_svg|mimes:jpeg,png,jpg,gif,svg|max:5048|required',
        'file' => 'required',
        'category' => 'required',
        'template' => 'required',
        'duration' => 'required|integer',
        'start_at' => 'required|date',
        'end_at' => 'required|date|after_or_equal:start_at',
        'point_cost' => 'nullable|integer|min:0',
    ]);

    $slug = strtolower(str_replace(' ', '-', $request->title));

    $image = $request->file('image');
    if ($image) {
        $image = $request->file('image')->storePublicly('/images');
        // Proceed with storing or processing the uploaded file
    };
        $event = Event::create([
            'title' => $request->title,
            'date' => $request->date,
            'participant' => $request->participant,
            'body' =>  $request->body,
            'slug' => $slug,
            'enddate' => $request->enddate,
            'image' => $image,
            'place' => $request->place,
            'link' => $request->link,
            'file' => $request->file,
            'category' => $request->category,
            'template_id' => $request->template,
            'duration' => $request->duration,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            // events.status is NOT NULL with no DB default, and this create()
            // never set it - every event creation was failing with a DB
            // integrity error before this fix. New events start 'active'
            // (open), matching the toggle() action's two valid states.
            'status' => 'active',
        ]);

        EventPoint::updateOrCreate(
            ['event_id' => $event->id],
            ['point_cost' => $request->point_cost ?? 0]
        );

        $isTryout = $event->category === 'Tryout';
        MemberNotification::broadcast(
            $isTryout ? 'tryout' : 'event',
            ($isTryout ? 'Tryout baru: ' : 'Kegiatan baru: ') . $event->title,
            $event->body,
            $isTryout ? '/user/tryouts' : "/user/events/{$event->slug}",
            $event
        );

     //redirect
     return redirect()->route('admin.events.index')->with('success', 'Event berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);

        $details = DetailEvent::where('event_id', $id)
        ->with('member', 'event')
        ->when(request()->q, function($query) {
            $query->whereHas('member', function($subQuery) {
            $subQuery->where('name', 'like', '%' . request()->q . '%');
            });
        })
        ->latest()
        ->paginate(10);

        $details->appends(['q' => request()->q]);

        return inertia('Admin/Events/Show', [
            'event' => $event,
            'details' => $details
        ]);
    }

    public function absenAll($id)
    {
        DetailEvent::where('event_id', $id)->update(['status' => 'hadir']);

        return redirect()->route('admin.events.show', $id)->with('success', 'Data has been saved');
    }

    public function updateRole($id, Request $request)
    {

        $request->validate([
            'title' => 'required',
        ]);

        $detail = DetailEvent::findOrFail($id);

        $detail->update([
            'title' => $request->title,
        ]);

        return redirect()->route('admin.events.show', $detail->event_id)->with('success', 'Data has been saved');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $event = Event::findOrFail($id);
         $templates = TemplateCertificate::all();
        return inertia('Admin/Events/Edit', [
            'event' => $event,
            'pointCost' => $event->point_cost,
            'templates' => $templates,
        ]);
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
        // Validate request including file validation
      $request->validate([
        'title' => 'required|string',
        'body' => 'required|',
        'date' => 'required',
        'participant' => 'required',
        'enddate' => 'required',
        'date' => 'required',
        'place' => 'required',
        'file' => 'required',
        'category' => 'required',
        'template' => 'required',
        'duration' => 'required|integer',
        'start_at' => 'required|date',
        'end_at' => 'required|date|after_or_equal:start_at',
        'point_cost' => 'nullable|integer|min:0',
        'image' => 'nullable|image:allow_svg|mimes:jpeg,png,jpg,gif,svg|max:5048',

    ]);

    $slug = strtolower(str_replace(' ', '-', $request->title));

    $oldImage = Event::where('id', $id)->value('image');

    $image = $request->file('image');
    if ($image) {
        $image = $request->file('image')->storePublicly('/images');
        // The old image is only replaced once the new one is safely
        // stored, and only deleted after that succeeds - so a failed
        // upload never leaves the event without any image on disk.
        if ($oldImage) {
            Storage::delete($oldImage);
        }
    } else {
        $image = $oldImage;
    };

        Event::where('id',$id)->update([
            'title' => $request->title,
            'date' => $request->date,
            'participant' => $request->participant,
            'body' =>  $request->body,
            'slug' => $slug,
            'enddate' => $request->enddate,
            'image' => $image,
            'place' => $request->place,
            'link' => $request->link,
            'file' => $request->file,
            'category' => $request->category,
            'template_id' => $request->template,
            'duration' => $request->duration,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
        ]);

        EventPoint::updateOrCreate(
            ['event_id' => $id],
            ['point_cost' => $request->point_cost ?? 0]
        );


     //redirect
     return redirect()->route('admin.events.index')->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        try {
            $event->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            // detail_events/certificates both restrict-on-delete this FK -
            // an event with participants or issued certificates can't be
            // deleted outright, and letting the constraint violation
            // bubble up as an uncaught 500 gives the admin no explanation.
            return redirect()->route('admin.events.index')->with('error', 'Kegiatan ini tidak bisa dihapus karena sudah memiliki peserta dan/atau sertifikat.');
        }

        if ($event->image) {
            Storage::delete($event->image);
        }

        //redirect
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }

    public function change($id)
    {

        $status = Event::where('id', $id)->value('status');

        if ($status == 'active') {
            Event::where('id',$id)->update([
                'status' => "closed",
            ]);
        } else {
            Event::where('id',$id)->update([
                'status' => "active",
            ]);
        }

     //redirect
     return redirect()->route('admin.events.index')->with('success', 'Status event berhasil diubah.');
    }

    public function absen($id)
    {

        $status = Event::where('id', $id)->value('absen');

        if ($status == 'N') {
            Event::where('id',$id)->update([
                'absen' => "Y",
            ]);
        } else {
            Event::where('id',$id)->update([
                'absen' => "N",
            ]);
        }

     //redirect
     return redirect()->route('admin.events.index')->with('success', 'Status absen berhasil diubah.');
    }


    public function exportParticipant($id)
    {
        $event = Event::findOrFail($id);

        $details = DetailEvent::where('event_id',$id)
        ->with('member.profileMain.position','event')
        ->when(request()->q, function($query) {
            // Was filtering on DetailEvent.title (the participant *role*
            // label, e.g. "peserta" for everyone) instead of the member's
            // name, so the search box on this export was a silent no-op.
            $query->whereHas('member', function ($memberQuery) {
                $memberQuery->where('name', 'like', '%' . request()->q . '%');
            });
        })
        ->latest()
        ->get();

        return Excel::download(new EventParticipantsExport($details), 'participants.xlsx');

    }

    public function certificatesIndex($id)
{
    $event = Event::findOrFail($id);

    // Gunakan pagination untuk members jika datanya banyak,
    // atau tetap all() jika memang dibutuhkan untuk dropdown
    $members = Member::all();

    $datas = Certificate::where('event_id', $id) // Filter utama tetap terjaga
        ->when(request()->q, function($query) {
            // Bungkus pencarian dalam closure agar menjadi: WHERE event_id = ? AND (nip LIKE ? OR name LIKE ? OR ...)
            $query->where(function($q) {
                $search = request()->q;
                $q->where('nip', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('no_certificate', 'like', '%' . $search . '%');
            });
        })
        ->orderBy('no_certificate', 'desc')
        ->paginate(10)
        ->withQueryString(); // Lebih praktis daripada manual ->appends()

    return inertia('Admin/Events/CertificatesIndex', [
        'event'   => $event,
        'members' => $members,
        'datas'   => $datas,
    ]);
}

    public function certificatesCreate($id)
    {
        $event = Event::findOrFail($id);
        $members = Member::all();
        $templates = TemplateCertificate::all();
        return inertia('Admin/Events/Certificates', [
            'event' => $event,
            'members' => $members,
            'templates' => $templates,
         ]);
    }

    public function certificatesView($event, $id)
    {
        $data = Certificate::with('event')->findOrFail($id);
        $nomor = substr($data->no_certificate, 0, 4);

        try {
            $path = app(CertificateGenerator::class)->generate($data);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return response()->download($path, 'sertifikat-' . $nomor . '-' . $data->name . '.pdf')->deleteFileAfterSend(true);
    }


    public function certificatesTemplateStore(Request $request)
    {

        $request->validate([
            'title' => 'required|unique:template_certificates,title',
            // The certificate-generation script opens this file as a PDF
            // (PyMuPDF/fitz), so anything else uploaded here would fail
            // silently later at generation time instead of at upload time.
            'image' => 'required|mimes:pdf|max:10240',
        ]);

        $image = $request->file('image')->storePublicly('/template');

        TemplateCertificate::create([
            'title' => $request->title,
            'image' => $image,
            'status' => '1',
        ]);

        return redirect()->back()->with('success', 'Template sertifikat berhasil ditambahkan.');

    }

    public function certificatesTemplate(Request $request)
    {

        $templates = TemplateCertificate::latest()->paginate(10);

        return inertia('Admin/Events/Templates', [
            'templates' => $templates,
            'event_id' => $request->event_id,
        ]);
    }

    public function certificatesTemplateDelete($id)
    {
        $template = TemplateCertificate::findOrFail($id);

        // events.template_id and certificates.template both just store this
        // id as a plain string with no FK constraint, so nothing at the DB
        // level stops this template from being deleted while still
        // referenced - it would only surface later, as a generation
        // failure, when someone tries to view/download one of those
        // certificates.
        $inUse = Event::where('template_id', $id)->exists() || Certificate::where('template', $id)->exists();

        if ($inUse) {
            return redirect()->back()->with('error', 'Template ini masih digunakan oleh kegiatan atau sertifikat yang sudah terbit, sehingga tidak bisa dihapus.');
        }

        $template->delete();

        if ($template->image) {
            Storage::delete($template->image);
        }

        return redirect()->back()->with('success', 'Data has been deleted');
    }

    public function certificatesImport($id)
    {

        $event = Event::findOrFail($id);
        return inertia('Admin/Events/CertificatesImport', [
            'event' => $event,
         ]);
    }

    public function certificatesImportCreate($id)
    {
        $event = Event::findOrFail($id);
        $templates = TemplateCertificate::all();
        $existingCertificateNips = Certificate::where('event_id', $id)
        ->pluck('nip')
        ->toArray();

        $users = DetailEvent::with('member')->where('event_id', $id)
        ->where('status','hadir')
        ->whereHas('member', function($query) use ($existingCertificateNips) {
            $query->whereNotIn('nip', $existingCertificateNips);
        })->when(request()->q, function($query) {
            $query->whereHas('member', function($subQuery) {
            $subQuery->where('name', 'like', '%' . request()->q . '%')
                 ->orWhere('nip', 'like', '%' . request()->q . '%');
            });
        })
        ->latest()
        ->paginate(20);

        $users->appends(['q' => request()->q]);

        return inertia('Admin/Events/CertificatesImport', [
            'event' => $event,
            'templates' => $templates,
            'users' => $users,
         ]);
    }

    public function certificatesImportStore($id, Request $request)
    {
        $request->validate([
            'category' => 'required',
            'date' => 'required|date',
            'template' => 'required',
        ]);

        $event = Event::findOrFail($id);
        $ids = is_array($request->user_id)
            ? array_map('trim', $request->user_id)
            : array_map('trim', explode(',', $request->user_id));

        $members = DetailEvent::with('member')
            ->where('event_id', $id)
            ->whereIn('member_id', $ids)
            ->get();

        if ($members->isEmpty()) {
            return response()->json(['message' => 'Tidak ada anggota yang ditemukan.'], 404);
        }

        // Cek yang sudah punya sertifikat
        $existingCertificates = Certificate::where('category', $request->category)
            ->where('event_id', $event->id)
            ->whereIn('nip', $members->pluck('member.nip'))
            ->pluck('nip')
            ->toArray();

        $filteredMembers = $members->filter(function ($member) use ($existingCertificates) {
            return !in_array($member->member->nip, $existingCertificates);
        });

        if ($filteredMembers->isEmpty()) {
            return response()->json(['message' => 'Semua anggota sudah memiliki sertifikat.'], 409);
        }

        $bulan = date('m', strtotime($request->date));
        $tahun = date('Y', strtotime($request->date));
        $sequenceKey = "import:{$request->category}:{$tahun}:{$bulan}";

        $errors = [];

        foreach ($filteredMembers as $member) {
            try {
                $nextNumber = $this->lockedNextCertificateNumber($sequenceKey, function () use ($request, $tahun, $bulan) {
                    $lastCertificate = Certificate::where('category', $request->category)
                        ->whereYear('date', $tahun)
                        ->whereMonth('date', $bulan)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    return $lastCertificate ? intval(explode('/', $lastCertificate->no_certificate)[0]) : 0;
                });

                $newNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                $kodeKegiatan = $request->category;
                $nomor = "{$newNumber}/{$kodeKegiatan}/PP Aspro SDMA/{$bulan}/{$tahun}";

                if (Certificate::where('no_certificate', $nomor)->exists()) {
                    $errors[] = [
                        'nip' => $member->member->nip,
                        'name' => $member->member->name,
                        'error' => "Nomor sertifikat {$nomor} sudah digunakan.",
                    ];
                    continue;
                }

                $link = (string) Str::uuid();
                $qrcode = "https://asprosdma.id/certificates/$link";

                Certificate::create([
                    'event_id' => $event->id,
                    'no_certificate' => $nomor,
                    'category' => $request->category,
                    'nip' => $member->member->nip,
                    'name' => $member->member->name,
                    'body' => $event->title, // Ganti jika ingin dinamis
                    'date' => $request->date,
                    'template' => $request->template,
                    'status' => '1',
                    'qr_code' => $qrcode,
                    'link' => $link,
                    // Generated on demand later (see certificatesView()),
                    // not at creation time - this was previously set to
                    // the member's agency name instead of being left empty.
                    'doc' => '',
                ]);
            } catch (\Exception $e) {
                $errors[] = [
                    'nip' => $member->member->nip,
                    'name' => $member->member->name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        if (count($errors)) {
            return redirect()->route('admin.events.certificates.index', $id)
                ->with('warning', 'Sebagian sertifikat gagal disimpan.')
                ->with('errors', $errors);
        }

        return redirect()->route('admin.events.certificates.index', $id)
            ->with('success', 'Semua sertifikat berhasil disimpan.');
    }


    /**
     * Draw the next certificate number for a numbering scope, locked by
     * exact key match instead of the previous pattern (read the current
     * max no_certificate, add one, no lock) that let two concurrent
     * certificate creations for the same category/period silently produce
     * the same number - no_certificate has no unique constraint to catch
     * that after the fact, unlike member numbering.
     *
     * $seed is only invoked the first time this key is used - it should
     * replicate whatever "find the current max" query this scope used
     * before, so numbering continues from real historical data instead of
     * restarting at 1 and colliding with already-issued numbers.
     */
    private function lockedNextCertificateNumber(string $key, \Closure $seed): int
    {
        return DB::transaction(function () use ($key, $seed) {
            if (!DB::table('certificate_number_sequences')->where('key', $key)->exists()) {
                DB::table('certificate_number_sequences')->insertOrIgnore([
                    'key' => $key,
                    'last_number' => $seed(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $sequence = DB::table('certificate_number_sequences')
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            $nextNumber = $sequence->last_number + 1;

            DB::table('certificate_number_sequences')
                ->where('key', $key)
                ->update(['last_number' => $nextNumber, 'updated_at' => now()]);

            return $nextNumber;
        });
    }

    public function certificatesStore($id, Request $request)
{
    $event = Event::findOrFail($id);

    // 1. Validasi dilakukan di paling AWAL
    $request->validate([
        'category' => 'required',
        'nip' => 'required',
        'name' => 'required',
        'date' => 'required|date',
        'template' => 'required',
    ]);

    // 2. Gunakan DB Transaction agar nomor tidak lompat jika terjadi error
    return \DB::transaction(function () use ($id, $event, $request) {

        $dateObj = strtotime($request->date);
        $bulan = date('m', $dateObj);
        $tahun = date('Y', $dateObj);

        $nextNumber = $this->lockedNextCertificateNumber(
            "single:{$request->category}:{$tahun}",
            function () use ($request, $tahun) {
                $lastCertificate = Certificate::whereYear('date', $tahun)
                    ->where('category', $request->category)
                    ->orderBy('no_certificate', 'desc')
                    ->first();

                return $lastCertificate ? intval(explode('/', $lastCertificate->no_certificate)[0]) : 0;
            }
        );

        $newNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $nomor = "{$newNumber}/{$request->category}/PP Aspro SDMA/{$bulan}/{$tahun}";

        $link = (string) \Illuminate\Support\Str::uuid();
        $qrcode = "https://asprosdma.id/certificates/$link";

        Certificate::create([
            'event_id'      => $event->id,
            'no_certificate'=> $nomor,
            'category'      => $request->category,
            'nip'           => $request->nip,
            'name'          => $request->name,
            'body'          => 'template',
            'date'          => $request->date,
            'template'      => $request->template,
            'status'        => '1',
            'qr_code'       => $qrcode,
            'link'          => $link,
            // The PDF itself is generated on demand the first time it's
            // viewed/downloaded (see certificatesView()), not at creation
            // time - "doc" was previously mistakenly set to the requester's
            // agency name here instead of being left empty until then.
            'doc'           => '',
        ]);

        return redirect()->route('admin.events.certificates.index', $id)
            ->with('success', "Data has been saved with certificate number: $nomor");
    });
}

    public function certificatesDestroy($id, $certificate)
    {
        $certificate = Certificate::findOrFail($certificate);
        $certificate->delete();
        return redirect()->back()->with('success', 'Data has been deleted');
    }

     public function certificatesExcelIndex($id)
    {
        $event = Event::findOrFail($id);
        $templates = TemplateCertificate::all();
        return inertia('Admin/Events/CertificateExcel', [
            'event' => $event,
            'templates' => $templates
         ]);
    }


    public function certificatesExcelStore(Request $request, $id)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv',
        'category' => 'required|string',
        'date' => 'required|date',
        'template' => 'required',
    ]);

    $event = Event::findOrFail($id);
    $rows = Excel::toCollection(new CertificateImport, $request->file('file'))->first();

    if ($rows->isEmpty()) {
        return back()->withErrors(['file' => 'File Excel kosong atau tidak terbaca.']);
    }

    // Previously hardcoded to one specific historical event (a fixed date,
    // a "Kombel" category that didn't even match the "Kombel-Panitia"
    // string the numbering sequence below was querying by, and a fixed
    // template UUID) - this form's date/category/template inputs were
    // being ignored entirely, so importing for any other event silently
    // produced wrong certificate numbers and the wrong template.
    $category = $request->category;
    $targetDate = $request->date;
    $month = date('m', strtotime($targetDate));
    $year = date('Y', strtotime($targetDate));

    $excelNips = $rows->pluck('nip')->toArray();
    $existingCertificates = Certificate::where('category', $category)
        ->where('event_id', $event->id)
        ->whereIn('nip', $excelNips)
        ->pluck('nip')
        ->toArray();

    $sequenceKey = "excel:{$category}:{$year}:{$month}";
    $failedImports = [];

    foreach ($rows as $row) {
        if (empty($row['nip']) || empty($row['name'])) {
            $failedImports[] = 'Baris dengan NIP "' . ($row['nip'] ?? '-') . '" dilewati karena NIP atau nama kosong.';
            continue;
        }

        if (in_array($row['nip'], $existingCertificates)) {
            $failedImports[] = "NIP {$row['nip']} sudah memiliki sertifikat.";
            continue;
        }

        try {
            $nextNumber = $this->lockedNextCertificateNumber($sequenceKey, function () use ($category, $year, $month) {
                return Certificate::where('category', $category)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->where('no_certificate', 'like', "%/{$category}/PP Aspro SDMA/{$month}/{$year}")
                    ->get()
                    ->map(fn ($cert) => (int) explode('/', $cert->no_certificate)[0])
                    ->max() ?? 0;
            });

            $newNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $nomor = "{$newNumber}/{$category}/PP Aspro SDMA/{$month}/{$year}";

            if (Certificate::where('no_certificate', $nomor)->exists()) {
                $failedImports[] = "Nomor sertifikat {$nomor} sudah digunakan.";
                continue;
            }

            $link = (string) Str::uuid();

            Certificate::create([
                'event_id' => $event->id,
                'no_certificate' => $nomor,
                'category' => $category,
                'nip' => $row['nip'],
                'name' => $row['name'],
                'body' => $event->title,
                'date' => $targetDate,
                'template' => $request->template,
                'status' => '1',
                'qr_code' => "https://asprosdma.id/certificates/$link",
                'link' => $link,
                // Generated on demand later (see certificatesView()), not
                // at creation time - this was previously set to the row's
                // "instansi" column instead of being left empty.
                'doc' => '',
            ]);

        } catch (\Exception $e) {
            $failedImports[] = "NIP {$row['nip']}: " . $e->getMessage();
        }
    }

    if (count($failedImports) > 0) {
        return redirect()->back()->withErrors(['import_failed' => $failedImports]);
    }

    return redirect()->route('admin.events.certificates.index', $id);
}

public function enrollMember(Request $request, $id)
    {
        // 1. Validasi: pastikan member_id ada dan valid
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'title' => 'required',
        ]);

        $event = Event::findOrFail($id);
        $memberId = $request->member_id;

        try {
            $detail = \DB::transaction(function () use ($id, $memberId, $request) {
                // Lock so a double-submitted "daftarkan member" (double click,
                // retried request) can't create two detail_events rows for
                // the same event+member.
                $existing = DetailEvent::where('event_id', $id)
                    ->where('member_id', $memberId)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return $existing;
                }

                return DetailEvent::create([
                    'event_id'  => $id,
                    'member_id' => $memberId,
                    'title'     => $request->title ?? 'peserta', // Default title jika tidak disediakan
                    'status'    => "approved", // Otomatis approved karena didaftarkan admin
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // race slipped past the lock; the unique constraint on
            // (event_id, member_id) still caught it
            return redirect()->back()->with('error', 'Member ini sudah terdaftar di event tersebut.');
        }

        if (!$detail->wasRecentlyCreated) {
            return redirect()->back()->with('error', 'Member ini sudah terdaftar di event tersebut.');
        }

        return redirect()->back()->with('success', 'Berhasil menambahkan peserta ke event.');
    }

    /**
     * Show the question-bank picker for a Tryout/Event: pick a Kelompok Soal,
     * then choose which of its questions this event uses.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function questionBank($id)
    {
        $event = Event::findOrFail($id);
        $categories = QuestionCategory::withCount('questions')->get();

        $selectedCategoryId = request()->category_id;
        $questions = collect();

        if ($selectedCategoryId) {
            $attachedIds = $event->questions()
                ->where('question_category_id', $selectedCategoryId)
                ->pluck('questions.id')
                ->toArray();

            $questions = Question::where('question_category_id', $selectedCategoryId)
                ->get()
                ->map(function ($question) use ($attachedIds) {
                    $question->attached = in_array($question->id, $attachedIds);
                    return $question;
                });
        }

        return inertia('Admin/Events/Questions', [
            'event' => $event,
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId ? (int) $selectedCategoryId : null,
            'questions' => $questions,
            'attachedTotal' => $event->questions()->count(),
        ]);
    }

    /**
     * Attach/detach the chosen questions of one Kelompok Soal to this event.
     * Only the selected category's questions are touched; other categories'
     * selections for this event are left as-is.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncQuestions(Request $request, $id)
    {
        $request->validate([
            'question_category_id' => 'required|exists:question_categories,id',
            'question_ids' => 'array',
            'question_ids.*' => 'integer|exists:questions,id',
        ]);

        $event = Event::findOrFail($id);

        $categoryQuestionIds = Question::where('question_category_id', $request->question_category_id)->pluck('id');
        $event->questions()->detach($categoryQuestionIds);
        $event->questions()->attach($request->question_ids ?? []);

        return redirect()->route('admin.events.questions', [$id, 'category_id' => $request->question_category_id])
            ->with('success', 'Soal untuk tryout ini berhasil diperbarui');
    }

    // Tambahkan di controller yang menangani Event
public function findMemberByNip($nip)
{
    $member = Member::where('nip', $nip)->first();

    if ($member) {
        return response()->json([
            'success' => true,
            'data'    => $member
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Member tidak ditemukan'
    ]);
}

}
