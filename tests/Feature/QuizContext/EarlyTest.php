<?php

namespace Tests\Feature\QuizContext;

use App\Models\User;
use App\Services\FullQuizInsertion;
use App\Services\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EarlyTest extends TestCase
{

    use RefreshDatabase;

    public function test_sees_early_message_and_wait_time(): void
    {
        $wait = 10;

        $user = User::factory()->create();
        Auth::login($user);

        $ins = new FullQuizInsertion;
        $quiz_seed = new FullQuizSeed;
        $quiz_example1 = $quiz_seed->example1($wait);

        $ins->insert($quiz_example1);

        $this->get(route('playground'))
            ->assertSeeInOrder([
                'لم تبدأ المسابقة بعد، يرجى الإنتظار أو العودة في',
                "data-countdown-duration=\"$wait\"",
            ], false);
    }
}
