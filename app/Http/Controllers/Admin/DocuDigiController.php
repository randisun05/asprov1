<?php

namespace App\Http\Controllers\Admin;

use TCPDF;
use App\Models\Member;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\DocumentDigital;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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
        $user_id = auth()->user()->id;
        $user = Member::where('id',$user_id)->first();


            $docu->update([
                'status' => "paraf",
            ]);

            return redirect()->route('admin.docudigi.index');
    }

    public function approve($id, Request $request)
    {
        $docu = DocumentDigital::findOrFail($id);
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

        $user_id = auth()->user()->id;
        $user = Member::where('id',$user_id)->first();

            $docu->update([
                'status' => "approved",
                'qrcode' => $qrLink,
                'status' => "approved",
                'document' => str_replace('.pdf', '_ttd.pdf', $docu->document),
            ]);

            return redirect()->route('admin.docudigi.index');
    }

    public function cancel($id, Request $request)
    {
        $docu = DocumentDigital::findOrFail($id);
        $user_id = auth()->user()->id;
        $user = Member::where('id',$user_id)->first();

        if ($request->password === $user->password) {
            $docu->update([
                'status' => "cancelled",
            ]);
        }
            return redirect()->route('admin.docudigi.index');
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
            // 'perihal' => 'required',
            // 'speciment' => 'required',
            // 'nipttd' => 'required',
            // 'anchor' => 'required',
            // 'nipparaf' => '',
            // 'tujuan' => 'required',
            // 'jenis' => 'required',
            // 'document' => 'document',
            // 'description' => 'required',
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

        return redirect()->route('admin.docudigi.index');
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
        return inertia('Admin/DocumentDigital/Index', [
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
        return inertia('Admin/DocumentDigital/Index', [
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
            // 'perihal' => 'required',
            // 'speciment' => 'required',
            // 'nipttd' => 'required',
            // 'anchor' => 'required',
            // 'nipparaf' => 'required',
            // 'tujuan' => 'required',
            // 'jenis' => 'required',
            // 'document' => 'required',
            // 'description' => 'required',
        ]);
        $docu->update($request->all());
        return redirect()->route('admin.docudigi.index');
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
        return redirect()->route('admin.docudigi.index');
    }
}
