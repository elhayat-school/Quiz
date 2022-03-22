<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizManagerController extends Controller
{
    private $currentQuiz;

    public function __construct()
    {
        $this->currentQuiz = Quiz::with('questions.choices')->notDone()->sortByOldestStartTime()->first();
    }

    public function getQuestion()
    {
        // * ...++++++++++(start_at)---(start_at + duration)----------...

        if (is_null($this->currentQuiz))
            return view('play.no_available_quizzes');

        $time_diff = strtotime($this->currentQuiz->start_at) - time(); // ! Timezone

        if ($time_diff > 0)
            return view('play.early')
                ->with('seconds_to_wait', strtotime($this->currentQuiz->start_at) - time());

        // elseif ($time_diff >= -$this->currentQuiz->duration && $time_diff <= 0)

        elseif ($time_diff < -$this->currentQuiz->duration)
            return view('play.late');

        /* ------------------------------------------------- */
        //      It's Quiz time
        /* ------------------------------------------------- */

        $answers = $this->currentQuiz->answers()
            ->where('user_id', auth()->user()->id)
            ->get();

        if ($this->finishedAllQuestions($answers))
            return view('play.finished');

        $question = NULL;

        if ($this->canAnswerPreviouslyServedQuestion($answers)) {

            //  !
            $this->currentQuiz->questions[$answers->count() - 1]->duration = $this->currentQuiz->questions[$answers->count() - 1]->duration - (time() - strtotime($answers->last()->served_at)); // Set the spared time

            $question = $this->currentQuiz->questions[$answers->count() - 1];
        } else {
            // prepare new question + placeholder answer(served_at)

            $question = $this->currentQuiz->questions[$answers->count()];

            Answer::create([
                'user_id' => auth()->user()->id,
                'question_id' => $question->id,
                'served_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $quiz_remaining_time = $this->currentQuiz->duration - (time() - strtotime($this->currentQuiz->start_at));
        if ($question->duration > $quiz_remaining_time)
            $question->duration = $quiz_remaining_time;

        return view('play.question')->with('question', $question);
    }

    public function postAnswer(Request $request)
    {
        $received_at = time();

        $question = $this->currentQuiz->questions->filter(function ($question) use ($request) {
            return $question->id == $request->question_id;
        })->first();

        $answer = Answer::where('user_id', auth()->user()->id)
            ->where('question_id', $question->id)
            ->firstOrfail();

        if ($received_at - strtotime($answer->served_at) <= $question->duration) {
            $answer->choice_number = $request->choice_number;
            $answer->received_at = date('Y-m-d H:i:s', $received_at);
            $answer->save();
        }

        return to_route('playground');
    }

    private function finishedAllQuestions($answers): bool
    {
        if ($answers->count() < $this->currentQuiz->questions->count())
            // Didn't answer every question
            return false;

        if ((time() - strtotime($answers->last()->served_at)) >= $this->currentQuiz->questions->last()->duration)
            // Time for last question elapsed
            return true;

        if (!empty($answers->last()->choice_number) && !empty($answers->last()->received_at))
            // Last question is answered
            return true;

        return false;
    }

    private function canAnswerPreviouslyServedQuestion($answers): bool
    {
        if ($answers->count() === 0)
            // First time requesting a question
            return false;

        if (
            !empty($answers->last()->choice_number) &&
            !empty($answers->last()->received_at)
        )
            // Did answer the latest question he got served
            return false;

        $answer_elapsed_time = time() - strtotime($answers->last()->served_at);
        $previously_served_question_duration = $this->currentQuiz->questions[$answers->count() - 1]->duration;

        if ($answer_elapsed_time <= $previously_served_question_duration)
            // Still has spared time to answer
            return true;

        return false;
    }
}
