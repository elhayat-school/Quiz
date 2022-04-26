<?php

namespace Tests\Feature\Http\Controllers\PlaygroundController;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Database\Seeders\FullQuizSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FinishedTest extends TestCase
{
    use RefreshDatabase;

    public function test_sees_finished_message(): void
    {
        Auth::login(User::factory()->create());

        FullQuizSeed::seed();

        $questions = Question::all();

        foreach ($questions as $question) {
            Answer::factory()
                ->answered()
                ->for(auth()->user())
                ->for($question)
                ->create();
        }

        $this->get(route('playground'))
            ->assertSee('تهانينا لقد انتهيت من المشاركة في مسابقة اليوم');
    }

    public function test_dont_see_finished_message_when_ended(): void
    {
        Auth::login(User::factory()->create());

        $quiz_example1 = FullQuizSeed::seed();

        $delay = quiz_duration(count($quiz_example1['questions'])) + 10;
        $this->travel($delay)->seconds();

        $this->get(route('playground'))
            ->assertDontSee('تهانينا لقد انتهيت من المشاركة في مسابقة اليوم');
    }
}
