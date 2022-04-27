<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LateTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_late_message(): void
    {
        $this->authenticate();

        FullQuizSeed::seed();

        $this->travel(config('quiz.QUIZ_MAX_DELAY') + 1)->seconds();

        $this->get(route('playground'))
            ->assertOk()
            ->assertSee('انت متأخر')
            ->assertDontSee('انظر إلى النتائج');
    }

    public function test_dont_see_late_message_when_ended(): void
    {
        $this->authenticate();

        $quiz_example1 = FullQuizSeed::seed();

        $delay = quiz_duration(count($quiz_example1['questions'])) + 10;
        $this->travel($delay)->seconds();

        $this->get(route('playground'))
            ->assertOk()
            ->assertDontSee('انت متأخر');
    }
}
