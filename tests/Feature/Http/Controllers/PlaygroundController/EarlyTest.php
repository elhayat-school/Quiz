<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EarlyTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_early_message_and_wait_time(): void
    {
        $wait = 10;

        $this->authenticate();

        FullQuizSeed::seed();

        $this->travel(-$wait)->seconds();

        $this->get(route('playground'))
            ->assertOk()
            ->assertSee('لم تبدأ المسابقة بعد، يرجى الإنتظار أو العودة في')
            ->assertSee("data-countdown-duration=\"$wait\"", false);
    }
}
