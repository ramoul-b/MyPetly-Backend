<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\BookingStatusEnum;

class StoreBookingRequest extends FormRequest
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
            'service_id'      => 'required|exists:services,id',
            'provider_id'     => 'required|exists:providers,id',
            'appointment_date'=> 'required|date',
            'time'            => ['required','regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'payment_intent'  => 'required|string',
            'currency'        => 'in:eur,usd',   // ou juste eur
            'status' => ['nullable', 'in:' . implode(',', array_column(BookingStatusEnum::cases(), 'value'))],
            'notes'           => 'nullable|string',
        ];
    }   
}
