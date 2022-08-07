<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizRequest;
use App\Models\Answer;
use App\Models\Quiz;
use App\Services\FullQuizInsertion;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(): View|Factory
    {
        return view('quiz.index')
            ->with('quizzes', Quiz::with('questions.choices')->oldest('start_at')->get());
    }

    public function create(): View|Factory
    {
        return view('quiz.create');
    }

    public function store(StoreQuizRequest $request): RedirectResponse
    {
        $ins = new FullQuizInsertion;

        $ins->insert($request->all());

        return to_route('quizzes.index');
    }

    public function cacheParticipationStats(Quiz $quiz): RedirectResponse
    {
        $establishments = config('quiz.ESTABLISHMENTS');
        // $establishments = DB::table('users')
        //     ->whereNotNull('establishment')
        //     ->groupBy('establishment')
        //     ->pluck('establishment');

        $participation_stats = [];
        foreach ($establishments as $establishment) {
            $count = Answer::getEstablishmentParticipation($quiz->id, $establishment);

            if ($count > 0) {
                $participation_stats[] = "$establishment:$count";
            }
        }
        $participation_stats = implode('-', $participation_stats);

        $quiz->update(['participation_stats' => $participation_stats]);

        return back();
    }

    public function markAsDone(Request $request, Quiz $quiz): RedirectResponse
    {
        $quiz->update(['done' => $request->new_state === 'done']);

        return back();
    }
}
