<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_at' => [
                'required',
                // 'date_format:Y-m-d H:i:s'
                // 'date_format:Y-m-d H:i'
            ],
            'questions.*.content' => ['required', 'min:1', 'max:100'],
            'questions.*.is_correct' => ['required', 'integer', 'between:1,4'],
            'questions.*.choices.*' => ['nullable', 'min:1', 'max:100'],
            // 'questions.*.choices.1' => ['require'],
            // 'questions.*.choices.2' => ['require'],
            // 'questions.*.choices.3' => ['require'],
            // 'questions.*.choices.4' => ['nullable'],
        ];
    }
}
