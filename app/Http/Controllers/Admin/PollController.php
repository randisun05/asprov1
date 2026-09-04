<?php

namespace App\Http\Controllers\Admin;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\MemberNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::withCount(['options', 'votes'])
            ->when(request()->q, function ($query) {
                $query->where('question', 'like', '%' . request()->q . '%');
            })
            ->latest()
            ->paginate(10);

        $polls->appends(['q' => request()->q]);

        return inertia('Admin/Polls/Index', [
            'polls' => $polls,
        ]);
    }

    public function create()
    {
        return inertia('Admin/Polls/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'description' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|distinct',
        ]);

        $poll = Poll::create([
            'question' => $request->question,
            'description' => $request->description,
            'is_open' => true,
        ]);

        foreach ($request->options as $optionText) {
            $poll->options()->create(['option_text' => $optionText]);
        }

        MemberNotification::broadcast(
            'poll',
            'Polling baru: ' . $poll->question,
            $poll->description,
            "/user/polls/{$poll->id}",
            $poll
        );

        return redirect()->route('admin.polls.index')->with('success', 'Polling berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $poll = Poll::with(['options' => function ($query) {
            $query->withCount('votes');
        }])->findOrFail($id);

        return inertia('Admin/Polls/Edit', [
            'poll' => $poll,
        ]);
    }

    /**
     * An option that already has votes is kept even if it's missing from
     * the submitted list, so editing a poll can never silently discard cast
     * votes - only untouched (zero-vote) options are actually removable.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string',
            'description' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.id' => 'nullable|integer',
            'options.*.option_text' => 'required|string',
        ]);

        $poll = Poll::with(['options' => function ($query) {
            $query->withCount('votes');
        }])->findOrFail($id);

        $poll->update([
            'question' => $request->question,
            'description' => $request->description,
        ]);

        $submittedIds = collect($request->options)->pluck('id')->filter()->all();

        foreach ($poll->options as $option) {
            if (!in_array($option->id, $submittedIds, true) && $option->votes_count === 0) {
                $option->delete();
            }
        }

        foreach ($request->options as $optionInput) {
            if (!empty($optionInput['id'])) {
                PollOption::where('poll_id', $poll->id)->where('id', $optionInput['id'])
                    ->update(['option_text' => $optionInput['option_text']]);
            } else {
                $poll->options()->create(['option_text' => $optionInput['option_text']]);
            }
        }

        return redirect()->route('admin.polls.index')->with('success', 'Polling berhasil diperbarui.');
    }

    public function status($id)
    {
        $poll = Poll::findOrFail($id);
        $poll->update(['is_open' => !$poll->is_open]);

        return redirect()->route('admin.polls.index')->with('success', 'Status polling berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Poll::findOrFail($id)->delete();

        return redirect()->route('admin.polls.index')->with('success', 'Polling berhasil dihapus.');
    }
}
