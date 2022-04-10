<?php

namespace App\Services;

use App\Models\Quiz;
use Illuminate\Support\Facades\Cache;

class CurrentQuiz
{
    private $currentQuiz;

    public function __construct()
    {
        $this->currentQuiz = Cache::remember(
            'current_quiz',
            10,
            fn () => Quiz::with('questions.choices')->notDone()->sortByOldestStartTime()->first()
        );
    }

    public function __invoke(): Quiz|null
    {
        return $this->currentQuiz;
    }
}
