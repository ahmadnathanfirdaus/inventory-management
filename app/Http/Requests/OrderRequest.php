<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = Auth::user();
        return Auth::check() && $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_date' => 'required|date|before_or_equal:today',
            'description' => 'required|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_code' => 'required|string|exists:products,product_code',
            'items.*.distributor_code' => 'nullable|string|exists:distributors,distributor_code',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'order_date.required' => 'Tanggal order wajib diisi.',
            'order_date.date' => 'Format tanggal tidak valid.',
            'order_date.before_or_equal' => 'Tanggal order tidak boleh lebih dari hari ini.',
            'description.required' => 'Deskripsi order wajib diisi.',
            'items.required' => 'Minimal harus ada 1 item.',
            'items.*.product_code.required' => 'Produk wajib dipilih.',
            'items.*.product_code.exists' => 'Produk yang dipilih tidak valid.',
            'items.*.distributor_code.exists' => 'Distributor yang dipilih tidak valid.',
            'items.*.quantity.required' => 'Jumlah item wajib diisi.',
            'items.*.quantity.min' => 'Jumlah item minimal 1.',
            'items.*.unit_price.required' => 'Harga satuan wajib diisi.',
            'items.*.unit_price.min' => 'Harga satuan tidak boleh negatif.',
        ];
    }
}
