<?php

namespace App\Http\Controllers;

use App\Services\QuizContextData;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class PlaygroundController extends Controller
{
    public function getQuizContext(QuizContextData $contextData): View|Factory
    {
        switch ($contextData->context) {
            case QuizContextData::NO_QUIZZES_AVAILABLE:
                return view('playground.no_available_quizzes');

            case QuizContextData::EARLY:
                return view('playground.early')
                    ->with('seconds_to_wait', $contextData->secondsToQuizStart);

            case QuizContextData::ENDED:
                return view('playground.ended');

            case QuizContextData::LATE:
                return view('playground.late');

            case QuizContextData::FINISHED:
                return view('playground.finished');

            default:
                return view('playground.question')
                    ->with('question', $contextData->question);
        }
    }
}
