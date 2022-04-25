<?php

namespace Tests\Feature\Services;

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

    public function test_accurate_quiz_insertion(): void
    {
        $ins = new FullQuizInsertion;
        $quiz_seed = new FullQuizSeed;
        $quiz_example_1 = $quiz_seed->example1();

        $ins->insert($quiz_example_1);

        $currentQuiz = new CurrentQuiz;
        $current_quiz = $currentQuiz();

        $this->assertEquals(
            0,
            $current_quiz->done,
            'quiz shouldn\'t be done'
        );

        $this->assertEquals(
            quiz_duration(count($quiz_example_1['questions'])),
            $current_quiz->duration,
            'incorrect quiz duration'
        );

        foreach ($current_quiz->questions as $i => $question) {
            $src_question = $quiz_example_1['questions'][$i];

            $src_question_content = $src_question['content'];
            $inserted_question_content = $question->content;

            $this->assertEquals(
                $src_question_content,
                $inserted_question_content,
                "Questions ORDER DOESN'T MATCH: \n\t" .
                    "DB: $inserted_question_content \n\t" .
                    "AND \n\t" .
                    "SRC: $src_question_content \n\t"
            );

            foreach ($question->choices as $j => $choice) {

                $src_choice_content = $src_question['choices'][$j + 1];
                $inserted_choice_content = $choice->content;

                $this->assertEquals(
                    $src_choice_content,
                    $inserted_choice_content,
                    "Choices ORDER DOESN'T MATCH: \n\t" .
                        "DB: $inserted_choice_content \n\t" .
                        "AND \n\t" .
                        "SRC: $src_choice_content \n\t"
                );

                $this->assertEquals(
                    ($src_question['is_correct'] == $j + 1),
                    $choice->is_correct,
                    "Incorrect choice is marked as correct"
                );
            }
        }
    }
}
