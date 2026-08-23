<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DocumentDigital;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use F9WebLtd\QrCode\Facades\QrCode;

class DocuDigiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $docus = DocumentDigital::when(request()->q, function($query) {
            $query->where('perihal', 'like', '%' . request()->q . '%');
        })
        ->where(function($query) {
            $query->where(function($query) {
            $query->where('nipttd', auth()->user()->nip)
                  ->where('status', 'paraf');
            })
            ->orWhere(function($query) {
            $query->where('nipparaf', auth()->user()->nip)
                  ->where('status', 'submitted');
            });
        })
        ->latest()
        ->paginate(10);
        $docus->appends(['q' => request()->q]);

        if (in_array(auth()->user()->role, ['administrator', 'sekretariat'])) {
            $docus = DocumentDigital::
            when(request()->q, function($query) {
                $query->where('perihal', 'like', '%' . request()->q . '%');
            })
            ->latest()
            ->paginate(10);
            $docus->appends(['q' => request()->q]);
        }

        return inertia('Admin/DocumentDigital/Index', [
            'docus' => $docus,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return inertia('Admin/DocumentDigital/Create', [

            ]);
    }

    public function paraf($id, Request $request)
    {
        $docu = DocumentDigital::findOrFail($id);

        $isTargetSigner = $docu->nipparaf && $docu->nipparaf === auth()->user()->nip;
        $isOverseer = in_array(auth()->user()->role, ['administrator', 'sekretariat'], true);

        if (!$isTargetSigner && !$isOverseer) {
            return redirect()->route('admin.docudigi.index')->with('error', 'Anda tidak memiliki akses untuk memparaf dokumen ini.');
        }

        $docu->update([
            'status' => "paraf",
        ]);

        return redirect()->route('admin.docudigi.index')->with('success', 'Dokumen berhasil diparaf.');
    }

    public function approve($id, Request $request)
    {
        $docu = DocumentDigital::findOrFail($id);

        $isTargetSigner = $docu->nipttd && $docu->nipttd === auth()->user()->nip;
        $isOverseer = in_array(auth()->user()->role, ['administrator', 'sekretariat'], true);

        if (!$isTargetSigner && !$isOverseer) {
            return redirect()->route('admin.docudigi.index')->with('error', 'Anda tidak memiliki akses untuk menandatangani dokumen ini.');
        }

        $fullPath = storage_path('app/public/' . $docu->document);
        $filename = Str::beforeLast(basename($docu->document), '.');

         // Generate QR Code
         $qrLink = "https://asprosdma.id/verification/" . $filename;
         $qrCodePath = storage_path('app/public/qrcode/' . uniqid() . '.png');
         $anchor = $docu->anchor;
         QrCode::format('png')->size(200)->generate($qrLink, $qrCodePath);
         // Add logo to QR Code
         //    QrCode::format('png')->size(200)->merge(public_path('assets/images/logo2.png'), 0.3)->generate($qrLink, $qrCodePath);
         $command = "python3 " . escapeshellarg(base_path('resources/py/find_text_position.py')) .
            " " . escapeshellarg($fullPath) .
            " " . escapeshellarg($qrCodePath) .
            " " . escapeshellarg($anchor);
             shell_exec($command);

        // find_text_position.py only writes the stamped _ttd.pdf when it
        // actually locates the anchor text; if it doesn't, marking the
        // document "approved" here would point it at a file that was
        // never created.
        $stampedPath = str_replace('.pdf', '_ttd.pdf', $fullPath);
        if (!file_exists($stampedPath)) {
            return redirect()->route('admin.docudigi.index')->with('error', 'Gagal menyisipkan QR Code: teks anchor tidak ditemukan pada dokumen.');
        }

            $docu->update([
                'status' => "approved",
                'qrcode' => $qrLink,
                'document' => str_replace('.pdf', '_ttd.pdf', $docu->document),
            ]);

            return redirect()->route('admin.docudigi.index')->with('success', 'Dokumen berhasil ditandatangani.');
    }

    public function cancel($id, Request $request)
    {
        $docu = DocumentDigital::findOrFail($id);

        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->route('admin.docudigi.index')->with('error', 'Password salah, dokumen tidak dibatalkan.');
        }

        $docu->update([
            'status' => "cancelled",
        ]);

        return redirect()->route('admin.docudigi.index')->with('success', 'Dokumen berhasil dibatalkan.');
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'perihal' => 'required',
            'speciment' => 'required',
            'nipttd' => 'required',
            'anchor' => 'required',
            'nipparaf' => 'nullable',
            'tujuan' => 'required',
            'jenis' => 'required',
            'document' => 'required|file|mimes:pdf|max:2048',
            'description' => 'required',
        ]);

        // Simpan dokumen asli
        $originalDocumentPath = $request->file('document')->store('documents');

        DocumentDigital::create([
            'perihal' => $request->perihal,
            'speciment' => $request->speciment,
            'nipttd' => $request->nipttd,
            'anchor' => $request->anchor,
            'nipparaf' => $request->nipparaf,
            'tujuan' => $request->tujuan,
            'jenis' => $request->jenis,
            'document' => $originalDocumentPath,
            'description' => $request->description,
            'status' => "submitted" ,
            'no_surat' => $request->no_surat,
            'kategori' => $request->kategori,
        ]);

        // return response()->json([
        //     'message' => 'Dokumen berhasil diproses',
        //     'anchor' => $request->anchor,

        // ]);

        return redirect()->route('admin.docudigi.index')->with('success', 'Dokumen berhasil diajukan.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $docu = DocumentDigital::findOrFail($id);
        return inertia('Admin/DocumentDigital/Edit', [
            'docu' => $docu,
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
        $docu = DocumentDigital::findOrFail($id);
        return inertia('Admin/DocumentDigital/Edit', [
            'docu' => $docu,
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
        $docu = DocumentDigital::findOrFail($id);
        $request->validate([
            'perihal' => 'required',
            'speciment' => 'required',
            'nipttd' => 'required',
            'anchor' => 'required',
            'nipparaf' => 'nullable',
            'tujuan' => 'required',
            'jenis' => 'required',
            'document' => 'nullable|file|mimes:pdf|max:2048',
            'description' => 'required',
        ]);

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents');
        } else {
            $documentPath = $docu->document;
        }

        $docu->update([
            'perihal' => $request->perihal,
            'speciment' => $request->speciment,
            'nipttd' => $request->nipttd,
            'anchor' => $request->anchor,
            'nipparaf' => $request->nipparaf,
            'tujuan' => $request->tujuan,
            'jenis' => $request->jenis,
            'document' => $documentPath,
            'description' => $request->description,
            'no_surat' => $request->no_surat,
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('admin.docudigi.index')->with('success', 'Dokumen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $docu = DocumentDigital::findOrFail($id);
        $docu->delete();
        return redirect()->route('admin.docudigi.index')->with('success', 'Dokumen berhasil dihapus.');
    }
}
