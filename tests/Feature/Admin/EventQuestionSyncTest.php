<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventQuestionSyncTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_syncing_one_category_does_not_touch_questions_from_another_category()
    {
        $admin = $this->makeAdminUser('administrator');
        $event = Event::factory()->create();

        $categoryA = QuestionCategory::create(['title' => 'Kategori A']);
        $categoryB = QuestionCategory::create(['title' => 'Kategori B']);

        $questionA1 = Question::create(['question_category_id' => $categoryA->id, 'text' => 'A1', 'answer' => 1]);
        $questionA2 = Question::create(['question_category_id' => $categoryA->id, 'text' => 'A2', 'answer' => 1]);
        $questionB1 = Question::create(['question_category_id' => $categoryB->id, 'text' => 'B1', 'answer' => 1]);

        // attach a question from category B beforehand, it must survive a sync on category A
        $event->questions()->attach($questionB1->id);

        $this->actingAs($admin)->post("/admin/events/{$event->id}/questions/sync", [
            'question_category_id' => $categoryA->id,
            'question_ids' => [$questionA1->id],
        ])->assertRedirect();

        $attachedIds = $event->questions()->pluck('questions.id')->sort()->values()->all();

        $this->assertEquals([$questionA1->id, $questionB1->id], collect($attachedIds)->sort()->values()->all());
        $this->assertNotContains($questionA2->id, $attachedIds);
    }

    public function test_unchecking_a_question_detaches_it()
    {
        $admin = $this->makeAdminUser('administrator');
        $event = Event::factory()->create();
        $category = QuestionCategory::create(['title' => 'Kategori']);
        $question = Question::create(['question_category_id' => $category->id, 'text' => 'Q1', 'answer' => 1]);

        $event->questions()->attach($question->id);

        $this->actingAs($admin)->post("/admin/events/{$event->id}/questions/sync", [
            'question_category_id' => $category->id,
            'question_ids' => [],
        ])->assertRedirect();

        $this->assertCount(0, $event->questions()->get());
    }
}
