<?php

namespace Tests\Feature\QuizContext;

use App\Models\Question;
use App\Models\User;
use App\Services\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PlayingTest extends TestCase
{

    use RefreshDatabase;

    public function test_sees_first_question(): void
    {
        $question_index = 0;

        // Introduce multiplication
        $wait = -2;

        $user = User::factory()->create();
        Auth::login($user);

        $quiz_example_1 = FullQuizSeed::seed($wait);

        $this->recordAnswers($question_index);

        $this->seesQuestionAndItsChoices($quiz_example_1['questions'][$question_index]);
    }

    public function test_sees_second_question(): void
    {
        $question_index = 1;

        // Introduce multiplication
        $wait = -2;

        $user = User::factory()->create();
        Auth::login($user);

        $quiz_example_1 = FullQuizSeed::seed($wait);

        $this->recordAnswers($question_index);

        $this->seesQuestionAndItsChoices($quiz_example_1['questions'][$question_index]);
    }

    public function test_sees_third_question(): void
    {
        $question_index = 2;

        // Introduce multiplication
        $wait = -2;

        $user = User::factory()->create();
        Auth::login($user);

        $quiz_example_1 = FullQuizSeed::seed($wait);

        $this->recordAnswers($question_index);

        $this->seesQuestionAndItsChoices($quiz_example_1['questions'][$question_index]);
    }

    public function test_sees_forth_question(): void
    {
        $question_index = 3;

        // Introduce multiplication
        $wait = -2;

        $user = User::factory()->create();
        Auth::login($user);

        $quiz_example_1 = FullQuizSeed::seed($wait);

        $this->recordAnswers($question_index);

        $this->seesQuestionAndItsChoices($quiz_example_1['questions'][$question_index]);
    }

    private function seesQuestionAndItsChoices(array $question): void
    {
        $response = $this->get(route('playground'))
            ->assertSee($question['content']);
        foreach ($question['choices'] as $choice) {
            $response->assertSee($choice);
        }
    }

    private function recordAnswers(int $last_answer_index = 0): void
    {
        if ($last_answer_index === 0)
            return;

        $questions = Question::all();

        if ($last_answer_index > $questions->count())
            throw new \Exception("Cannot record $last_answer_index answers for the quiz", 1);

        for ($i = 0; $i < $last_answer_index; $i++) {

            $ans = $questions[$i]->answers()->create([
                'user_id' => auth()->user()->id,
                'choice_number' => rand(1, 4),
                // Introduce multiplication
                'served_at' => date('Y-m-d H:i:s',  time() - config('quiz.QUESTION_DEFAULT_DURATION') - 2),
                'received_at' => date('Y-m-d H:i:s',  time() - 5),
            ]);
        }
    }
}
