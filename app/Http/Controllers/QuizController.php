<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!password_verify($_GET['p'], '$2y$10$QNTo7dxm9n7xq.JGyr03EOcdUWEV/OtMdk142MxBkEvBKIkRhXQCS')) {
            return response('unauthorized', 401);
        }
        return view('quiz.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dump($request->all());
        $quiz = Quiz::create(['start_at' => $request->start_at]);

        // dump('quiz', $quiz->toArray());

        for ($i = 1; $i < 5; $i++) {
            $question_data = $request->questions[$i];
            $choices = $question_data['choices'];

            $question = $quiz->questions()->create([
                'content' => $question_data['content'],

            ]);

            dump('question', $question->toArray());

            for ($j = 1; $j < 5; $j++) {
                $choice_content = $choices[$j];

                $choice = $question->choices()->create([
                    'content' => $choice_content,
                    'choice_number' => $j,
                    'is_correct' => $j == $question_data['is_correct'],
                ]);

                dump($j, 'choice', $choice->toArray());
            }
            echo '<hr/><hr/><hr/>';
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Quiz $quiz)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function edit(Quiz $quiz)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quiz $quiz)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz)
    {
        //
    }
}
