<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\FullQuizInsertion;
use App\Services\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class QuizContextIsLateTest extends TestCase
{

    use RefreshDatabase;

    public function test_sees_late_message_and_ranking_button(): void
    {
        $wait = -config('quiz.QUIZ_MAX_DELAY') - 1;

        $user = User::factory()->create();
        Auth::login($user);

        $ins = new FullQuizInsertion;
        $quiz_seed = new FullQuizSeed;
        $quiz_example1 = $quiz_seed->example1($wait);

        $ins->insert($quiz_example1);

        $this->get(route('playground'))
            ->assertSee([
                'انت متأخر',
                'انظر إلى النتائج'
            ]);
    }
}
