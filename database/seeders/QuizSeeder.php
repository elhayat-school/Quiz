<?php

namespace Database\Seeders;

use App\Services\FullQuizInsertion;
use App\Services\QuizExamples;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{

    public function run(): void
    {
        $insertor = new FullQuizInsertion;

        $examples = new QuizExamples;

        $insertor->insert($examples->example1());
    }
}
