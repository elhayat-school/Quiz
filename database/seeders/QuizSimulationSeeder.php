<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSimulationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('email', '<>', 'y@y.y')->get();

        $currentQuiz = Quiz::with('questions.choices')->notDone()->sortByOldestStartTime()->first();

        $answers = [];

        foreach ($currentQuiz->questions as $question) {
            $served_at = time();
            foreach ($users as $user) {
                $answer = [
                    'user_id' => $user->id,
                    'question_id' => $question->id,
                    'choice_number' => random_int(1, 4).'',
                    'served_at' => date('Y-m-d H:i:s', $served_at),
                    'received_at' => date('Y-m-d H:i:s', $served_at + random_int(5, $question->duration)),
                ];
                $answers[] = $answer;
            }
        }
        echo DB::table('answers')->insert($answers);
    }
}
