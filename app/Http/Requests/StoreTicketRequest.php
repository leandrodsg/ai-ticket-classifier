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
        return [
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:10|max:5000',
            'category' => 'nullable|string|max:100',
            'sentiment' => 'nullable|string|in:positive,negative,neutral',
            'status' => 'nullable|string|in:open,closed,pending',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'O título do ticket é obrigatório.',
            'title.min' => 'O título deve ter pelo menos 5 caracteres.',
            'title.max' => 'O título não pode exceder 255 caracteres.',
            'description.required' => 'A descrição do ticket é obrigatória.',
            'description.min' => 'A descrição deve ter pelo menos 10 caracteres.',
            'description.max' => 'A descrição não pode exceder 5000 caracteres.',
            'category.max' => 'A categoria não pode exceder 100 caracteres.',
            'sentiment.in' => 'O sentimento deve ser: positive, negative ou neutral.',
            'status.in' => 'O status deve ser: open, closed ou pending.',
        ];
    }
}
