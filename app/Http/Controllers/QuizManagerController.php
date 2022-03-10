<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizManagerController extends Controller
{
    public function getQuestion()
    {
        $lorem = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

        return response()->json(
            [
                'question' => [
                    'content' => substr($lorem, rand(30, 50), rand(60, 80)),
                    'choices' => [
                        ['nb' => 1, 'content' => substr($lorem, rand(150, 170), rand(180, 200))],
                        ['nb' => 2, 'content' => substr($lorem, rand(120, 140), rand(150, 170))],
                        ['nb' => 3, 'content' => substr($lorem, rand(200, 220), rand(230, 250))],
                        ['nb' => 4, 'content' => substr($lorem, rand(100, 120), rand(130, 150))],
                    ]
                ],
            ]
        );
    }

    private function getStartAt()
    {
        Quiz::where('done', false)
        ->orderBy('order', 'desc')->first();
    }

}
