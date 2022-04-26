<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use App\Models\User;
use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LateTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_late_message(): void
    {
        Auth::login(User::factory()->create());

        FullQuizSeed::seed();

        $this->travel(config('quiz.QUIZ_MAX_DELAY') + 1)->seconds();

        $this->get(route('playground'))
            ->assertSee('انت متأخر')
            ->assertDontSee('انظر إلى النتائج');
    }

    public function test_dont_see_late_message_when_ended(): void
    {
        Auth::login(User::factory()->create());

        $quiz_example1 = FullQuizSeed::seed();

        $delay = quiz_duration(count($quiz_example1['questions'])) + 10;
        $this->travel($delay)->seconds();

        $this->get(route('playground'))
            ->assertDontSee('انت متأخر');
    }
}
