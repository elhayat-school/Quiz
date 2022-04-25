<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use App\Models\User;
use App\Services\FullQuizInsertion;
use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LateTest extends TestCase
{

    use RefreshDatabase;

    public function test_sees_late_message(): void
    {
        $wait = -config('quiz.QUIZ_MAX_DELAY') - 1;

        $user = User::factory()->create();
        Auth::login($user);

        $ins = new FullQuizInsertion;
        $quiz_seed = new FullQuizSeed;
        $quiz_example1 = $quiz_seed->example1($wait);

        $ins->insert($quiz_example1);

        $this->get(route('playground'))
            ->assertSee('انت متأخر')
            ->assertDontSee('انظر إلى النتائج');
    }

    public function test_not_sees_late_message_when_ended(): void
    {
        $wait = -2000;

        $user = User::factory()->create();
        Auth::login($user);

        $ins = new FullQuizInsertion;
        $quiz_seed = new FullQuizSeed;
        $quiz_example1 = $quiz_seed->example1($wait);

        $ins->insert($quiz_example1);

        $this->get(route('playground'))
            ->assertDontSee('انت متأخر');
    }
}
