<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeMember(array $overrides = []): Member
    {
        static $counter = 0;
        $counter++;

        return Member::create(array_merge([
            'nip' => '19900101202088' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'name' => 'Pemilih ' . $counter,
            'email' => "pemilih{$counter}@example.com",
            'agency' => 'Instansi Test',
            'nomember' => '0008' . $counter . '/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ], $overrides));
    }

    private function makePoll(array $options = ['Setuju', 'Tidak Setuju']): Poll
    {
        $poll = Poll::create([
            'question' => 'Apakah Anda setuju?',
            'is_open' => true,
        ]);

        foreach ($options as $optionText) {
            $poll->options()->create(['option_text' => $optionText]);
        }

        return $poll;
    }

    public function test_store_rejects_fewer_than_two_options()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/polls', [
            'question' => 'Pertanyaan tidak lengkap?',
            'options' => ['Hanya satu opsi'],
        ]);

        $response->assertSessionHasErrors('options');
        $this->assertDatabaseCount('polls', 0);
    }

    public function test_store_rejects_duplicate_options()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/polls', [
            'question' => 'Pertanyaan?',
            'options' => ['Sama', 'Sama'],
        ]);

        $response->assertSessionHasErrors('options.1');
    }

    public function test_admin_can_toggle_poll_status()
    {
        $admin = $this->makeAdminUser('administrator');
        $poll = $this->makePoll();

        $this->actingAs($admin)->post("/admin/polls/{$poll->id}/status")->assertSessionHas('success');
        $this->assertFalse($poll->fresh()->is_open);

        $this->actingAs($admin)->post("/admin/polls/{$poll->id}/status")->assertSessionHas('success');
        $this->assertTrue($poll->fresh()->is_open);
    }

    public function test_admin_can_update_question_and_add_a_new_option()
    {
        $admin = $this->makeAdminUser('administrator');
        $poll = $this->makePoll();
        $options = $poll->options->map(fn ($o) => ['id' => $o->id, 'option_text' => $o->option_text])->toArray();
        $options[] = ['id' => null, 'option_text' => 'Opsi Baru'];

        $response = $this->actingAs($admin)->put("/admin/polls/{$poll->id}", [
            'question' => 'Apakah Anda setuju (revisi)?',
            'options' => $options,
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('Apakah Anda setuju (revisi)?', $poll->fresh()->question);
        $this->assertSame(3, $poll->fresh()->options()->count());
        $this->assertDatabaseHas('poll_options', ['poll_id' => $poll->id, 'option_text' => 'Opsi Baru']);
    }

    public function test_removing_an_option_with_zero_votes_deletes_it()
    {
        $admin = $this->makeAdminUser('administrator');
        $poll = $this->makePoll(['A', 'B', 'C']);
        $remaining = $poll->options->where('option_text', '!=', 'C')
            ->map(fn ($o) => ['id' => $o->id, 'option_text' => $o->option_text])->values()->toArray();

        $this->actingAs($admin)->put("/admin/polls/{$poll->id}", [
            'question' => $poll->question,
            'options' => $remaining,
        ]);

        $this->assertSame(2, $poll->fresh()->options()->count());
        $this->assertDatabaseMissing('poll_options', ['poll_id' => $poll->id, 'option_text' => 'C']);
    }

    public function test_removing_an_option_that_already_has_votes_keeps_it_instead_of_discarding_the_vote()
    {
        $admin = $this->makeAdminUser('administrator');
        $poll = $this->makePoll(['A', 'B', 'C']);
        $optionA = $poll->options->firstWhere('option_text', 'A');
        $optionB = $poll->options->firstWhere('option_text', 'B');
        $optionC = $poll->options->firstWhere('option_text', 'C');
        $member = $this->makeMember();

        PollVote::create(['poll_id' => $poll->id, 'poll_option_id' => $optionA->id, 'member_id' => $member->id]);

        // Submit an update that only lists options B and C - simulating an
        // admin trying to drop option A, which already has a cast vote.
        $response = $this->actingAs($admin)->put("/admin/polls/{$poll->id}", [
            'question' => $poll->question,
            'options' => [
                ['id' => $optionB->id, 'option_text' => 'B'],
                ['id' => $optionC->id, 'option_text' => 'C'],
            ],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('poll_options', ['id' => $optionA->id]);
        $this->assertDatabaseHas('poll_votes', ['poll_option_id' => $optionA->id, 'member_id' => $member->id]);
    }

    public function test_admin_can_delete_a_poll()
    {
        $admin = $this->makeAdminUser('administrator');
        $poll = $this->makePoll();

        $this->actingAs($admin)->delete("/admin/polls/{$poll->id}")->assertSessionHas('success');
        $this->assertDatabaseMissing('polls', ['id' => $poll->id]);
    }

    public function test_deleting_a_poll_also_removes_its_options_and_votes()
    {
        $admin = $this->makeAdminUser('administrator');
        $poll = $this->makePoll();
        $option = $poll->options->first();
        $member = $this->makeMember();
        PollVote::create(['poll_id' => $poll->id, 'poll_option_id' => $option->id, 'member_id' => $member->id]);

        $this->actingAs($admin)->delete("/admin/polls/{$poll->id}");

        $this->assertDatabaseMissing('poll_options', ['poll_id' => $poll->id]);
        $this->assertDatabaseMissing('poll_votes', ['poll_id' => $poll->id]);
    }

    public function test_index_lists_option_and_vote_counts()
    {
        $admin = $this->makeAdminUser('administrator');
        $poll = $this->makePoll();
        $member = $this->makeMember();
        PollVote::create(['poll_id' => $poll->id, 'poll_option_id' => $poll->options->first()->id, 'member_id' => $member->id]);

        $response = $this->actingAs($admin)->get('/admin/polls');

        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Polls/Index')
            ->where('polls.data.0.options_count', 2)
            ->where('polls.data.0.votes_count', 1)
        );
    }
}
