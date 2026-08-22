<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;

class QuestionCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = QuestionCategory::when(request()->q, function ($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })->withCount('questions')->latest()->paginate(10);

        $datas->appends(['q' => request()->q]);

        return inertia('Admin/QuestionCategories/Index', [
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
        return inertia('Admin/QuestionCategories/Create');
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
            'title' => 'required|unique:question_categories,title',
        ], [
            'title.unique' => 'Kelompok soal sudah tersedia',
        ]);

        QuestionCategory::create([
            'title' => $request->title,
        ]);

        return redirect()->route('admin.question-categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = QuestionCategory::findOrFail($id);

        return inertia('Admin/QuestionCategories/Edit', [
            'data' => $data,
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
            'title' => 'required|unique:question_categories,title,' . $id,
        ], [
            'title.unique' => 'Kelompok soal sudah tersedia',
        ]);

        QuestionCategory::findOrFail($id)->update([
            'title' => $request->title,
        ]);

        return redirect()->route('admin.question-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        QuestionCategory::findOrFail($id)->delete();

        return redirect()->route('admin.question-categories.index');
    }
}
