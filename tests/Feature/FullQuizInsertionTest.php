<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CurrentQuiz;
use App\Services\FullQuizInsertion;
use App\Services\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FullQuizInsertionTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_quiz_post_request(): void
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        Auth::login($user);

        $quiz_seed = new FullQuizSeed;

        $response = $this->post(route('quizzes.store'), $quiz_seed->example1())
            ->assertSessionHasNoErrors()
            // ->assertViewHasAll()
            // ->assertValid()
            ->assertRedirect(route('quizzes.index'));
    }

    public function test_accurate_insertion(): void
    {
        $ins = new FullQuizInsertion;
        $quiz_seed = new FullQuizSeed;
        $quiz_example1 = $quiz_seed->example1();

        $ins->insert($quiz_example1);

        $currentQuiz = new CurrentQuiz;
        $current_quiz = $currentQuiz();

        $this->assertTrue(
            $current_quiz->done === 0,
            'quiz shouldn\'t be done'
        );

        $this->assertTrue(
            $current_quiz->duration === $quiz_example1['duration'],
            'incorrect quiz duration'
        );

        foreach ($current_quiz->questions as $i => $question) {
            $src_question = $quiz_example1['questions'][$i];

            $src_question_content = $src_question['content'];
            $inserted_question_content = $question->content;

            $this->assertTrue(
                $inserted_question_content === $src_question_content,
                "Questions ORDER DOESN'T MATCH: \n\t" .
                    "DB: $inserted_question_content \n\t" .
                    "AND \n\t" .
                    "SRC: $src_question_content \n\t"
            );

            foreach ($question->choices as $j => $choice) {

                $src_choice_content = $src_question['choices'][$j + 1];
                $inserted_choice_content = $choice->content;

                $this->assertTrue(
                    $inserted_choice_content === $src_choice_content,
                    "Choices ORDER DOESN'T MATCH: \n\t" .
                        "DB: $inserted_choice_content \n\t" .
                        "AND \n\t" .
                        "SRC: $src_choice_content \n\t"
                );

                $this->assertTrue(
                    ($src_question['is_correct'] == $j + 1) == $choice->is_correct
                );
            }
        }
    }
}
