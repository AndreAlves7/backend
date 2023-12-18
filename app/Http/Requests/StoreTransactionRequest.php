<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
        // "vcard" – foreign key - the vCard (the phone number of the vCard) that owns the transaction. Every transaction is associated to a specific vCard.
        // • "type" – type of transaction ("C" is a credit transaction; "D" is a debit transaction).
        // "value" – the value (€) of the transaction. If type of transaction is "C", the balance of the vCard increases; if type is "D", then the balance decreases. Only transactions with a "value" greater than zero (value is at least 0,01€) are allowed. Also, on a debit transaction, the value must be equal or less than the vCard balance (to guarantee that the balance is not negative) and equal or less than the maximum limit ("max_debit") for debits of the vCard.
        // "payment_type" – the type of payment associated to the transaction. If the transaction is a debit, the payment refers to where the money was sent to (the destination of the money). If the transaction is a credit, the payment refers to where the money came from (the source of the money).
        // o Possible values: VCARD, MBWAY, PAYPAL, IBAN (bank transfer), MB (“Multibanco”) and VISA
        // "payment_reference" – the reference of the payment associated to the transaction. Note that different types of payment require references with different validation rules, namely:
        //     o VCARD – a Portuguese phone number (9 digits starting with the digit 9) relative to an existing vCard (must exist on the database)
        //     o MBWAY– any Portuguese phone number (9 digits starting with the digit 9) – does not need to exist on the database - e.g.: 915785345
        //     o PAYPAL– a valid email - e.g.: john.doe@gmail.com
        //     o IBAN – 2 letters followed by 23 digits - e.g.: PT50123456781234567812349
        //     o MB - 5 digits (entity number), an hyphen (“-“) and a reference (9 digits) – e.g.: 45634-123456789
        //     o VISA - 16 digits starting with the digit 4 - e.g.: 4321567812345678
        // • "category_id " – (optional) the category of the transaction. This column is optional and only used if the vCard owner decides to classify the transaction (classification of the transaction is optional). The application must guarantee that a credit transaction only uses credit categories, and a debit transaction only uses debit categories.
        // • "description " – (optional) the description of the transaction (free text). This column is optional and only used if the vCard owner decides to describe the transaction.
        return [
            'vcard' => 'required|exists:vcards,phone_number',
            'type' => 'required|in:C,D',
            'value' => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:VCARD,MBWAY,PAYPAL,IBAN,MB,VISA',
            'payment_reference' => [
                'required',
                function ($attribute, $value, $fail) {
                    $paymentType = request()->input('payment_type');
                    switch ($paymentType) {
                        case 'VCARD':
                            if (!preg_match('/^9[0-9]{8}$/', $value)) {
                                $fail('O número do VCARD é inválido.');
                            }
                            break;
                        case 'MBWAY':
                            if (!preg_match('/^9[0-9]{8}$/', $value)) {
                                $fail('O número do MBWAY é inválido.');
                            }
                            break;
                        case 'PAYPAL':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $fail('O email do PAYPAL é inválido.');
                            }
                            break;
                        case 'IBAN':
                            if (!preg_match('/^[A-Z]{2}[0-9]{23}$/', $value)) {
                                $fail('O IBAN é inválido.');
                            }
                            break;
                        case 'MB':
                            if (!preg_match('/^[0-9]{5}-[0-9]{9}$/', $value)) {
                                $fail('O MB é inválido.');
                            }
                            break;
                        case 'VISA':
                            if (!preg_match('/^4[0-9]{15}$/', $value)) {
                                $fail('O número do VISA é inválido.');
                            }
                            break;
                        default:
                            $fail('Tipo de pagamento desconhecido.');
                    }
                },
            ],
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ];
    }
}
