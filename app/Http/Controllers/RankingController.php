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

        $ranking = $this->limitRankingList($ranking);

        return view('results.results')
            ->with('results', $ranking);
    }

    public function globalResults()
    {

        if (!Answer::count())
            return view('results.no_results');

        $correct_choices = Choice::where('is_correct', 1)->get();

        // cache
        $ranking = Answer::getRanking($correct_choices)->get();

        // $ranking = $this->limitRankingList($ranking, 5);

        return view('results.global')
            ->with('results', $ranking);
    }

    /* ------------------------------------------------- */
    //      ******************
    /* ------------------------------------------------- */

    /**
     * TODO: IDK how to do the equivelent filtering in a query
     */
    private function limitRankingList(\Illuminate\Database\Eloquent\Collection $rankingList, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($limit) || $limit <= 0)
            throw new \Exception('No valid limit property on the rankingList collection', 1);

        $tempCollection = $rankingList->reject(function ($result, $rank) use ($limit) {
            return $rank >= $limit && $result->user->id !== auth()->user()->id;
        });

        $tempCollection->limit = $limit; // Set the limit property to reuse in the view

        return $tempCollection;
    }
}
