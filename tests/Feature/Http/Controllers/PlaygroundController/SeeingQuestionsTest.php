<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use App\Models\User;
use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SeeingQuestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_all_questions_in_sequence(): void
    {
        Auth::login(User::factory()->create());

        $quiz_example_1 = FullQuizSeed::seed();

        foreach ($quiz_example_1['questions'] as $question) {

            $response = $this->get(route('playground'))
                ->assertSee($question['content']);

            foreach ($question['choices'] as $choice) {
                $response->assertSee($choice);
            }

            // Go beyond currently seen question period
            $this->travel(config('quiz.QUESTION_DEFAULT_DURATION') + 1)->seconds();
        }
    }
}
