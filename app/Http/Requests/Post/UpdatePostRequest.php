<?php

namespace App\Http\Requests\Post;

use App\Rules\Slug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'slug'  => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('posts')->ignore($this->post->id),
                new Slug
            ],
            'body'  => ['sometimes', 'required', 'string', 'min:6', 'max:1600'],
        ];
    }
}
