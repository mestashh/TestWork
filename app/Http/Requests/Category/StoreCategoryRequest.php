<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Авторизация будет производиться через Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Чуть-чуть добавил от тебя, не может же быть 2‑х категорий с одним названием.
            'name' => ['required', 'string', 'unique:categories,name'],
            'description' => ['required', 'string', 'max:255'],
        ];
    }
}
