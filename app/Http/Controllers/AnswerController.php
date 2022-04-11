<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecordChoiceRequest;
use App\Services\CurrentQuiz;
use  Illuminate\Http\RedirectResponse;

class AnswerController extends Controller
{
    public int $currentTimestamp;
    private $currentQuiz;

    public function __construct(CurrentQuiz $currentQuiz)
    {
        $this->currentTimestamp = time();
        $this->currentQuiz = $currentQuiz();
    }

    public function recordChoice(RecordChoiceRequest $request): RedirectResponse
    {
        $question = $this->currentQuiz->questions->where('id', $request->question_id)->first();

        $answer = $question->answers()
            ->where('user_id', auth()->user()->id)
            ->firstOrfail();

        if (
            $this->currentTimestamp - strtotime($answer->served_at) <= $question->duration &&
            empty($answer->choice_number)
        ) {

            $answer->choice_number = $request->choice_number;
            $answer->received_at = date('Y-m-d H:i:s',  $this->currentTimestamp);

            $answer->save();
        }

        return to_route('playground');
    }
}
