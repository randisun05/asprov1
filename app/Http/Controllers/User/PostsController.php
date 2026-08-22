<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\instansi;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $posts = Post::where('member_id', auth()->guard('member')->user()->id)
        ->when(request()->q, function($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })
        ->with('member','category','react')
        ->latest()
        ->paginate(6);

        //append query string to pagination links
        $posts->appends(['q' => request()->q]);


        return inertia('User/Posts/Index', [
            'posts' => $posts,

         ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $categories = Category::get();
        return inertia('User/Posts/Create', [
            'categories' => $categories,
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
        'title' => 'required|string|unique:posts,title',
        'body' => 'required|',
        'document' => 'file|mimes:pdf|max:2048|nullable',
        'picture' => 'image:allow_svg|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
    ]);

    $slug = strtolower(str_replace(' ', '-', $request->title));
     $slug = preg_replace('/[^a-z0-9-]/', '', $slug);

    // Ensure the slug stays unique since `posts.slug` has a unique
    // constraint - two different titles can still slugify to the same
    // value once punctuation is stripped.
    $originalSlug = $slug;
    $counter = 1;
    while (Post::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }

    $body = $request->body;

    // Ambil 100 kata pertama dari body
    // Hapus tag HTML dari body
    $bodyWithoutTags = strip_tags($body);
    $words = str_word_count($bodyWithoutTags, 1);
    $excerpt = implode(' ', array_slice($words, 0, 50));

    // Tambahkan "..." jika body memiliki lebih dari 100 kata
    if (count($words) > 50) {
        $excerpt .= '...';
    }
    $today = Carbon::now()->format('Y-m-d H:i:s');

    // Store the file using Laravel's file storage system
    $document = $request->file('document');
    if ($document) {
        $document = $request->file('document')->storePublicly('/documents');
        // Proceed with storing or processing the uploaded file
    };

    $image = $request->file('picture');
    if ($image) {
        $image = $request->file('picture')->storePublicly('/images');
        // Proceed with storing or processing the uploaded file
    };


        Post::create([
            'title' => $request->title,
            'category_id' => $request->category,
            'body' =>  $request->body,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'image' => $image,
            'document' => $document,
            'publish_at' => $today,
            'member_id' => auth()->guard('member')->user()->id,
        ]);



     //redirect
     return redirect()->route('user.posts.index')->with('success', 'Cerita berhasil ditambahkan.');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {

        if ($post->member_id == auth()->guard('member')->user()->id) {
            $post->load('member', 'category', 'react'); // Load the 'member' and 'category' relationships
            return inertia('User/Posts/Show', [
            'post' => $post
            ]);
        } else {
            return redirect()->route('user.posts.index')->with('error', 'You are not authorized to view this post');
        }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {

        $categories = Category::get();

        if ($post->member_id == auth()->guard('member')->user()->id) {
            return inertia('User/Posts/Edit', [
                'post' => $post,
                'categories' => $categories
            ]);
        } else {
            return redirect()->route('user.posts.index')->with('error', 'You are not authorized to edit this post');
        }

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

    $post = Post::findOrFail($id);

    if ($post->member_id != auth()->guard('member')->user()->id) {
        return redirect()->route('user.posts.index')->with('error', 'You are not authorized to update this post');
    }

    // Validate request including file validation
      $request->validate([
        'title' => ['required', 'string', \Illuminate\Validation\Rule::unique('posts', 'title')->ignore($id)],
        'body' => 'required|',
        'document' => 'file|mimes:pdf|max:2048|nullable',
        'picture' => 'image:allow_svg|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
    ]);

    $slug = strtolower(str_replace(' ', '-', $request->title));
     $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
    $body = $request->body;

    // Ambil 100 kata pertama dari body
    // Hapus tag HTML dari body
    $bodyWithoutTags = strip_tags($body);
    $words = str_word_count($bodyWithoutTags, 1);
    $excerpt = implode(' ', array_slice($words, 0, 50));

    // Tambahkan "..." jika body memiliki lebih dari 100 kata
    if (count($words) > 50) {
        $excerpt .= '...';
    }
    $today = Carbon::now()->format('Y-m-d H:i:s');

    // Store the file using Laravel's file storage system
    $document = $request->file('document');
    if ($document) {
        $document = $request->file('document')->storePublicly('/documents');
        // Proceed with storing or processing the uploaded file
    } else {
        $document = Post::where ('id', $id)->value('document');
    };

    $image = $request->file('picture');
    if ($image) {
        $image = $request->file('picture')->storePublicly('/images');
        // Proceed with storing or processing the uploaded file
    }  else {
        $image = Post::where ('id', $id)->value('image');
    };

        Post::where('id',$id)->update([
            'title' => $request->title,
            'category_id' => $request->category,
            'body' =>  $request->body,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'image' => $image,
            'document' => $document,
            'publish_at' => $today,
            'member_id' => auth()->guard('member')->user()->id,
        ]);



     //redirect
     return redirect()->route('user.posts.index')->with('success', 'Cerita berhasil diperbarui.');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if ($post->member_id == auth()->guard('member')->user()->id) {

            $post->delete();

            //redirect
            return redirect()->route('user.posts.index')->with('success', 'Cerita berhasil dihapus.');
        } else {
            return redirect()->route('user.posts.index')->with('error', 'You are not authorized to delete this post');
        }
    }

    public function submission($id)
    {
        $post = Post::findOrFail($id);

        if ($post->member_id == auth()->guard('member')->user()->id) {
            $post->update([
                'status' => 'submission'
            ]);

        } else {
            return redirect()->route('user.posts.index')->with('error', 'You are not authorized to submit this post');
        }


        //redirect
        return redirect()->route('user.posts.index')->with('success', 'Cerita berhasil diajukan untuk publikasi.');
    }

    public function list()
    {
        if (auth()->guard('member')->check()) {
            $posts = Post::with('member', 'category', 'react')
            ->when(request()->q, function($query) {
                $query->where('title', 'like', '%' . request()->q . '%');
            })
            ->where(function($query) {
                $query->where('status', 'approved')
                    ->orWhere('status', 'limited');
            })
            ->latest()
            ->paginate(6);

            $posts->appends(['q' => request()->q]);
            return inertia('User/Posts/List', [
                'posts' => $posts
            ]);
        } else {
            return redirect()->route('login');
        }
    }

    public function showlist(Post $post)
    {
        if (auth()->guard('member')->check() && ($post->status == 'approved' || $post->status == 'limited')) {
            $post->load('member', 'category','react'); // Mengambil relasi 'member' dan 'category'
            return inertia('User/Posts/ListShow', [
                'post' => $post
            ]);
        } else {
            return redirect()->route('user.posts.list')->with('error', 'You are not authorized to view this post');
        }

    }

}
