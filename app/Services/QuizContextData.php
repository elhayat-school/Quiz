<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Services\CurrentQuiz;
use Illuminate\Support\Collection;

class QuizContextData
{
    /**
     * @var int
     */
    public int $currentTimestamp;

    /**
     * @var Quiz|null
     */
    private $currentQuiz;

    /**
     * @var int
     */
    public int $secondsToQuizStart;

    const NO_QUIZZES_AVAILABLE = 'NO_QUIZZES_AVAILABLE';
    const EARLY = 'EARLY';
    const ENDED = 'ENDED';
    const LATE = 'LATE';
    const FINISHED = 'FINISHED';

    public string $context = '';
    public $question = NULL;

    public function __construct(CurrentQuiz $currentQuiz)
    {
        $this->currentTimestamp = time();
        $this->currentQuiz = $currentQuiz();

        $this->setQuizContextData();
    }

    public function setQuizContextData(): void
    {
        if (!$this->currentQuiz) {
            $this->context = self::NO_QUIZZES_AVAILABLE;
            return;
        }

        $answers = $this->currentQuiz->answers()
            ->where('user_id', auth()->user()->id)
            ->get();

        // * ...++++++++++(start_at)---(start_at + duration)----------...
        $this->secondsToQuizStart = strtotime($this->currentQuiz->start_at) - $this->currentTimestamp; // ! Timezone

        if ($this->secondsToQuizStart > 0) {
            $this->context = self::EARLY;
            return;
        } elseif (
            $this->secondsToQuizStart < -$this->currentQuiz->duration
            && $this->firstTimeRequestingQuestion($answers)
        ) {
            $this->context = self::LATE;
            return;
        }

        /* ------------------------------------------------- */
        //      It's Quiz time
        /* ------------------------------------------------- */
        // secondsToQuizStart = [-QUIZ_DURATION - 0] (NEGATIVE INT) -> secondsSinceQuizStart (abs)

        if (
            !config('quiz.QUIZ_ALLOW_DELAY') &&
            $this->firstTimeRequestingQuestion($answers) &&
            ($this->secondsSinceQuizStart() > config('quiz.QUIZ_MAX_DELAY'))
        ) {
            $this->context = self::ENDED;
            return;
        }

        if (
            $this->reachedLastQuestion($answers) &&
            (!$this->hasSparedTimeForLatestAnswer($answers) ||
                $this->filledLatestAnswer($answers)
            )
        ) {
            $this->context = self::FINISHED;
            return;
        }

        $this->question = $this->pickQuestion($answers);
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

    private function pickQuestion(Collection $answers): Question
    {
        $question = NULL;
        $must_wait_countdown = false;

        if (
            !$this->firstTimeRequestingQuestion($answers) && // prevent negative answer index
            $this->hasSparedTimeForLatestAnswer($answers)
        ) {

            // !
            $this->currentQuiz->questions[$answers->count() - 1]->duration = $this->currentQuiz->questions[$answers->count() - 1]->duration - ($this->currentTimestamp - strtotime($answers->last()->served_at)); // Set the spared time

            // Reset previous question
            $question = $this->currentQuiz->questions[$answers->count() - 1];

            if ($this->filledLatestAnswer($answers))
                $must_wait_countdown = true;
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

        $question->mustWaitCountdown = $must_wait_countdown;

        return $question;
    }

    /**
     * @throws \Exception when using for positive amount of seconds
     */
    public function secondsSinceQuizStart(): int
    {
        if ($this->secondsToQuizStart > 0)
            throw new \Exception('bad lexics usage', 1);

        return abs($this->secondsToQuizStart);
    }
}
