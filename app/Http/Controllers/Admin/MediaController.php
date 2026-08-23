<?php

namespace App\Http\Controllers\Admin;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Event;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $medias = Media::with('event')
        ->when(request()->q, function($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })
        ->latest()
        ->paginate(10);

   $medias->appends(['q' => request()->q]);

   return inertia('Admin/Medias/Index', [
       'medias' => $medias,
    ]);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $events = Event::get();

         return inertia('Admin/Medias/Create', [
            'events' => $events
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
        'media' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'event_id' => 'required',
    ]);

    $image = $request->file('media');
    if ($image) {
        $image = $request->file('media')->storePublicly('/images');
        // Proceed with storing or processing the uploaded file
    };

        Media::create([
            'title' => $request->title,
            'media' =>  $image,
            'event_id' => $request->event_id,
        ]);

     //redirect
     return redirect()->route('admin.medias.index')->with('success', 'Media berhasil ditambahkan.');

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
        $media = Media::with('event')->findOrFail($id);
        $event = Event::get();

        return inertia('Admin/Medias/Edit', [
            'media' => $media,
            'events' => $event
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
        'media' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'event_id' => 'required',
    ]);

    $image = $request->file('media');
    if ($image) {
        $image = $request->file('media')->storePublicly('/images');
        // Proceed with storing or processing the uploaded file
    }else{
        $image = Media::where('id', $id)->value('media');
    };

        Media::where('id',$id)->update([
            'title' => $request->title,
            'media' =>  $image,
            'event_id' => $request->event_id,
        ]);

     //redirect
     return redirect()->route('admin.medias.index')->with('success', 'Media berhasil diperbarui.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $media = Media::findOrFail($id);

        $media->delete();

        //redirect
        return redirect()->route('admin.medias.index')->with('success', 'Media berhasil dihapus.');
    }
}
