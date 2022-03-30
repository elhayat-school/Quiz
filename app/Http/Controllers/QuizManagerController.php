<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QuizManagerController extends Controller
{
    public int $currentTimestamp;

    private $currentQuiz;

    public function __construct()
    {
        $this->currentQuiz = Cache::remember(
            'current_quiz',
            10,
            fn () => Quiz::with('questions.choices')->notDone()->sortByOldestStartTime()->first()
        );

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

        if (
            !config('quiz.QUIZ_ALLOW_DELAY') &&
            $answers->count() === 0 &&
            ($this->currentTimestamp - strtotime($this->currentQuiz->start_at) > config('quiz.QUIZ_MAX_DELAY'))
        )
            return view('play.late');

        if ($this->finishedAllQuestions($answers))
            return view('play.finished');

        $question = NULL;
        $readonly_countdown = NULL;

        if (
            !$this->firstTimeRequestingQuestion($answers) &&
            $this->hasSparedTimeToAnswer($answers)
        ) {

            if (!$this->filledLatestAnswer($answers)) {

                // !
                $this->currentQuiz->questions[$answers->count() - 1]->duration = $this->currentQuiz->questions[$answers->count() - 1]->duration - ($this->currentTimestamp - strtotime($answers->last()->served_at)); // Set the spared time

                // Reset previous question
                $question = $this->currentQuiz->questions[$answers->count() - 1];
                $readonly_countdown = false; // !

            } else {
                // !
                $this->currentQuiz->questions[$answers->count() - 1]->duration = $this->currentQuiz->questions[$answers->count() - 1]->duration - ($this->currentTimestamp - strtotime($answers->last()->served_at)); // Set the spared time

                // Reset previous question
                $question = $this->currentQuiz->questions[$answers->count() - 1];
                $readonly_countdown = true; // !
            }
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
            ->with('readonly_countdown', $readonly_countdown)
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

    public function getResults()
    {

        if (is_null($this->currentQuiz))
            return view('play.no_available_quizzes');

        $correct_choices = $this->currentQuiz->choices()->where('is_correct', 1)->get();

        // cache
        $results = Answer::with('user')
            ->select('user_id')
            ->addSelect(DB::raw('SUM(UNIX_TIMESTAMP(received_at) - UNIX_TIMESTAMP(served_at)) AS sum_elapsed_seconds'))
            ->addSelect(DB::raw('COUNT(DISTINCT question_id) AS count_correct_answers'))
            ->filterCorrectChoices($correct_choices)
            ->orderBy('count_correct_answers', 'DESC')
            ->orderBy('sum_elapsed_seconds')
            ->groupBy('user_id')
            ->get();

        if (is_null($results))
            return view('play.no_results');

        $filtered_results = $results->reject(function ($result, $rank) {
            return $rank >= 10 && $result->user->id !== auth()->user()->id;
        });
        unset($results);

        return view('play.results')
            ->with('results', $filtered_results);
    }

    /* ------------------------------------------------- */
    //      Context conditions
    /* ------------------------------------------------- */

    /**
     * @param \Illuminate\Database\Eloquent\Collection $answers Authenticated User answers for the current Quiz
     */
    private function finishedAllQuestions(\Illuminate\Database\Eloquent\Collection $answers): bool
    {
        if (!$this->receivedLastQuestion($answers))
            return false;

        if ($this->timeForLastAnswerElapsed($answers))
            return true;

        if (!empty($answers->last()->choice_number) && !empty($answers->last()->received_at))
            // Last question is answered
            return true;

        return false;
    }

    /* ------------------------------------------------- */
    //      Micro conditions
    /* ------------------------------------------------- */

    private function firstTimeRequestingQuestion(\Illuminate\Database\Eloquent\Collection $answers): bool
    {
        return $answers->count() === 0;
    }

    private function filledLatestAnswer(\Illuminate\Database\Eloquent\Collection $answers): bool
    {
        return !empty($answers->last()->choice_number) && !empty($answers->last()->received_at);
    }

    private function hasSparedTimeToAnswer(\Illuminate\Database\Eloquent\Collection $answers): bool
    {
        $answer_elapsed_time =  $this->currentTimestamp - strtotime($answers->last()->served_at);
        $previously_served_question_duration = $this->currentQuiz->questions[$answers->count() - 1]->duration;

        return $answer_elapsed_time <= $previously_served_question_duration;
    }

    private function timeForLastAnswerElapsed(\Illuminate\Database\Eloquent\Collection $answers): bool
    {
        return ($this->currentTimestamp - strtotime($answers->last()->served_at)) >= $this->currentQuiz->questions->last()->duration;
    }

    private function receivedLastQuestion(\Illuminate\Database\Eloquent\Collection $answers): bool
    {
        if ($answers->count() > $this->currentQuiz->questions->count())
            throw new \Exception('Check the junk code you wrote in receivedLastQuestion', 1);

        return $answers->count() === $this->currentQuiz->questions->count();
    }
}
