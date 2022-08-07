<?php

namespace Database\Seeders;

use App\Services\FullQuizInsertion;

class FullQuizSeed
{
    public static function seed(int $seconds_offset = 0)
    {
        $ins = new FullQuizInsertion;
        $quiz_seed = new self;
        $quiz_example1 = $quiz_seed->example1($seconds_offset);

        $ins->insert($quiz_example1);

        return $quiz_example1;
    }

    public function example1(int $seconds_offset = 0): array
    {
        return [
            'start_at' => date('Y-m-d H:i:s', time() + $seconds_offset),
            'questions' => [
                [
                    'content' => 'في أي عام فُرِض الصيام على المسلمين',
                    'is_correct' => '1',
                    'choices' => [
                        1 => 'في السنة الثانية من الهجرة',
                        2 => 'في السنة الأولى من الهجرة',
                        3 => 'في السنة الثالثة من الهجرة',
                        // 4=> 'في السنة الرابعة من الهجرة',
                    ],
                ],
                [
                    'content' => 'من هو أول من قام بالطواف حول البيت العتيق؟',
                    'is_correct' => '4',
                    'choices' => [
                        1 => 'ابراهيم عليه السلام',
                        2 => 'اسماعيل عليه السلام',
                        3 => 'اليهود',
                        4 => 'الملائكة',
                    ],
                ],
                [
                    'content' => 'ما اسم أول دار تم بناؤها في مكة المكرمة؟',
                    'is_correct' => '1',
                    'choices' => [
                        1 => 'دار الندوة',
                        2 => 'الكعبة',
                        3 => 'دار النبي',
                        // 4=> 'دار الصلاة',
                    ],
                ],
                [
                    'content' => 'من الذي جمع الناس لصلاة التراويح ؟',
                    'is_correct' => '1',
                    'choices' => [
                        1 => 'عمر بن الخطاب',
                        2 => 'الرسول صلى الله عليه و سلم',
                        3 => 'حمزة بن عبد المطلب',
                        // 4=> 'بلال ابن رباح',
                    ],
                ],

            ],
        ];
    }
}
