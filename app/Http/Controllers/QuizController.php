<?php

namespace App\Http\Controllers;

use App\Models\Choice;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("quiz.index")
            ->with('quizzes', Quiz::with('questions.choices')->get());
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('quiz.create');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $quiz = Quiz::create(['start_at' => $request->start_at, 'duration' => $request->duration]);
            $questions = $quiz->questions()->createMany($request->questions);

            $choices = [];
            foreach ($request->questions as $i => $question_data) {
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
        });
        return to_route('quiz.index', ['_p' => get_weak_auth_hashed_password()]);
    }

    /**
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Quiz $quiz)
    {
        //
    }

    /**
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function edit(Quiz $quiz)
    {
        //
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quiz $quiz)
    {
        //
    }

    /**
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz)
    {
        //
    }
}
