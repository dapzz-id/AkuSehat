<?php

namespace App\Modules\SuperAdmin\Instansi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstansiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('nama_instansi') && $this->has('nama_sekolah')) {
            $this->merge(['nama_instansi' => $this->nama_sekolah]);
        }
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('instansi');
        $jenisInstansi = $this->input('jenis_instansi', 'Sekolah');

        return [
            'nama_instansi' => 'required|string|max:255',
            'jenis_instansi' => 'required|in:Sekolah,Universitas,Perusahaan,Pemerintah,Klinik,Lainnya',
            'npsn' => $jenisInstansi === 'Sekolah' ? 'required|string|max:20|unique:instansi,npsn,' . $id : 'nullable|string|max:30|unique:instansi,npsn,' . $id,
            'jenjang' => $jenisInstansi === 'Sekolah' ? 'required|in:SD,SMP,SMA,SMK,MA,SLB,MTS' : 'nullable|string|max:50',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100|unique:instansi,email,' . $id,
            'website' => 'nullable|url|max:255',
            'status' => 'required|in:active,inactive',
            'tipe_sebutan' => 'nullable|string|max:50',
            'sebutan_spesifik' => 'nullable|string|max:50',
            'label_member_singular' => 'nullable|string|max:50',
            'label_member_plural' => 'nullable|string|max:50',
            'label_admin' => 'nullable|string|max:50',
            'label_divisi' => 'nullable|string|max:50',
            'label_divisi_plural' => 'nullable|string|max:50',
        ];
    }
}
