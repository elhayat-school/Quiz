<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionForcedTimerTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_wait_for_the_end_of_current_question_period_timer(): void
    {
        $this->travel(0)->seconds();

        $this->authenticate();

        FullQuizSeed::seed();

        $response = $this->get(route('playground'));

        $time_to_answer = 2;
        $this->travel($time_to_answer)->seconds();

        $question = $response->getOriginalContent()->getData()['question'];

        $this->post(route('answer'), [
            'question_id' => $question->id,
            'choice_number' => $question->choices->random()->choice_number,
        ]);

        $remaining_question_time = config('quiz.QUESTION_DEFAULT_DURATION') - $time_to_answer;

        $this->get(route('playground'))
            ->assertOk()
            ->assertSee('يرجى الانتظار حتى نهاية العد التنازلي قبل الخوض في السؤال الموالي')
            ->assertSee("data-countdown-duration=\"$remaining_question_time\"", false);
    }
}
