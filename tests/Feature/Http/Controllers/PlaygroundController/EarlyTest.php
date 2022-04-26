<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use App\Models\User;
use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EarlyTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_early_message_and_wait_time(): void
    {
        $wait = 10;

        Auth::login(User::factory()->create());

        FullQuizSeed::seed();

        $this->travel(-$wait)->seconds();

        $this->get(route('playground'))
            ->assertSee('لم تبدأ المسابقة بعد، يرجى الإنتظار أو العودة في')
            ->assertSee("data-countdown-duration=\"$wait\"", false);
    }
}
