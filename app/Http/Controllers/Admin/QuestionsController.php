<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\QuestionsImport;
use App\Models\Answer;
use App\Models\DetailEvent;
use App\Models\Event;
use App\Models\Member;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get exams
        $datas = Question::when(request()->q, function($datas) {
            $datas = $datas->where('text', 'like', '%'. request()->q . '%');
        })->when(request()->category_id, function($datas) {
            $datas = $datas->where('question_category_id', request()->category_id);
        })->with('category')->latest()->paginate(5);

        //append query string to pagination links
        $datas->appends(['q' => request()->q, 'category_id' => request()->category_id]);

        //render with inertia
        return inertia('Admin/Questions/Index', [
            'datas' => $datas,
            'categories' => QuestionCategory::all(),
            'filters' => [
                'q' => request()->q,
                'category_id' => request()->category_id ? (int) request()->category_id : null,
            ],
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = QuestionCategory::all();
        return inertia('Admin/Questions/Create', [
            'categories' => $categories
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
        //validate request
        $request->validate([
            'text'          => 'required',
            'a'          => 'nullable',
            'b'          => 'nullable',
            'c'          => 'nullable',
            'd'          => 'nullable',
            'e'          => 'nullable',
            'answer'            => 'required',
            'question_category_id' => 'required|exists:question_categories,id',
        ]);

        //create question
        Question::create([
            'text'          => $request->text,
            'a'          => $request->a,
            'b'          => $request->b,
            'c'          => $request->c,
            'd'          => $request->d,
            'e'          => $request->e,
            'answer'            => $request->answer,
            'question_category_id' => $request->question_category_id,
        ]);

        //redirect
        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil ditambahkan.');
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
         $categories = QuestionCategory::all();
         $data = Question::findOrFail($id);

         return inertia('Admin/Questions/Edit',
         [
            'data' => $data,
            'categories' => $categories
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
        //validate request
        $request->validate([
            'text'          => 'required',
            'a'          => 'nullable',
            'b'          => 'nullable',
            'c'          => 'nullable',
            'd'          => 'nullable',
            'e'          => 'nullable',
            'answer'            => 'required',
            'question_category_id' => 'required|exists:question_categories,id',
        ]);

        //update question
        Question::findOrFail($id)->update([
            'text'          => $request->text,
            'a'          => $request->a,
            'b'          => $request->b,
            'c'          => $request->c,
            'd'          => $request->d,
            'e'          => $request->e,
            'answer'            => $request->answer,
            'question_category_id' => $request->question_category_id,
        ]);

        //redirect
        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //delete question
        Question::findOrFail($id)->delete();

        //redirect
        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil dihapus.');
    }

    public function import()
    {
        $categories = QuestionCategory::all();

        return inertia('Admin/Questions/Import', [
            'categories' => $categories
        ]);
    }

    /**
     * storeImport
     *
     * @param  mixed $request
     * @return void
     */
    public function storeImport(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        // import data
        Excel::import(new QuestionsImport($id), $request->file('file'));

        //redirect
        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil diimport.');
    }

   public function EnrollQuestion($id)
{
    DB::beginTransaction();

    try {
        $event = Event::findOrFail($id);
        // ambil semua member dari detail_event
        $detailEvents = DetailEvent::where('event_id', $event->id)->get();

        if ($detailEvents->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada member'
            ]);
        }

        // ambil member yang SUDAH punya soal
        $membersWithQuestion = Answer::where('event_id', $event->id)
            ->distinct()
            ->pluck('member_id')
            ->toArray();

        // filter hanya member yang BELUM punya soal
        $newMembers = $detailEvents->whereNotIn('member_id', $membersWithQuestion);

        if ($newMembers->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Semua member sudah memiliki soal'
            ]);
        }

        // ambil semua soal yang sudah dipilih untuk event/tryout ini
        $questions = $event->questions;

        if ($questions->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Soal belum tersedia'
            ]);
        }

        $batchInsert = [];
        $batchSize = 1000;

        foreach ($newMembers as $detail) {

            $member_id = $detail->member_id;

            // random soal jika aktif
            $memberQuestions = $event->random_question == 'Y'
                ? $questions->shuffle()
                : $questions;

            $order = 1;

            foreach ($memberQuestions as $question) {

                // opsi jawaban
                $options = [1,2];

                if ($question->c) $options[] = 3;
                if ($question->d) $options[] = 4;
                if ($question->e) $options[] = 5;

                if ($event->random_answer == 'Y') {
                    shuffle($options);
                }

                $batchInsert[] = [
                    'event_id'        => $event->id,
                    'detail_event_id' => $detail->id,
                    'member_id'       => $member_id,
                    'question_id'     => $question->id,
                    'question_order'  => $order,
                    'answer_order'    => implode(',', $options),
                    'answer'          => 0,
                    'is_correct'      => 'N', //0
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];

                $order++;

                // batch insert
                if (count($batchInsert) >= $batchSize) {
                    Answer::insertOrIgnore($batchInsert);
                    $batchInsert = [];
                }
            }
        }

        if (!empty($batchInsert)) {
            Answer::insertOrIgnore($batchInsert);
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Generate soal untuk member baru berhasil'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ]);
    }
}
}
