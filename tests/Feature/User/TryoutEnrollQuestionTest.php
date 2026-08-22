<?php

namespace Tests\Feature\User;

use App\Models\Answer;
use App\Models\DetailEvent;
use App\Models\Event;
use App\Models\Member;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TryoutEnrollQuestionTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(): Member
    {
        return Member::create([
            'nip' => '199001012020121008',
            'name' => 'Anggota Tryout',
            'email' => 'anggotatryout@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00008/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_generating_soal_twice_does_not_duplicate_answers()
    {
        $member = $this->makeMember();
        $event = Event::factory()->create(['category' => 'Tryout']);
        DetailEvent::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'approved',
        ]);

        $category = QuestionCategory::create(['title' => 'Kategori Tryout']);
        $question = Question::create(['question_category_id' => $category->id, 'text' => 'Q1', 'answer' => 1]);
        $event->questions()->attach($question->id);

        $first = $this->actingAs($member, 'member')->post("/user/tryout/{$event->id}/generate");
        $first->assertJson(['status' => true, 'message' => 'Generate soal berhasil']);

        $second = $this->actingAs($member, 'member')->post("/user/tryout/{$event->id}/generate");
        $second->assertJson(['status' => true, 'message' => 'Member sudah memiliki soal']);

        $this->assertSame(1, Answer::where('event_id', $event->id)->where('member_id', $member->id)->count());
    }
}
