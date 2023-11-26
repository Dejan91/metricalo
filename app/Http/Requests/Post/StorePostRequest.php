<?php

namespace App\Http\Requests\Post;

use App\Rules\Slug;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'slug'  => ['sometimes', 'string', 'min:2', 'max:255', 'unique:posts', new Slug],
            'body'  => ['required', 'string', 'min:6', 'max:1600'],
        ];
    }
}
