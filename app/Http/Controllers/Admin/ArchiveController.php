<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Archive;
use App\Models\instansi;
use Illuminate\Http\Request;
use App\Models\DetailArchive;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $archives = Archive::
    when(request()->q, function($query) {
        $query->where('title', 'like', '%' . request()->q . '%')
              ->orWhere('agency', 'like', '%' . request()->q . '%')
              ->orWhere('name', 'like', '%' . request()->q . '%');
    });
        // Simpan kondisi role dalam variabel agar lebih mudah dibaca
        $isNotAdminOrSekretariat = auth()->user()->role !== 'administrator' && auth()->user()->role !== 'sekretariat';
        // Terapkan filter user_id hanya jika bukan administrator atau sekretariat
        if ($isNotAdminOrSekretariat) {
            $archives->where('user_id', auth()->id());
        }
        $archives = $archives->select('archives.*',
                DB::raw("(SELECT GROUP_CONCAT(name SEPARATOR ', ')
                            FROM users
                            WHERE FIND_IN_SET(id, archives.dispositions)) as user_names")
            )
            ->latest()
            ->paginate(10);
        $archives->appends(['q' => request()->q]);

        return inertia('Admin/Archives/Index', [
            'archives' => $archives,
        ]);
    }

    public function inbox(Request $request)
    {

        $archives = DetailArchive::select('detail_archives.*',
        DB::raw("(SELECT GROUP_CONCAT(name SEPARATOR ', ')
                  FROM users
                  WHERE FIND_IN_SET(id, detail_archives.dispositions)) as user_names")
    )->
        with('archive', 'user')
        ->where('user_id', auth()->id())
        ->whereHas('archive', function($query) {
            $query->when(request()->q, function($query) {
                $query->where('title', 'like', '%' . request()->q . '%')
                      ->orWhere('agency', 'like', '%' . request()->q . '%')
                      ->orWhere('name', 'like', '%' . request()->q . '%');
            });
        })
        ->latest()
        ->paginate(10);

   $archives->appends(['q' => request()->q]);

        return inertia('Admin/Archives/Inbox', [
            'archives' => $archives,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $instansis = instansi::get();
        return inertia('Admin/Archives/Create', [
            'users' => $users,
            'instansis' => $instansis,
        ]);
    }

    public function dispo(Request $request, $id)
    {
        $archive = Archive::findOrFail($id);

        foreach ($request->user_id as $userId) {
            DetailArchive::create([
            'archive_id' => $archive->id,
            'user_id' => $userId,
            'noticket' => $archive->noticket,
            'title' => $archive->title,
            'detail' => '',
            'status' => '1',
            'disposition' => '',
            'action' => '',
            'reaction' => '',
            'isi' => implode(',', array_unique((array) $request->isi)),
            'document' => $archive->document,
            'from' => auth()->id(),
            ]);
        }

        $archive->update([
        'dispositions' => $archive->dispositions . ',' . implode(',', (array) $request->user_id),
        'status' => $archive->status == '1' ? '4' : $archive->status,
        ]);

        return redirect()->route('admin.archives.index')->with('success', 'Data berhasil didisposisikan');
    }

    public function dispoInbox(Request $request, $id)
    {
        $archive = DetailArchive::findOrFail($id);

        foreach ($request->user_id as $userId) {
            DetailArchive::create([
            'archive_id' => $archive->archive_id,
            'user_id' => $userId,
            'noticket' => $archive->noticket,
            'title' => $archive->title,
            'detail' => '',
            'status' => '1',
            'disposition' => '',
            'action' => '',
            'reaction' => '',
            'from' => auth()->id(),
            ]);
        }

        $archive->update([
            'dispositions' => $archive->dispositions . ',' . implode(',', $request->user_id),
            'status' => $archive->status == '1' ? '4' : $archive->status,
        ]);

        return redirect()->route('admin.archives.inbox')->with('success', 'Data berhasil didisposisikan');
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
      $validatedData = $request->validate([
        'nip' => ['required', 'string', 'regex:/^\d{18}$/'],
        'name' => 'required|string',
        'email' => 'required|email',
        'contact' => 'required|string',
        'agency' => 'required|string',
        'position' => 'required|string',
        'category' => 'required|string',
        'title' => 'required|string',
    ],
    [
        'nip.regex' => 'NIP harus terdiri dari 18 angka.',
        'nip.required' => 'NIP harus diisi.',
        'name.required' => 'Nama harus diisi.',
        'email.required' => 'Email harus diisi.',
        'contact.required' => 'Kontak harus diisi.',
        'agency.required' => 'Instansi harus diisi.',
        'position.required' => 'Jabatan harus diisi.',
        'title.required' => 'Topik pertanyaan harus diisi.',


    ]);

 // Store the file using Laravel's file storage system
     if ($request->hasFile('document')) {
        $document = $request->file('document')->storePublicly('/documents');
        } else {
        $document = null;
        }

    $lastMonth = Carbon::now()->format('ymd');
    do {
        $randomNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $tiket = $lastMonth . $randomNumber;
        $existingTicket = Archive::where('noticket', $tiket)->first();
    } while ($existingTicket);

    // Create registration
    $archive = Archive::create(array_merge($validatedData, ['status' => '1', 'noticket' => $tiket, 'document' => $document]));

     //redirect
     return redirect()->route('admin.archives.index')->with('success', 'Data berhasil dibuat');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $archive = Archive::select('archives.*',
        DB::raw("(SELECT GROUP_CONCAT(name SEPARATOR ', ')
                  FROM users
                  WHERE FIND_IN_SET(id, archives.dispositions)) as user_names")
    )->with('user')->findOrFail($id);

        $enrolled = DetailArchive::where('archive_id', $id)->pluck('user_id');
        $users = User::where('id', '!=', auth()->id())
        ->where('ref', 1)->where('position', '!=', 'anggota')
        ->whereNotIn('id', $enrolled)
        ->get();

        $datas = DetailArchive::select('detail_archives.*',
        DB::raw("(SELECT GROUP_CONCAT(name SEPARATOR ', ')
                  FROM users
                  WHERE FIND_IN_SET(id, detail_archives.dispositions)) as user_names")
    )
        ->with('user')
        ->where('archive_id', $id)->get();

        $refs = User::where('id', '!=', auth()->id())->where('role', '!=' ,'administrator')
        ->whereNotIn('id', $enrolled)
        ->get();

        return inertia('Admin/Archives/Show', [
            'archive' => $archive,
            'users' => $users,
            'datas' => $datas ? $datas : null,
            'refs' => $refs,
        ]);
    }

    public function showInbox($id)
    {
        $archive = DetailArchive::select('detail_archives.*',
        DB::raw("(SELECT GROUP_CONCAT(name SEPARATOR ', ')
                  FROM users
                  WHERE FIND_IN_SET(id, detail_archives.dispositions)) as user_names")
    )
        ->with('user','archive')
        ->where('user_id', auth()->id())
        ->findOrFail($id);

        $datas = DetailArchive::with('user')
        ->where('archive_id', $archive->archive_id)
        ->where('user_id', '!=', auth()->id())
        ->get();

        $enrolled = DetailArchive::where('archive_id', $id)->pluck('user_id');

         $users = User::where('id', '!=', auth()->id())
        ->where('role', auth()->user()->role)->where('position', 'anggota')
        ->where('ref', 2)
        ->whereNotIn('id', $enrolled)
        ->get();

        $refs = User::where('id', '!=', auth()->id())->where('role', '!=' ,'administrator')
        ->whereNotIn('id', $enrolled)
        ->get();

        return inertia('Admin/Archives/ShowInbox', [
            'archive' => $archive,
            'users' => $users,
            'datas' => $datas ? $datas : null,
            'refs' => $refs,
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
        $archive = Archive::with('user')->findOrFail($id);
        $users = User::where('id', '!=', auth()->id())->get();
        $datas = DetailArchive::where('user_id', auth()->id())->where('archive_id', $id)->get();
        return inertia('Admin/Archives/Edit', [
            'archive' => $archive,
            'users' => $users,
            'datas' => $datas ? $datas : null,
        ]);
    }

    public function editArchive($id)
    {

        $instansis = instansi::get();
        $archive = Archive::findOrFail($id);
        return inertia('Admin/Archives/EditArchive', [
            'archive' => $archive,
            'instansis' => $instansis,
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
      $validatedData = $request->validate([
        'nip' => ['required', 'string', 'regex:/^\d{18}$/'],
        'name' => 'required|string',
        'email' => 'required|email',
        'contact' => 'required|string',
        'agency' => 'required|string',
        'position' => 'required|string',
        'category' => 'required|string',
        'title' => 'required|string',
    ],
    [
        'nip.regex' => 'NIP harus terdiri dari 18 angka.',
        'nip.required' => 'NIP harus diisi.',
        'name.required' => 'Nama harus diisi.',
        'email.required' => 'Email harus diisi.',
        'contact.required' => 'Kontak harus diisi.',
        'agency.required' => 'Instansi harus diisi.',
        'position.required' => 'Jabatan harus diisi.',
        'title.required' => 'Topik pertanyaan harus diisi.',

    ]);

 // Update registration
    $archive = Archive::findOrFail($id);

    // Store the file using Laravel's file storage system; keep the
    // existing document when no new file is uploaded instead of wiping it.
     if ($request->hasFile('document')) {
        $document = $request->file('document')->storePublicly('/documents');
        } else {
        $document = $archive->document;
        }

    $archive->update(array_merge($validatedData, ['document' => $document]));

     //redirect
     return redirect()->route('admin.archives.index')->with('success', 'Data berhasil diupdate');
    }

    public function updateInbox(Request $request, $id)
    {
        $request->validate([
            'detail' => 'nullable|string',
            'status' => 'required',
        ]);

        $detailarchive = DetailArchive::findOrFail($id);
        $detailarchive->update([
            'detail' => $request->detail,
            'status' => $request->status,
        ]);

        $archive = Archive::findOrFail($detailarchive->archive_id);

        $archive->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.archives.inbox')->with('success', 'Data berhasil diupdate');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $archive = Archive::findOrFail($id);
        $archive->delete();

        return redirect()->route('admin.archives.index')->with('success', 'Data berhasil dihapus');
    }
}
