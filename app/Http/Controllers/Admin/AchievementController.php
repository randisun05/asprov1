<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Achievement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $datas = Achievement::
             when(request()->q, function($query) {
                 $query->where('title', 'like', '%' . request()->q . '%');
             })
             ->with('member')
             ->latest()
             ->paginate(10);

        $datas->appends(['q' => request()->q]);

        return inertia('Admin/Achievements/Index', [
            'datas' => $datas,
         ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
            $members = Member::all();
            return inertia('Admin/Achievements/Create', [
                'members' => $members,
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

        $request->validate([
            'nip' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'date' => 'required|date',
            'image' => '',
            'document' => '',
            'icon' => 'required|string|max:255',
        ]);

        $memberId = Member::where('nip', $request->nip)->first()->id;

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

        Achievement::create([
            'member_id' => $memberId,
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'category' => $request->category,
            'image' => $image,
            'document' => $document,
            'icon' => $request->icon,
            'status' => '1',
        ]);

        return redirect()->route('admin.achievements.index')->with('success', 'Achievement created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $achievement = Achievement::with('member')->findOrFail($id);
        return inertia('Admin/Achievements/Edit', [
            'data' => $achievement,
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

         $request->validate([
            'nip' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'date' => 'required|date',
            'image' => '',
            'document' => '',
            'icon' => 'required|string|max:255',
        ]);

        $memberId = Member::where('nip', $request->nip)->first()->id;
        $data = Achievement::where('id', $id)->first();
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
        Achievement::where('id',$id)->update([
            'member_id' => $memberId,
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'category' => $request->category,
            'image' => $image ?? $data->image,
            'document' => $document ?? $data->document,
            'icon' => $request->icon,
            'status' => '1',
        ]);

        return redirect()->route('admin.achievements.index')->with('success', 'Achievement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Achievement::destroy($id);
        return redirect()->route('admin.achievements.index')->with('success', 'Achievement deleted successfully.');
    }

    /**
     * Toggle the achievement's published status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function change($id)
    {
        $status = Achievement::where('id', $id)->value('status');

        Achievement::where('id', $id)->update([
            'status' => $status == '1' ? '0' : '1',
        ]);

        return redirect()->route('admin.achievements.index')->with('success', 'Status achievement berhasil diubah.');
    }
}
