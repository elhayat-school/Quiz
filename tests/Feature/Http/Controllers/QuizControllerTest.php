<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Services\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_quiz(): void
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        Auth::login($user);

        $quiz_seed = new FullQuizSeed;

        $this->post(route('quizzes.store'), $quiz_seed->example1())
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('quizzes.index'));
    }
}
