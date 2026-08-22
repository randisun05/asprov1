<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\DetailEvent;
use App\Models\Event;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Timer\Duration;

class TryoutController extends Controller
{


public function index()
    {
        //get exam group
         $memberId = auth()->guard('member')->user()->id;

            $events = Event::with(['detailEvents' => function ($query) use ($memberId) {
                $query->where('member_id', $memberId);
            }])
            ->where('category', 'Tryout')
            ->get();

            return inertia('User/Tryouts/Index', [
                'events' => $events,
            ]);
    }
   public function confirmation($id)
    {
        //get exam group
        $detail_event = DetailEvent::with('event', 'member')
                    ->where('member_id', auth()->guard('member')->user()->id)
                    ->where('id', $id)
                    ->first();

        if ($detail_event->event->duration == null) {
            DetailEvent::where('id', $detail_event->id)->update([
                'duration' => $detail_event->event->duration
            ]);
        } else {
            DetailEvent::where('id', $detail_event->id)->update([
                'duration' => 6000000
            ]);
        }

        //return with inertia
        return inertia('User/Tryouts/Confirmation', [
            'detail_event' => $detail_event,
        ]);
    }

    public function startExam($id)
{
    $member = auth()->guard('member')->user();

    $detail = DetailEvent::with('event')
        ->where('id', $id)
        ->where('member_id', $member->id)
        ->firstOrFail();

    // jika belum mulai ujian
    if (!$detail->start_time) {

        $detail->update([
            'start_at' => now(),
            'duration'   => $detail->event->duration * 60000 // convert menit ke ms
        ]);

    }

    return redirect()->route('user.tryouts.show', [
        'id'   => $detail->id,
        'page' => 1
    ]);
}

    /**
     * show
     *
     * @param  mixed $id
     * @param  mixed $page
     * @return void
     */
    public function show($id,$page)
{
    $member = auth()->guard('member')->user();

    $detail = DetailEvent::with('event')
        ->where('id',$id)
        ->where('member_id',$member->id)
        ->firstOrFail();

    $answers = Answer::with('question')
        ->where('detail_event_id',$detail->id)
        ->orderBy('question_order')
        ->get();

    $active = $answers->where('question_order',$page)->first();


    return inertia('User/Tryouts/Show',[
        'id'=>$id,
        'page'=>$page,
        'detail_event'=>$detail,
        'all_questions'=>$answers,
        'question_active'=>$active,
        'question_answered'=>$answers->whereNotNull('answer')->count(),
        'answer_order'=>explode(',',$active->answer_order ?? '')
    ]);
}

    public function updateDuration(Request $request, $detail_event_id)
    {
        $detail_event = DetailEvent::find($detail_event_id);
        $detail_event->duration = $request->duration;
        $detail_event->update();

        return response()->json([
            'success'  => true,
            'message' => 'Duration updated successfully.'
        ]);
    }

    public function answerQuestion(Request $request)
{
    $member = auth()->guard('member')->user();

    $answer = Answer::where('detail_event_id',$request->detail_event_id)
        ->where('question_id',$request->question_id)
        ->where('member_id',$member->id)
        ->firstOrFail();

    $question = $answer->question;

    $isCorrect = $question->answer == $request->answer ? 'Y':'N';

    $answer->update([
        'answer'=>$request->answer,
        'is_correct'=>$isCorrect
    ]);

    return back();
}

     public function endExam(Request $request)
{
    $member = auth()->guard('member')->user();

    $detail = DetailEvent::where('id',$request->detail_event_id)
        ->where('member_id',$member->id)
        ->firstOrFail();

    $totalCorrect = Answer::where('detail_event_id',$detail->id)
        ->where('is_correct','Y')
        ->count();

    $totalQuestion = Answer::where('detail_event_id',$detail->id)->count();

    $grade = round(($totalCorrect/$totalQuestion)*100,2);

    $detail->update([
        'end_at'=>now(),
        'total_correct'=>$totalCorrect,
        'grade'=>$grade
    ]);

    return redirect()->route('user.tryouts.resultExam',$detail->id);
}

    public function resultExam($detail_event_id)
    {
        //get exam group
        $detail_event = DetailEvent::with('event', 'member')
                ->where('member_id', auth()->guard('member')->user()->id)
                ->where('id', $detail_event_id)
                ->first();

        //return with inertia
        return inertia('User/Tryouts/Result', [
            'detail_event' => $detail_event,
        ]);
    }


   public function EnrollQuestion($id)
{
    $memberId = auth()->guard('member')->user()->id;

    try {
        $event = Event::findOrFail($id);

        return DB::transaction(function () use ($event, $memberId) {

            // Lock the member's registration row so a concurrent double-submit
            // (double click, retried request, a second tab) can't both pass
            // the "belum punya soal" check below and insert duplicate soal.
            $detail = DetailEvent::where('event_id', $event->id)
                ->where('member_id', $memberId)
                ->lockForUpdate()
                ->first();

            if (!$detail) {
                return response()->json([
                    'status' => false,
                    'message' => 'Member tidak terdaftar di event ini'
                ]);
            }

            // cek apakah sudah punya soal
            $already = Answer::where('event_id', $event->id)
                ->where('member_id', $memberId)
                ->exists();

            if ($already) {
                return response()->json([
                    'status' => true,
                    'message' => 'Member sudah memiliki soal'
                ]);
            }

            // ambil soal yang sudah dipilih untuk event/tryout ini
            $questions = $event->questions;

            if ($questions->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Soal belum tersedia'
                ]);
            }

            // random soal jika aktif
            $memberQuestions = $event->random_question == 'Y'
                ? $questions->shuffle()
                : $questions;

            $batchInsert = [];
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
                    'question_id'     => $question->id,
                    'member_id'       => $memberId,
                    'question_order'  => $order,
                    'answer_order'    => implode(',', $options),
                    'answer'          => 0,
                    'is_correct'      => 'N',
                ];

                $order++;
            }

            // insert sekali saja (karena 1 member)
            Answer::insert($batchInsert);

            return response()->json([
                'status' => true,
                'message' => 'Generate soal berhasil'
            ]);
        });

    } catch (\Throwable $e) {

        return response()->json([
            'status' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ]);
    }
}
}
