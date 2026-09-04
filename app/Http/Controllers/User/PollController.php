<?php

namespace App\Http\Controllers\User;

use App\Models\Poll;
use App\Models\PollVote;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function index()
    {
        $memberId = auth()->guard('member')->id();

        $polls = Poll::withCount('options')
            ->latest()
            ->paginate(10);

        $votedPollIds = PollVote::where('member_id', $memberId)
            ->whereIn('poll_id', $polls->pluck('id'))
            ->pluck('poll_id')
            ->all();

        $polls->getCollection()->transform(function ($poll) use ($votedPollIds) {
            $poll->has_voted = in_array($poll->id, $votedPollIds, true);
            return $poll;
        });

        return inertia('User/Polls/Index', [
            'polls' => $polls,
        ]);
    }

    public function show($id)
    {
        $memberId = auth()->guard('member')->id();

        $poll = Poll::with(['options' => function ($query) {
            $query->withCount('votes');
        }])->findOrFail($id);

        $vote = PollVote::where('poll_id', $poll->id)->where('member_id', $memberId)->first();

        return inertia('User/Polls/Show', [
            'poll' => $poll,
            'hasVoted' => $vote !== null,
            'myOptionId' => $vote?->poll_option_id,
            'totalVotes' => $poll->options->sum('votes_count'),
        ]);
    }

    public function vote(Request $request, $id)
    {
        $memberId = auth()->guard('member')->id();
        $poll = Poll::findOrFail($id);

        if (!$poll->is_open) {
            return redirect()->back()->with('error', 'Polling ini sudah ditutup.');
        }

        if (PollVote::where('poll_id', $poll->id)->where('member_id', $memberId)->exists()) {
            return redirect()->back()->with('error', 'Anda sudah memilih di polling ini.');
        }

        $request->validate([
            'option_id' => 'required|exists:poll_options,id',
        ]);

        $option = $poll->options()->where('id', $request->option_id)->first();

        if (!$option) {
            return redirect()->back()->with('error', 'Opsi tidak ditemukan pada polling ini.');
        }

        try {
            PollVote::create([
                'poll_id' => $poll->id,
                'poll_option_id' => $option->id,
                'member_id' => $memberId,
            ]);
        } catch (QueryException $e) {
            // Unique(poll_id, member_id) constraint - guards against a
            // double-submitted vote request racing past the exists() check
            // above.
            return redirect()->back()->with('error', 'Anda sudah memilih di polling ini.');
        }

        return redirect()->back()->with('success', 'Terima kasih, pilihan Anda telah disimpan.');
    }
}
