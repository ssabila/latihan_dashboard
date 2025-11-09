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
        // Aturan dasar yang berlaku untuk create dan update
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'geojson_file' => 'required|string',
        ];

        // Cek apakah ini request UPDATE (punya parameter 'visualization' di rute)
        // atau request CREATE (tidak punya parameter)
        if ($this->route('visualization')) {
            // --- INI LOGIKA UNTUK UPDATE ---
            // File 'nullable', artinya opsional. Boleh tidak diisi.
            $rules['file'] = 'nullable|file|mimes:xlsx,xls|max:5120';
        } else {
            // --- INI LOGIKA UNTUK CREATE ---
            // File 'required', artinya wajib diisi.
            $rules['file'] = 'required|file|mimes:xlsx,xls|max:5120';
        }

        return $rules;
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