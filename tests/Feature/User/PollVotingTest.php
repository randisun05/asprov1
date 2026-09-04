<?php

namespace Tests\Feature\User;

use App\Models\Member;
use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollVotingTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(array $overrides = []): Member
    {
        static $counter = 0;
        $counter++;

        return Member::create(array_merge([
            'nip' => '19900101202077' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'name' => 'Pemilih ' . $counter,
            'email' => "votingmember{$counter}@example.com",
            'agency' => 'Instansi Test',
            'nomember' => '0007' . $counter . '/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ], $overrides));
    }

    private function makePoll(array $attrs = [], array $options = ['Setuju', 'Tidak Setuju']): Poll
    {
        $poll = Poll::create(array_merge([
            'question' => 'Apakah Anda setuju?',
            'is_open' => true,
        ], $attrs));

        foreach ($options as $optionText) {
            $poll->options()->create(['option_text' => $optionText]);
        }

        return $poll;
    }

    public function test_guest_cannot_view_polls()
    {
        $this->get('/user/polls')->assertRedirect('/user/login');
    }

    public function test_member_sees_poll_list_with_vote_status()
    {
        $member = $this->makeMember();
        $poll = $this->makePoll();

        $response = $this->actingAs($member, 'member')->get('/user/polls');

        $response->assertInertia(fn ($page) => $page
            ->component('User/Polls/Index')
            ->where('polls.data.0.has_voted', false)
        );
    }

    public function test_member_can_vote_on_an_open_poll()
    {
        $member = $this->makeMember();
        $poll = $this->makePoll();
        $option = $poll->options->first();

        $response = $this->actingAs($member, 'member')->post("/user/polls/{$poll->id}/vote", [
            'option_id' => $option->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('poll_votes', [
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'member_id' => $member->id,
        ]);
    }

    public function test_member_cannot_vote_twice_on_the_same_poll()
    {
        $member = $this->makeMember();
        $poll = $this->makePoll();
        $options = $poll->options;

        $this->actingAs($member, 'member')->post("/user/polls/{$poll->id}/vote", [
            'option_id' => $options->first()->id,
        ]);

        $response = $this->actingAs($member, 'member')->post("/user/polls/{$poll->id}/vote", [
            'option_id' => $options->last()->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(1, PollVote::where('poll_id', $poll->id)->where('member_id', $member->id)->count());
    }

    public function test_member_cannot_vote_on_a_closed_poll()
    {
        $member = $this->makeMember();
        $poll = $this->makePoll(['is_open' => false]);
        $option = $poll->options->first();

        $response = $this->actingAs($member, 'member')->post("/user/polls/{$poll->id}/vote", [
            'option_id' => $option->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('poll_votes', 0);
    }

    public function test_member_cannot_vote_with_an_option_from_a_different_poll()
    {
        $member = $this->makeMember();
        $poll = $this->makePoll();
        $otherPoll = $this->makePoll(['question' => 'Pertanyaan lain?']);
        $foreignOption = $otherPoll->options->first();

        $response = $this->actingAs($member, 'member')->post("/user/polls/{$poll->id}/vote", [
            'option_id' => $foreignOption->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('poll_votes', 0);
    }

    public function test_show_page_exposes_results_after_voting()
    {
        $member = $this->makeMember();
        $poll = $this->makePoll();
        $option = $poll->options->first();
        PollVote::create(['poll_id' => $poll->id, 'poll_option_id' => $option->id, 'member_id' => $member->id]);

        $response = $this->actingAs($member, 'member')->get("/user/polls/{$poll->id}");

        $response->assertInertia(fn ($page) => $page
            ->component('User/Polls/Show')
            ->where('hasVoted', true)
            ->where('myOptionId', $option->id)
            ->where('totalVotes', 1)
        );
    }

    public function test_show_page_reports_not_voted_for_an_open_poll_before_voting()
    {
        $member = $this->makeMember();
        $poll = $this->makePoll();

        $response = $this->actingAs($member, 'member')->get("/user/polls/{$poll->id}");

        $response->assertInertia(fn ($page) => $page
            ->component('User/Polls/Show')
            ->where('hasVoted', false)
        );
    }
}
