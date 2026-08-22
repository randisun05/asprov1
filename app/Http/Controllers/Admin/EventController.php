<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\Member;
use App\Models\EventPoint;
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
use F9WebLtd\QrCode\Facades\QrCode;

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
        ]);

        EventPoint::updateOrCreate(
            ['event_id' => $event->id],
            ['point_cost' => $request->point_cost ?? 0]
        );

     //redirect
     return redirect()->route('admin.events.index');
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
        //redirect
        return redirect()->route('admin.events.index');
    }

    public function absenAll($id)
    {
        $details = DetailEvent::where('event_id', $id)->get();

        foreach ($details as $detail) {
            $detail->update([
                'status' => 'hadir',
            ]);
        }

        return redirect()->route('admin.events.show', $id)->with('success', 'Data has been saved');
    }

    public function updateRole($id, Request $request)
    {

        $request->validate([
            'title' => 'required',
        ]);

        $detail = DetailEvent::where('id', $id)->first();

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

        //redirect
        return redirect()->route('admin.events.index');
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

    ]);

    $slug = strtolower(str_replace(' ', '-', $request->title));

    $image = $request->file('image');
    if ($image) {
        $image = $request->file('image')->storePublicly('/images');
        // Proceed with storing or processing the uploaded file
    } else {
        $image = Event::where('id', $id)->value('image');
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
     return redirect()->route('admin.events.index');
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

        $event->delete();

        //redirect
        return redirect()->route('admin.events.index');
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
     return redirect()->route('admin.events.index');
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
     return redirect()->route('admin.events.index');
    }


    public function exportParticipant($id)
    {
        $event = Event::findOrFail($id);

        $details = DetailEvent::where('event_id',$id)
        ->with('member.profileMain.position','event')
        ->when(request()->q, function($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
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
        $template = TemplateCertificate::where('id',$data->template)->first();
        $nomor = substr($data->no_certificate, 0, 4);
        $storagePath = storage_path('app/public/sertifikat');

         // Generate QR Code
        $qrLink = $data->qr_code;
        QrCode::format('png')->size(300)->generate($qrLink);
        // Generate QR Code (variable $qr removed as it was unused)
        QrCode::generate($qrLink);

         // Build the command
         $command = "python3 " . escapeshellarg(base_path('resources/py/certificate.py')) .
        // " " . escapeshellarg('template=' . 'storage/documents/' . $data->template) .
        " " . escapeshellarg(public_path('storage/' . $template->image)) .
         " " . escapeshellarg('nomor=' . $data->no_certificate) .
         " " . escapeshellarg('nama=' . $data->name) .
         " " . escapeshellarg('qr=' . $qrLink) .
         " " . escapeshellarg('file=' . 'sertifikat-' . $nomor . '-' . $data->name . '.pdf').
         " " . escapeshellarg('path=' . $storagePath);

         $output = shell_exec($command);


         if ($output === null) {
             return response()->json(['error' => 'Command execution failed.'], 500);
         }

        $data->update([
            'doc' => 'sertifikat/' . 'sertifikat-' . $nomor . '-' . $data->name . '.pdf'
        ]);

        return response()->download(public_path('storage/' . $data->doc), 'sertifikat-' . $nomor . '-' . $data->name . '.pdf')->deleteFileAfterSend(true);

        //  return response()->json(['success' => 'Certificate generated successfully.']);

         // Return success response
        //  return redirect()->route('admin.events.certificates.index', $event)->with('success', 'Sertifikat berhasil dihasilkan');

    }


    public function certificatesTemplateStore(Request $request)
    {

        $request->validate([
            'title' => 'required|unique:template_certificates,title',
            'image' => 'required',
        ]);

        $image = $request->file('image')->storePublicly('/template');

        TemplateCertificate::create([
            'title' => $request->title,
            'image' => $image,
            'status' => '1',
        ]);

        return redirect()->back();

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

        $template->delete();

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

        // Ambil nomor terakhir
        $lastCertificate = Certificate::where('category', $request->category)
            ->whereYear('date', date('Y', strtotime($request->date)))
            ->whereMonth('date', date('m', strtotime($request->date)))
            ->orderBy('created_at', 'desc')
            ->first();

        $lastNumber = $lastCertificate ? intval(explode('/', $lastCertificate->no_certificate)[0]) : 0;

        $errors = [];

        foreach ($filteredMembers as $member) {
            try {
                $attempts = 0;
                do {
                    $newNumber = str_pad(++$lastNumber, 4, '0', STR_PAD_LEFT);
                    $kodeKegiatan = $request->category;
                    $bulan = date('m', strtotime($request->date));
                    $tahun = date('Y', strtotime($request->date));
                    $nomor = "{$newNumber}/{$kodeKegiatan}/PP Aspro SDMA/{$bulan}/{$tahun}";
                    $attempts++;
                } while (
                    Certificate::where('no_certificate', $nomor)->exists()
                    && $attempts < 5
                );

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
                    'doc' => $member->member->agency,
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

        // 3. Ambil nomor terakhir dengan filter yang lebih stabil
        // Pastikan order by no_certificate atau ID untuk akurasi
        $lastCertificate = Certificate::whereYear('date', $tahun)
            ->where('category', $request->category)
            ->orderBy('no_certificate', 'desc') // Urutkan berdasarkan nomornya
            ->first();

        $lastNumber = 0;
        if ($lastCertificate) {
            // Pecah string nomor: "0002/KODE/..." ambil bagian indeks [0]
            $parts = explode('/', $lastCertificate->no_certificate);
            $lastNumber = intval($parts[0]);
        }

        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
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
            'doc'           => $request->agency,
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
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    $event = Event::findOrFail($id);
    $rows = Excel::toCollection(new CertificateImport, $request->file('file'))->first();

    if ($rows->isEmpty()) {
        return back()->withErrors(['file' => 'File Excel kosong atau tidak terbaca.']);
    }

    $excelNips = $rows->pluck('nip')->toArray();
    $existingCertificates = Certificate::where('category', 'Kombel')
        ->where('event_id', $event->id)
        ->whereIn('nip', $excelNips)
        ->pluck('nip')
        ->toArray();

    // PERBAIKAN 1: Gunakan penanggalan yang konsisten
    $targetDate = '2025-12-18';
    $month = date('m', strtotime($targetDate));
    $year = date('Y', strtotime($targetDate));

    // PERBAIKAN 2: Ambil nomor urut tertinggi saja (integer) agar lebih akurat
    $lastCertificate = Certificate::where('category', 'Kombel-Panitia')
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->where('no_certificate', 'like', "%/Kombel-Panitia/PP Aspro SDMA/{$month}/{$year}")
        ->get()
        ->map(function($cert) {
            return (int) explode('/', $cert->no_certificate)[0];
        })
        ->max(); // Ambil angka tertinggi

    $currentNumber = $lastCertificate ?? 0;
    $failedImports = [];

    foreach ($rows as $row) {
        if (in_array($row['nip'], $existingCertificates)) {
            $failedImports[] = "NIP {$row['nip']} sudah memiliki sertifikat.";
            continue;
        }

        try {
            // PERBAIKAN 3: Increment dilakukan di luar do-while agar angka selalu naik
            $currentNumber++;

            $attempts = 0;
            do {
                $newNumber = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
                $nomor = "{$newNumber}/kombel/PP Aspro SDMA/{$month}/{$year}";

                // Jika ternyata nomor ini sudah ada (karena input manual atau data lama), naikkan terus
                if (Certificate::where('no_certificate', $nomor)->exists()) {
                    $currentNumber++;
                    $attempts++;
                } else {
                    break;
                }
            } while ($attempts < 100);

            $link = (string) Str::uuid();

            Certificate::create([
                'event_id' => $event->id,
                'no_certificate' => $nomor,
                'category' => 'Kombel',
                'nip' => $row['nip'],
                'name' => $row['name'],
                'body' => $event->title,
                'date' => $targetDate,
                'template' => 'a0ab92e9-82d4-45c8-86fc-ddf5c9ce13d7', //9ea04e9f-0d35-49a0-b026-a507233139a4
                'status' => '1',
                'qr_code' => "https://asprosdma.id/certificates/$link",
                'link' => $link,
                'doc' => $row['instansi'],
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

        // 2. Cek apakah member sudah terdaftar di event ini
        $exists = DetailEvent::where('event_id', $id)
            ->where('member_id', $memberId)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Member ini sudah terdaftar di event tersebut.');
        }


        // 4. Daftarkan Member
        DetailEvent::create([
            'event_id'  => $id,
            'member_id' => $memberId,
            'title'     => $request->title ?? 'peserta', // Default title jika tidak disediakan
            'status'    => "approved", // Otomatis approved karena didaftarkan admin

        ]);

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
