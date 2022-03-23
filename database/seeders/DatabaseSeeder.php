<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'nsvjon',
            'email' => 'y@y.y',
            'password' => password_hash('123', PASSWORD_DEFAULT),
        ]);

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
                'في السنة الرابعة من الهجرة',
            ],
            [
                'الملائكة',
                'ابراهيم عليه السلام',
                'اسماعيل عليه السلام',
                'اليهود',
            ],
            [
                'دار الندوة',
                'الكعبة',
                'دار النبي',
                'دار الصلاة',
            ],
            [
                'عمر بن الخطاب',
                'الرسول صلى الله عليه و سلم',
                'حمزة بن عبد المطلب',
                'بلال ابن رباح',
            ]
        ];
        foreach ($questions as $i => $question_content) {
            $question = $quiz->questions()->create([
                'content' => $question_content,
                'duration' => 30,
            ]);


            foreach ($choices[$i] as $j => $choice_content) {

                $question->choices()->create([
                    'content' => $choice_content,
                    'choice_number' => $j + 1 . "",
                    'is_correct' => $j === 0,
                ]);
            }
        }
    }
}
