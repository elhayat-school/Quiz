<?php

namespace Tests\Feature\QuizContext;

use App\Models\User;
use App\Services\FullQuizInsertion;
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
        $wait = -2;

        $user = User::factory()->create();
        Auth::login($user);

        $ins = new FullQuizInsertion;
        $quiz_seed = new FullQuizSeed;
        $quiz_example1 = $quiz_seed->example1($wait);

        $ins->insert($quiz_example1);

        $question1 = $quiz_example1['questions'][0];

        $response = $this->get(route('playground'))
            ->assertSee($question1['content']);
        foreach ($question1['choices'] as $choice) {
            $response->assertSee($choice);
        }
    }
}
