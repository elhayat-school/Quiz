<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizManagerController extends Controller
{
    public const NO_QUIZZES = "NO_QUIZZES";
    public const TOO_EARLY = "TOO_EARLY";
    public const PLAYING = "PLAYING";
    public const FINISHED = "FINISHED";
    public const TOO_LATE = "TOO_LATE";

    private $currentQuiz;
    public string $quizStatus;

    public function __construct()
    {
        $this->currentQuiz = Quiz::with('questions.choices')->notDone()->sortByOldestStartTime()->first();
        $this->setQuizStatus();
    }

    public function getQuestion()
    {
        if ($this->quizStatus === self::NO_QUIZZES)
            return view('play.no_available_quizzes');
        if ($this->quizStatus === self::TOO_EARLY)
            return view('play.early')
                ->with('seconds_to_wait', strtotime($this->currentQuiz->start_at) - time());
        if ($this->quizStatus === self::TOO_LATE)
            return view('play.late');
        /* ------------------------------------------------- */
        //      It's Quiz time
        /* ------------------------------------------------- */

        $answers = $this->currentQuiz->answers()->where('user_id', auth()->user()->id)->get();

        $questions_per_quiz = $this->currentQuiz->questions->count();

        if ($answers->count() === $questions_per_quiz) {
            if ((time() - strtotime($answers[$questions_per_quiz - 1]->served_at)) > $this->currentQuiz->questions->last()->duration
                || (!empty($answers[$questions_per_quiz - 1]->choice_number) &&
                    !empty($answers[$questions_per_quiz - 1]->received_at)
                )
            ) {
                $this->quizStatus = self::FINISHED;
                return view('play.finished');
            }
        }
        /* ------------------------------------------------- */
        //      User answered everything
        /* ------------------------------------------------- */

        $question = NULL;

        if (
            $answers->count() > 0 &&
            empty($answers->last()->choice_number) && empty($answers->last()->received_at) &&
            (time() - strtotime($answers->last()->served_at)) <= $this->currentQuiz->questions[$answers->count() - 1]->duration
        ) {
            /* ------------------------------------------------- */
            //   didn't answer his latest question AND still can
            /* ------------------------------------------------- */

            $question = $this->currentQuiz->questions[$answers->count() - 1];
        } else {
            /* ------------------------------------------------- */
            // prepare new question + placeholder answer(served_at)
            /* ------------------------------------------------- */

            $question = $this->currentQuiz->questions[$answers->count()];

            Answer::create([
                'user_id' => auth()->user()->id,
                'question_id' => $question->id,
                'served_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return view('play.question')->with('question', $question);

        // DONT SERVE ENTIRE MODELS !
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

    /**
     * ...++++++++++(start_at)---(start_at + duration)----------...
     */
    private function setQuizStatus(): void
    {
        if (is_null($this->currentQuiz)) {
            $this->quizStatus = self::NO_QUIZZES;
            return;
        }

        $time_diff = strtotime($this->currentQuiz->start_at) - time(); // ! Timezone

        if ($time_diff > 0)
            $this->quizStatus = self::TOO_EARLY;

        elseif ($time_diff >= -$this->currentQuiz->duration && $time_diff <= 0)
            $this->quizStatus = self::PLAYING;

        elseif ($time_diff < -$this->currentQuiz->duration)
            $this->quizStatus = self::TOO_LATE;
    }
}
