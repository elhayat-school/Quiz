<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndedTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_ended_message(): void
    {
        $this->authenticate();

        $quiz_example1 = FullQuizSeed::seed();

        $delay = quiz_duration(count($quiz_example1['questions'])) + 10;
        $this->travel($delay)->seconds();

        $this->get(route('playground'))
            ->assertOk()
            ->assertSee([
                'لقد إنتهت لعبة اليوم',
                'انظر إلى النتائج',
            ]);
    }
}
