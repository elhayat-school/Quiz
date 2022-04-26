<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class NoAvailableQuizzesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_no_available_quizzes(): void
    {
        Auth::login(User::factory()->create());

        $this->get(route('playground'))
            ->assertStatus(200)
            ->assertSee('لا يوجد مسابقة مبرمجة');
    }
}
