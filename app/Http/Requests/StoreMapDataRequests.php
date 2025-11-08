<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMapDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah sesuai dengan middleware auth Anda
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
            'geojson_file' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File Excel harus diunggah',
            'file.mimes' => 'File harus berformat .xlsx atau .xls',
            'file.max' => 'Ukuran file tidak boleh melebihi 5MB',
        ];
    }
}