<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizManagerController extends Controller
{

    public const TOO_EARLY = "TOO_EARLY";
    public const PLAYING = "PLAYING";
    public const TOO_LATE = "TOO_LATE";

    public array $json = [];

    public function getQuestion()
    {
        // cover the begging when there is no quizzes
        $quiz = Quiz::with('questions.choices')->currentQuiz();

        $this->setJsonStartAt(strtotime($quiz->start_at));
        $this->setJsonDuration($quiz->duration);

        $this->presetResponseForQuizTimeContext();

        if ($this->getJsonStatus() !== self::PLAYING)
            return response()->json($this->json);

        // NOT AS SIMPLE AS THIS
        $this->json['body']['question'] = $quiz->questions->first();

        return response()->json($this->json);
    }

    /**
     * ...++++++++++(start_at)---(start_at + duration)----------...
     *
     * @param int $start_at Quiz starting timestamp in seconds
     * @param int $duration Quiz duration in seconds
     */
    private function presetResponseForQuizTimeContext(): void
    {
        $time_diff = $this->getJsonStartAt() - date(time()); // ! Timezone

        if ($time_diff > 0) {
            $this->setJsonSuccess(false);
            $this->setJsonStatus(self::TOO_EARLY);
        }
        //
        elseif ($time_diff >= -$this->getJsonDuration() && $time_diff <= 0) {
            $this->setJsonSuccess(true);
            $this->setJsonStatus(self::PLAYING);
        }
        //
        elseif ($time_diff < -$this->getJsonDuration()) {
            $this->setJsonSuccess(false);
            $this->setJsonStatus(self::TOO_LATE);
        }
    }


    /* ----------------------------------------- */
    //      SETTING & GETTING JSON
    /* ----------------------------------------- */
    private function setJsonSuccess(bool $success): void
    {
        $this->json['success'] = $success;
    }

    private function setJsonStatus(string $status): void
    {
        $this->json['status'] = $status;
    }

    private function setJsonStartAt(int $start_at): void
    {
        $this->json['body']['start_at'] = $start_at;
    }

    private function setJsonDuration(int $duration): void
    {
        $this->json['body']['duration'] = $duration;
    }

    private function getJsonSuccess(): bool
    {
        return $this->json['success'];
    }

    private function getJsonStatus(): string
    {
        return $this->json['status'];
    }

    private function getJsonStartAt(): int
    {
        return $this->json['body']['start_at'];
    }

    private function getJsonDuration(): int
    {
        return $this->json['body']['duration'];
    }
}
