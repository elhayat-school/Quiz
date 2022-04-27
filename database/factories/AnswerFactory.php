<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
 */
class AnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'served_at' => date('Y-m-d H:i:s', now()->timestamp),
        ];
    }

    public function answered()
    {
        return $this->state(function (array $attributes) {
            return [
                'choice_number' => rand(1, 4),
                'received_at' => date('Y-m-d H:i:s', now()->timestamp + rand(5, config('quiz.QUESTION_DEFAULT_DURATION'))),
            ];
        });
    }

    /**
     * 1 second to answer
     */
    public function receivedInstaniously()
    {
        return $this->state(function (array $attributes) {
            return [
                'served_at' => date('Y-m-d H:i:s', now()->timestamp),
                'received_at' => date('Y-m-d H:i:s', now()->timestamp + 1),
            ];
        });
    }
}
