<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class NoAvailableQuizzesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_no_available_quizzes(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->get(route('playground'))
            ->assertStatus(200)
            ->assertSee('لا يوجد مسابقة مبرمجة');
    }
}
