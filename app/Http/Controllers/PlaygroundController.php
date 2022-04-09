<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Services\CurrentQuiz;
use Illuminate\Support\Collection;

class PlaygroundController extends Controller
{
    public int $currentTimestamp;

    private $currentQuiz;

    private $secondsToQuizStart;

    public function __construct(CurrentQuiz $currentQuiz)
    {
        $this->currentQuiz = $currentQuiz();

        $this->currentTimestamp = time();
    }

    public function getQuizContext()
    {

        if (is_null($this->currentQuiz))
            return view('play.no_available_quizzes');

        // * ...++++++++++(start_at)---(start_at + duration)----------...
        $this->secondsToQuizStart = strtotime($this->currentQuiz->start_at) - $this->currentTimestamp; // ! Timezone

        if ($this->secondsToQuizStart > 0)
            return view('play.early')
                ->with('seconds_to_wait', $this->secondsToQuizStart);

        elseif ($this->secondsToQuizStart < -$this->currentQuiz->duration)
            return view('play.ended');

        /* ------------------------------------------------- */
        //      It's Quiz time
        /* ------------------------------------------------- */
        // secondsToQuizStart = [-QUIZ_DURATION - 0] (NEGATIVE INT) -> secondsSinceQuizStart (abs)

        $answers = $this->currentQuiz->answers()
            ->where('user_id', auth()->user()->id)
            ->get();

        if (
            !config('quiz.QUIZ_ALLOW_DELAY') &&
            $this->firstTimeRequestingQuestion($answers) &&
            ($this->secondsSinceQuizStart() > config('quiz.QUIZ_MAX_DELAY'))
        )
            return view('play.late');

        if (
            $this->reachedLastQuestion($answers) &&
            (!$this->hasSparedTimeForLatestAnswer($answers) ||
                $this->filledLatestAnswer($answers)
            )
        )
            return view('play.finished');

        $question = NULL;
        $readonly_countdown = false;

        if (
            !$this->firstTimeRequestingQuestion($answers) && // prevent negative answer index
            $this->hasSparedTimeForLatestAnswer($answers)
        ) {

            // !
            $this->currentQuiz->questions[$answers->count() - 1]->duration = $this->currentQuiz->questions[$answers->count() - 1]->duration - ($this->currentTimestamp - strtotime($answers->last()->served_at)); // Set the spared time

            // Reset previous question
            $question = $this->currentQuiz->questions[$answers->count() - 1];

            if ($this->filledLatestAnswer($answers))
                $readonly_countdown = true;
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

        $quiz_remaining_time = $this->currentQuiz->duration - $this->secondsSinceQuizStart();

        if ($question->duration > $quiz_remaining_time)
            $question->duration = $quiz_remaining_time;

        return view('play.question')
            // ->with('quiz_remaining_time', $quiz_remaining_time)
            ->with('readonly_countdown', $readonly_countdown)
            ->with('question', $question);
    }

    /* ------------------------------------------------- */
    //      Micro conditions
    /* ------------------------------------------------- */

    /**
     * @var Illuminate\Support\Collection $answers
     * @return bool
     */
    private function firstTimeRequestingQuestion(Collection $answers): bool
    {
        return $answers->count() === 0;
    }

    /**
     * @var Illuminate\Support\Collection $answers
     * @return bool
     */
    private function filledLatestAnswer(Collection $answers): bool
    {
        return !empty($answers->last()->choice_number) && !empty($answers->last()->received_at);
    }

    /**
     * @var Illuminate\Support\Collection $answers
     * @return bool
     */
    private function hasSparedTimeForLatestAnswer(Collection $answers): bool
    {
        $answer_elapsed_time =  $this->currentTimestamp - strtotime($answers->last()->served_at);
        $previously_served_question_duration = $this->currentQuiz->questions[$answers->count() - 1]->duration;

        return $answer_elapsed_time <= $previously_served_question_duration;
    }

    /**
     * @var Illuminate\Support\Collection $answers
     * @return bool
     */
    private function reachedLastQuestion(Collection $answers): bool
    {
        if ($answers->count() > $this->currentQuiz->questions->count())
            throw new \Exception('Check the junk code you wrote in reachedLastQuestion', 1);

        return $answers->count() === $this->currentQuiz->questions->count();
    }

    /* ------------------------------------------------- */
    //      Helpers
    /* ------------------------------------------------- */

    public function secondsSinceQuizStart(): int
    {
        if ($this->secondsToQuizStart > 0)
            throw new \Exception('bad lexics usage', 1);

        return abs($this->secondsToQuizStart);
    }
}
