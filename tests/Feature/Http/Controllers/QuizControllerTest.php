<?php

namespace Tests\Feature\Http\Controllers;

use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_quiz(): void
    {
        $this->authenticate('admin');

        $this->post(route('quizzes.store'), FullQuizSeed::seed())
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('quizzes.index'));
    }
}
