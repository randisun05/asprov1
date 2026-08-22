<?php

namespace Tests\Feature\Admin;

use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionCrudTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_administrator_can_create_a_question_in_a_category()
    {
        $admin = $this->makeAdminUser('administrator');
        $category = QuestionCategory::create(['title' => 'Kategori A']);

        $response = $this->actingAs($admin)->post('/admin/questions', [
            'text' => 'Pertanyaan 1',
            'a' => 'A', 'b' => 'B',
            'answer' => 1,
            'question_category_id' => $category->id,
        ]);

        $response->assertRedirect(route('admin.questions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('questions', [
            'text' => 'Pertanyaan 1',
            'question_category_id' => $category->id,
        ]);
    }

    public function test_administrator_can_delete_a_question()
    {
        $admin = $this->makeAdminUser('administrator');
        $category = QuestionCategory::create(['title' => 'Kategori B']);
        $question = Question::create(['question_category_id' => $category->id, 'text' => 'Q', 'answer' => 1]);

        $response = $this->actingAs($admin)->delete("/admin/questions/{$question->id}");

        $response->assertRedirect(route('admin.questions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }

    public function test_questions_index_can_be_filtered_by_category()
    {
        $admin = $this->makeAdminUser('administrator');
        $categoryA = QuestionCategory::create(['title' => 'Kategori A']);
        $categoryB = QuestionCategory::create(['title' => 'Kategori B']);
        Question::create(['question_category_id' => $categoryA->id, 'text' => 'Soal A', 'answer' => 1]);
        Question::create(['question_category_id' => $categoryB->id, 'text' => 'Soal B', 'answer' => 1]);

        $response = $this->actingAs($admin)->get('/admin/questions?category_id=' . $categoryA->id);

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('datas.total', 1)
            ->where('datas.data.0.text', 'Soal A')
        );
    }
}
