<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_quiz(): void
    {
        $this->authenticate('admin');

        $quiz_seed = new FullQuizSeed;

        $this->post(route('quizzes.store'), $quiz_seed->example1())
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('quizzes.index'));
    }
}
