<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWordCloudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'words' => 'required|array|min:1',
            'words.*.word' => 'required|string|max:100',
            'words.*.value' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'words.required' => 'Minimal satu kata harus ditambahkan',
            'words.*.word.required' => 'Kata tidak boleh kosong',
            'words.*.value.required' => 'Nilai harus diisi',
            'words.*.value.min' => 'Nilai minimal 1',
        ];
    }
}