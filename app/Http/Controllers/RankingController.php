<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Choice;
use App\Models\Quiz;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $results = Answer::with('user')
            ->select('user_id')
            ->addSelect(DB::raw('SUM(UNIX_TIMESTAMP(received_at) - UNIX_TIMESTAMP(served_at)) AS sum_elapsed_seconds'))
            ->addSelect(DB::raw('COUNT(DISTINCT question_id) AS count_correct_answers'))
            ->filterCorrectChoices($correct_choices)
            ->orderBy('count_correct_answers', 'DESC')
            ->orderBy('sum_elapsed_seconds')
            ->groupBy('user_id')
            ->get();

        if (is_null($results))
            return view('results.no_results');

        $filtered_results = $results->reject(function ($result, $rank) {
            return $rank >= 10 && $result->user->id !== auth()->user()->id;
        });
        unset($results);

        return view('results.results')
            ->with('results', $filtered_results);
    }

    public function globalResults()
    {

        if (!Answer::count())
            return view('results.no_results');

        $correct_choices = Choice::where('is_correct', 1)->get();

        // cache
        $results = Answer::with('user')
            ->select('user_id')
            ->addSelect(DB::raw('SUM(UNIX_TIMESTAMP(received_at) - UNIX_TIMESTAMP(served_at)) AS sum_elapsed_seconds'))
            ->addSelect(DB::raw('COUNT(DISTINCT question_id) AS count_correct_answers'))
            ->filterCorrectChoices($correct_choices)
            ->orderBy('count_correct_answers', 'DESC')
            ->orderBy('sum_elapsed_seconds')
            ->groupBy('user_id')
            ->get();


        // $filtered_results = $results->reject(function ($result, $rank) {
        //     return $rank >= 10 && $result->user->id !== auth()->user()->id;
        // });
        // unset($results);

        return view('results.global')
            ->with('results', $results);
        // ->with('results', $filtered_results);
    }
}
