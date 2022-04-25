<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use App\Models\User;
use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\Helpers\RecordAnswers;
use Tests\TestCase;

class SeeingQuestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_first_question(): void
    {
        $this->sees_question(0);
    }

    public function test_sees_second_question(): void
    {
        $this->sees_question(1);
    }

    public function test_sees_third_question(): void
    {
        $this->sees_question(2);
    }

    public function test_sees_forth_question(): void
    {
        $this->sees_question(3);
    }

    private function sees_question(int $question_index): void
    {
        // Introduce multiplication
        $wait = -2;

        $user = User::factory()->create();
        Auth::login($user);

        $quiz_example_1 = FullQuizSeed::seed($wait);

        RecordAnswers::exec($question_index);

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
}
