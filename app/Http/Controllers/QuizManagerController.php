<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizManagerController extends Controller
{
    public const MAX_START_DELAY = 25;

    public int $currentTimestamp;

    private $currentQuiz;


    public function __construct()
    {
        $this->currentQuiz = Quiz::with('questions.choices')->notDone()->sortByOldestStartTime()->first();
        $this->currentTimestamp = time();
    }

    public function getQuestion()
    {
        // * ...++++++++++(start_at)---(start_at + duration)----------...

        if (is_null($this->currentQuiz))
            return view('play.no_available_quizzes');

        $time_diff = strtotime($this->currentQuiz->start_at) - $this->currentTimestamp; // ! Timezone

        if ($time_diff > 0)
            return view('play.early')
                ->with('seconds_to_wait', strtotime($this->currentQuiz->start_at) - $this->currentTimestamp);

        // ($time_diff >= -$this->currentQuiz->duration && $time_diff <= 0)

        elseif ($time_diff < -$this->currentQuiz->duration)
            return view('play.late');

        /* ------------------------------------------------- */
        //      It's Quiz time
        /* ------------------------------------------------- */

        $answers = $this->currentQuiz->answers()
            ->where('user_id', auth()->user()->id)
            ->get();

        if ($answers->count() === 0 && ($this->currentTimestamp - strtotime($this->currentQuiz->start_at) > self::MAX_START_DELAY))
            return view('play.late');

        if ($this->finishedAllQuestions($answers))
            return view('play.finished');

        $question = NULL;

        if ($this->canAnswerPreviouslyServedQuestion($answers)) {

            //  !
            $this->currentQuiz->questions[$answers->count() - 1]->duration = $this->currentQuiz->questions[$answers->count() - 1]->duration - ($this->currentTimestamp - strtotime($answers->last()->served_at)); // Set the spared time

            // Reset previous question
            $question = $this->currentQuiz->questions[$answers->count() - 1];
        } else {

            // Set new question
            $question = $this->currentQuiz->questions[$answers->count()];

            // placeholder answer(served_at)
            Answer::create([
                'user_id' => auth()->user()->id,
                'question_id' => $question->id,
                'served_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $quiz_remaining_time = $this->currentQuiz->duration - ($this->currentTimestamp - strtotime($this->currentQuiz->start_at));
        if ($question->duration > $quiz_remaining_time)
            $question->duration = $quiz_remaining_time;

        return view('play.question')
            // ->with('quiz_remaining_time', $quiz_remaining_time)
            ->with('question', $question);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postAnswer(Request $request)
    {
        $question = $this->currentQuiz->questions->filter(function ($question) use ($request) {
            return $question->id == $request->question_id;
        })->first();

        $answer = Answer::where('user_id', auth()->user()->id)
            ->where('question_id', $question->id)
            ->firstOrfail();

        if ($this->currentTimestamp - strtotime($answer->served_at) <= $question->duration) {
            $answer->choice_number = $request->choice_number;
            $answer->received_at = date('Y-m-d H:i:s',  $this->currentTimestamp);
            $answer->save();
        }

        return to_route('playground');
    }

    /**
     * The player has finished the current Quiz if:
     * - Every question has a recorded answer (placeholder)
     * +  **AND**
     * -  -  Time to fill  the last answer elapsed
     * -  +  **OR**
     * -  -  Last answer is filled
     *
     * @param \Illuminate\Database\Eloquent\Collection $answers Authenticated User answers for the current Quiz
     */
    private function finishedAllQuestions(\Illuminate\Database\Eloquent\Collection $answers): bool
    {
        if ($answers->count() < $this->currentQuiz->questions->count())
            // Didn't get to the last question
            return false;

        if (($this->currentTimestamp - strtotime($answers->last()->served_at)) >= $this->currentQuiz->questions->last()->duration)
            // Time for last question elapsed
            return true;

        if (!empty($answers->last()->choice_number) && !empty($answers->last()->received_at))
            // Last question is answered
            return true;

        return false;
    }

    /**
     * - Have some answers (placeholder|filled) recorded for the current Quiz
     * + **AND**
     * - Didn't fill the Answer of the priviously served Question
     * + **AND**
     * - Still have remaining time to answer the priviously served Question
     *
     * @param \Illuminate\Database\Eloquent\Collection $answers Authenticated User answers for the current Quiz
     */
    private function canAnswerPreviouslyServedQuestion(\Illuminate\Database\Eloquent\Collection $answers): bool
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

        $answer_elapsed_time =  $this->currentTimestamp - strtotime($answers->last()->served_at);
        $previously_served_question_duration = $this->currentQuiz->questions[$answers->count() - 1]->duration;

        if ($answer_elapsed_time <= $previously_served_question_duration)
            // Still has spared time to answer
            return true;

        return false;
    }
}
