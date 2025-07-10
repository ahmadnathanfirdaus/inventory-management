<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GoodsReceivedRequest extends FormRequest
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
            'po_number' => 'required|string|exists:purchase_orders,po_number',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|integer|exists:order_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'po_number.required' => 'Nomor PO wajib diisi.',
            'po_number.exists' => 'Nomor PO tidak ditemukan.',
            'items.required' => 'Minimal harus ada 1 item.',
            'items.*.order_item_id.required' => 'Item order wajib dipilih.',
            'items.*.quantity_received.required' => 'Jumlah diterima wajib diisi.',
            'items.*.quantity_received.min' => 'Jumlah diterima tidak boleh negatif.',
        ];
    }
}
