<?php

namespace Database\Seeders;

use Database\Seeders\FullQuizSeed;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class QuizSeeder extends Seeder
{

    public function run(): void
    {
        FullQuizSeed::seed(15);
    }
}
