<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $validCategories = implode(',', config('ai.global.valid_categories', ['technical', 'commercial', 'billing', 'general', 'support']));
        $validSentiments = implode(',', config('ai.global.valid_sentiments', ['positive', 'negative', 'neutral']));

        return [
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:10|max:2000',
            'category' => 'nullable|string|max:100|in:' . $validCategories,
            'sentiment' => 'nullable|string|in:' . $validSentiments,
            'status' => 'nullable|string|in:open,closed,pending',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.min' => 'The title must be at least 5 characters.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.required' => 'The description field is required.',
            'description.min' => 'The description must be at least 10 characters.',
            'description.max' => 'The description may not be greater than 2000 characters.',
            'category.max' => 'The category may not be greater than 100 characters.',
            'sentiment.in' => 'The selected sentiment is invalid.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
