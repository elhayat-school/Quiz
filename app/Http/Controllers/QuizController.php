<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    private string $super_security = 'pass';

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!isset($_GET['p']) || $_GET['p'] !== $this->super_security) {
            return response('unauthorized', 401);
        }

        return Quiz::with('questions.choices')->get();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!isset($_GET['p']) && $_GET['p'] !== $this->super_security) {
            return response('unauthorized', 401);
        }

        return view('quiz.create');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $quiz = Quiz::create([
            'start_at' => $request->start_at,
            'duration' => $request->duration,
        ]);

        for ($i = 1; $i < 5; $i++) {
            $question_data = $request->questions[$i];
            $choices = $question_data['choices'];

            $question = $quiz->questions()->create([
                'content' => $question_data['content'],
            ]);

            for ($j = 1; $j < 5; $j++) {
                $choice_content = $choices[$j];

                $choice = $question->choices()->create([
                    'content' => $choice_content,
                    'choice_number' => $j,
                    'is_correct' => $j == $question_data['is_correct'],
                ]);
            }
            // REDIRECT was here in the wtf commit
        }
        return to_route('quiz.index', ['p' => 'pass']);
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
