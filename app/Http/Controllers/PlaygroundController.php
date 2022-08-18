<?php

namespace App\Http\Controllers;

use App\Services\QuizContextData;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class PlaygroundController extends Controller
{
    public function getQuizContext(QuizContextData $contextData): View|Factory
    {
        return match ($contextData->context) {
            QuizContextData::NO_QUIZZES_AVAILABLE => view('playground.no_available_quizzes'),
            QuizContextData::EARLY => view('playground.early')->with('seconds_to_wait', $contextData->secondsToQuizStart),
            QuizContextData::ENDED => view('playground.ended'),
            QuizContextData::LATE => view('playground.late'),
            QuizContextData::FINISHED => view('playground.finished'),
            default => view('playground.question')->with('question', $contextData->question),
        };
    }
}
