<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:kantor,showroom,kafe,restoran,outlet,other',
            'location' => 'required|string',
            'client_id' => 'required|exists:clients,id',
            'pic_id' => 'required|exists:users,id',
            'status' => 'sometimes|required|in:lead,survey,penawaran,negosiasi,deal,eksekusi,selesai,batal',
            'project_value' => 'nullable|numeric|min:0',
            'deal_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama project harus diisi',
            'type.required' => 'Jenis project harus dipilih',
            'location.required' => 'Lokasi project harus diisi',
            'client_id.required' => 'Klien harus dipilih',
            'pic_id.required' => 'PIC harus dipilih',
            'target_date.after_or_equal' => 'Target selesai harus setelah tanggal mulai'
        ];
    }
}