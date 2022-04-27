<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoAvailableQuizzesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_no_available_quizzes(): void
    {
        $this->authenticate();

        $this->get(route('playground'))
            ->assertOk()
            ->assertSee('لا يوجد مسابقة مبرمجة');
    }
}
