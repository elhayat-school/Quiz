<?php

namespace Tests\Feature\QuizContext;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class NoAvailableQuizzesTest extends TestCase
{
    use RefreshDatabase;

    public function test_on_play(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->get(route('playground'))
            ->assertStatus(200)
            ->assertSee('لا يوجد مسابقة مبرمجة');
    }

    public function test_on_current_quiz_ranking(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->get(route('ranking.current_quiz'))
            ->assertStatus(200)
            ->assertSee('لا يوجد مسابقة مبرمجة');
    }

    public function test_on_global_ranking_for_player(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->get(route('ranking.global'))
            ->assertStatus(403);
    }

    public function test_on_global_ranking_for_admin(): void
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        Auth::login($user);

        $this->get(route('ranking.global'))
            ->assertStatus(200)
            ->assertSee('لا يوجد نتائج');
    }
}
