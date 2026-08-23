<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Management;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->cekAuth();

        $datas = Management::when(request()->q, function ($query) {
            $query->where('item', 'like', '%' . request()->q . '%');
        })->latest()
            ->paginate(10);
        $datas->appends(['q' => request()->q]);

        return inertia('Admin/Management/Index', [
            'datas' => $datas,
        ]);
    }

    public function filter()
    {
        $this->cekAuth();
            $datas = Management::when(request()->q, function ($query) {
                $query->where('item', 'like', '%' . request()->q . '%');
            })->latest()
                ->paginate(10);
            $datas->appends(['q' => request()->q]);

            return inertia('Admin/Management/Index', [
                'datas' => $datas,

            ]);
    }

    public function cekAuth()
    {
        if (!auth()->check()) {
            auth()->logout(); // Log out the user programmatically
            return redirect()->route('login')->with('warning', 'Anda tidak memiliki akses');
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

        // Validate request including file validation
        $request->validate([
            'item' => 'required|string',
            'sub' => 'nullable|string',
            'subitem' => 'nullable|string',
            'status' => 'nullable',
            'position' => 'nullable',
            'button' => 'nullable',
            'link' => 'nullable|string',
            'desc' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'document' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $image = $request->file('image');
        if ($image) {
            $image = $request->file('image')->storePublicly('/images');
            // Proceed with storing or processing the uploaded file
        };

        $document = $request->file('document');
        if ($document) {
            $document = $request->file('document')->storePublicly('/document');
            // Proceed with storing or processing the uploaded file
        };


        Management::create([
            'item' => $request->item,
            'sub' => $request->sub ?? '-',
            'subitem' => $request->subitem ?? '-',
            'status' => $request->status ?? '1',
            'position' => $request->position ?? '1',
            'button' => $request->button ?? '0',
            'link' => $request->link ?? '-',
            'desc' => $request->desc ?? '-',
            'image' =>  $image ?? '-',
            'document' =>  $document ?? '-',
            'body' => $request->body ?? '-',

        ]);

        //redirect
        return redirect()->route('admin.management.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request)
    {

        $before = Management::findOrFail($request->id);

        // Validate request including file validation
        $request->validate([
            'item' => 'required|string',
            'sub' => 'nullable|string',
            'subitem' => 'nullable|string',
            'status' => 'nullable',
            'position' => 'nullable',
            'button' => 'nullable',
            'link' => 'nullable|string',
            'desc' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'document' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $image = $request->file('image');
        if ($image) {
            $image = $request->file('image')->storePublicly('/images');
            // Proceed with storing or processing the uploaded file
        };

        $document = $request->file('document');
        if ($document) {
            $document = $request->file('document')->storePublicly('/document');
            // Proceed with storing or processing the uploaded file
        };

        $before->update([
            'item' => $request->item ?? $before->item,
            'sub' => $request->sub ?? $before->sub,
            'subitem' => $request->subitem ?? $before->subitem,
            'status' => $request->status ?? $before->status,
            'position' => $request->position ?? $before->position,
            'button' => $request->button ?? $before->button,
            'link' => $request->link ?? $before->link,
            'desc' => $request->desc ?? $before->desc,
            'image' =>  $image ?? $before->image,
            'document' =>  $document ?? $before->document,
            'body' => $request->body ?? $before->body,
        ]);

        //redirect
        return redirect()->route('admin.management.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Management::findOrFail($id);

        $data->delete();

        //redirect
        return redirect()->route('admin.management.index')->with('success', 'Data berhasil dihapus');
    }

    public function status(Request $request)
    {
        $data = Management::findOrFail($request->id);

        $data->update([
            'status' => $data->status == '1' ? '0' : '1',
        ]);

        return redirect()->route('admin.management.index')->with('success', 'Status berhasil diperbarui');
    }
}
