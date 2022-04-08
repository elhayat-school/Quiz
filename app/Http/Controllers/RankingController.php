<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Choice;
use App\Models\Quiz;
use Illuminate\Support\Facades\Cache;

class RankingController extends Controller
{
    public function currentQuizResults()
    {
        $current_quiz = Cache::remember(
            'current_quiz',
            10,
            fn () => Quiz::with('questions.choices')->notDone()->sortByOldestStartTime()->first()
        );

        if (is_null($current_quiz))
            return view('play.no_available_quizzes');

        $correct_choices = $current_quiz->choices()->where('is_correct', 1)->get();

        // cache
        $ranking = Answer::getRanking($correct_choices)->get();

        if (is_null($ranking))
            return view('results.no_results');

        $filtered_ranking = $ranking->reject(function ($result, $rank) {
            return $rank >= 10 && $result->user->id !== auth()->user()->id;
        });
        unset($ranking);

        return view('results.results')
            ->with('results', $filtered_ranking);
    }

    public function globalResults()
    {

        if (!Answer::count())
            return view('results.no_results');

        $correct_choices = Choice::where('is_correct', 1)->get();

        // cache
        $ranking = Answer::getRanking($correct_choices)->get();


        // $filtered_ranking = $ranking->reject(function ($result, $rank) {
        //     return $rank >= 10 && $result->user->id !== auth()->user()->id;
        // });
        // unset($ranking);

        return view('results.global')
            ->with('results', $ranking);
        // ->with('results', $filtered_ranking);
    }
}
