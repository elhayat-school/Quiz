<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ==========================================================

        $quiz = Quiz::create([
            'start_at' => date('Y-m-d H:i:s', time() + 15),
            'duration' => 140
        ]);
        $questions = [
            'في أي عام فُرِض الصيام على المسلمين',
            'من هو أول من قام بالطواف حول البيت العتيق؟',
            'ما اسم أول دار تم بناؤها في مكة المكرمة؟',
            'من الذي جمع الناس لصلاة التراويح ؟',

        ];
        $choices = [
            [
                'في السنة الثانية من الهجرة',
                'في السنة الأولى من الهجرة',
                'في السنة الثالثة من الهجرة',
                // 'في السنة الرابعة من الهجرة',
            ],
            [
                'الملائكة',
                'ابراهيم عليه السلام',
                'اسماعيل عليه السلام',
                // 'اليهود',
            ],
            [
                'دار الندوة',
                'الكعبة',
                'دار النبي',
                // 'دار الصلاة',
            ],
            [
                'عمر بن الخطاب',
                'الرسول صلى الله عليه و سلم',
                'حمزة بن عبد المطلب',
                // 'بلال ابن رباح',
            ]
        ];
        foreach ($questions as $i => $question_content) {
            $question = $quiz->questions()->create([
                'content' => $question_content,
                'duration' => config('quiz.QUESTION_DEFAULT_DURATION'),
            ]);


            foreach ($choices[$i] as $j => $choice_content) {

                $question->choices()->create([
                    'content' => $choice_content,
                    'choice_number' => $j + 1 . "",
                    'is_correct' => $j === 0,
                ]);
            }
        }

        // ==========================================================
    }
}
