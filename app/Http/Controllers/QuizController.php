<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Choice;
use App\Models\Quiz;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(): View|Factory
    {
        return view("quiz.index")
            ->with('quizzes', Quiz::with('questions.choices')->oldest('start_at')->get());
    }

    public function create(): View|Factory
    {
        return view('quiz.create');
    }

    public function store(Request $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $quiz = Quiz::create(['start_at' => $request->start_at, 'duration' => $request->duration]);
            $questions = $quiz->questions()->createMany($request->questions);
            $this->insertChoices($questions, $request->questions);
        });

        return to_route('quizzes.index');
    }

    public function cacheParticipationStats(Quiz $quiz): RedirectResponse
    {
        $establishments = config('quiz.ESTABLISHMENTS');
        // $establishments = DB::table('users')
        //     ->whereNotNull('establishment')
        //     ->groupBy('establishment')
        //     ->pluck('establishment');

        $str = '';
        foreach ($establishments as $establishment) {
            $count = Answer::getEstablishmentParticipation($quiz->id, $establishment);

            if ($count > 0)
                $str .= "$establishment:$count-";
        }

        $quiz->update(['participation_stats' => $str]);

        return back();
    }

    public function markAsDone(Request $request, Quiz $quiz): RedirectResponse
    {
        $quiz->update(['done' => $request->new_state === "done"]);

        return back();
    }

    /* ------------------------------------------------- */
    //      HELPERS
    /* ------------------------------------------------- */
    private function insertChoices($questions, $reqQuestions)
    {
        $choices = [];
        foreach ($reqQuestions as $i => $question_data) {
            foreach ($question_data['choices'] as $j => $choice_content) {
                $choices[] = [
                    'question_id' => $questions[$i - 1]->id, // Append foreign id
                    'content' => $choice_content,
                    'choice_number' => "$j",
                    'is_correct' => "$j" === $question_data['is_correct'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        Choice::insert($choices);
    }
}
