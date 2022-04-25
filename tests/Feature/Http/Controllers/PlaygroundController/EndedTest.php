<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use App\Models\User;
use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EndedTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_ended_message(): void
    {
        Auth::login(User::factory()->create());

        $quiz_example1 = FullQuizSeed::seed();

        $delay = quiz_duration(count($quiz_example1['questions'])) + 10;
        $this->travel($delay)->seconds();

        $this->get(route('playground'))
            ->assertSee([
                'لقد إنتهت لعبة اليوم',
                'انظر إلى النتائج'
            ]);
    }
}
