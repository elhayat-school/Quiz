<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_ranking(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $student = User::factory()->create();
        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($student)
            ->get(route('ranking.current_quiz'))
            ->assertOk()
            ->assertSee('لا يوجد مسابقة مبرمجة');

        $this->actingAs($admin)
            ->get(route('ranking.global'))
            ->assertOk()
            ->assertSee('لا يوجد نتائج');

        FullQuizSeed::seed();

        $this->actingAs($student)
            ->get(route('ranking.current_quiz'))
            ->assertOk()
            ->assertSee('لا يوجد نتائج');

        $this->actingAs($admin)
            ->get(route('ranking.current_quiz'))
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

    public function test_see_ranking_table()
    {
        # code...
    }
}
