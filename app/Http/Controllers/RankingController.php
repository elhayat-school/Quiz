<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Choice;
use App\Services\CurrentQuiz;
use Illuminate\Support\Collection;

class RankingController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function currentQuizResults(CurrentQuiz $currentQuiz)
    {
        $current_quiz = $currentQuiz();

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

    /**
     * @return \Illuminate\Http\Response
     */
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
    //      Helpers
    /* ------------------------------------------------- */

    /**
     * TODO: IDK how to do the equivelent filtering in a query
     * ? Caching the $rankingList may compensate the downside of getting all results (less round trips)
     */
    private function limitRankingList(Collection $rankingList, int $limit = 10): Collection
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
