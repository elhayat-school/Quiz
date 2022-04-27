<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_available_quizzes_on_current_quiz_ranking(): void
    {
        $this->authenticate();

        $this->get(route('ranking.current_quiz'))
            ->assertOk()
            ->assertSee('لا يوجد مسابقة مبرمجة');
    }

    public function test_no_global_ranking_for_player(): void
    {
        $this->authenticate();

        $this->get(route('ranking.global'))
            ->assertStatus(403);
    }

    public function test_no_available_quizzes_on_global_ranking_for_admin(): void
    {
        $this->authenticate('admin');

        $this->get(route('ranking.global'))
            ->assertOk()
            ->assertSee('لا يوجد نتائج');
    }

    public function test_all_answers_correct_each_in_one_second()
    {
        $this->travel(0)->seconds();

        $this->authenticate();

        $quiz_example_1 = FullQuizSeed::seed();

        $questions = Question::all();

        Answer::factory()
            ->count(4)
            ->sequence(
                [
                    'choice_number' => $quiz_example_1['questions'][0]['is_correct'],
                    'question_id' => $questions[0]->id,
                ],
                [
                    'choice_number' => $quiz_example_1['questions'][1]['is_correct'],
                    'question_id' => $questions[1]->id,
                ],
                [
                    'choice_number' => $quiz_example_1['questions'][2]['is_correct'],
                    'question_id' => $questions[2]->id,
                ],
                [
                    'choice_number' => $quiz_example_1['questions'][3]['is_correct'],
                    'question_id' => $questions[3]->id,
                ]
            )
            ->receivedInstaniously() // 1s per answer
            ->for(auth()->user())
            ->create();

        $res = $this->get(route('ranking.current_quiz'))
            ->assertOk();

        $results = $res->getOriginalContent()->getData()['results'];

        $this->assertCount($results->first()->count_correct_answers, auth()->user()->answers);
        $this->assertCount($results->first()->sum_elapsed_seconds, auth()->user()->answers);
    }
}
