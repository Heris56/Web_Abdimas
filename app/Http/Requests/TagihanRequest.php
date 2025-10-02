<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagihanRequest extends FormRequest
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
            'status_pembayaran' => 'nullable|in:Belum Lunas,Lunas',
            'tanggal_pembuatan_tagihan' => 'nullable|date',
            'nisn' => 'required|string',
            'periode' => 'nullable|string',
            'id_tipe_pembayaran' => 'required|integer',
            'id_tahun_ajaran' => 'nullable|integer',
            'nominal_tagihan' => 'nullable|numeric|min:0',
        ];
    }
}
