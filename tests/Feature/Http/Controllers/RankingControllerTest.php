<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RankingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_available_quizzes_on_current_quiz_ranking(): void
    {
        Auth::login(User::factory()->create());

        $this->get(route('ranking.current_quiz'))
            ->assertStatus(200)
            ->assertSee('لا يوجد مسابقة مبرمجة');
    }

    public function test_no_global_ranking_for_player(): void
    {
        Auth::login(User::factory()->create());

        $this->get(route('ranking.global'))
            ->assertStatus(403);
    }

    public function test_no_available_quizzes_on_global_ranking_for_admin(): void
    {
        Auth::login(User::factory()->make(['role' => 'admin']));

        $this->get(route('ranking.global'))
            ->assertStatus(200)
            ->assertSee('لا يوجد نتائج');
    }
}
