<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Models\Vcard;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Transaction::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        //
        //transacao de debito



        $validated = $request->validated();

        //----
        if ($validated['type'] == 'D') {
            //verificar se o vcard tem saldo suficiente e o valor esta dentro do limite do max_debit
            $vcard = Vcard::where('phone_number', $validated['vcard'])->firstOrFail();

            if ($vcard->balance < $validated['value']) {
                abort(400, 'Saldo insuficiente');
            }
            if ($vcard->max_debit < $validated['value']) {
                abort(400, 'Valor superior ao limite de débito');
            }
        }


        //----


        try {
            $result = DB::transaction(function () use ($request, $validated) {
                $transaction = new Transaction();


                $transaction->fill($validated);

                $transaction->date = Carbon::now()->toDateString();
                $transaction->datetime = Carbon::now()->toDateTimeString();


                $associated = $transaction->vcardAssociated;

                //----
                $transaction->old_balance = $associated->balance;

                if ($validated['type']) {
                    $transaction->new_balance = $associated->balance + $transaction->value;
                } else {
                    $transaction->new_balance = $associated->balance - $transaction->value;
                }

                //----

                $associated->balance = $transaction->new_balance;

                $associated->save();

                if ($transaction->payment_type == 'VCARD') {

                    $pair_transaction = new Transaction();
                    $pair_transaction->vcard = $transaction->payment_reference;
                    $pair_transaction->type = 'C'; // credit
                    $pair_transaction->value = $transaction->value;
                    $pair_transaction->payment_type = 'VCARD';
                    $pair_transaction->payment_reference = $transaction->vcardAssociated->phone_number;
                    $pair_transaction->date = $transaction->date;
                    $pair_transaction->datetime = $transaction->datetime;
                    // vai ser uma transação de crédito
                    $pair_transaction->old_balance = $transaction->vcardAssociated->balance;
                    $pair_transaction->new_balance = $transaction->vcardAssociated->balance + $transaction->value;
                    $pair_transaction->pair_transaction = $transaction->id;
                    $pair_transaction->save();

                    $transaction->pair_transaction = $pair_transaction->id;

                    $transaction->pair_vcard = $pair_transaction->vcardAssociated->phone_number;
                    $pair_transaction->pair_vcard = $transaction->vcardAssociated->phone_number;

                    $pairVcardAssociated = $pair_transaction->vcardAssociated;
                    $pairVcardAssociated->balance = $pair_transaction->new_balance;
                    $pairVcardAssociated->save();
                } else {
                    // PAYMENT GATEWAY SERVICE
                    // post <service URI>/api/credit Creates a new credit on the external entity
                    // https://dad-202324-payments-api.vercel.app
                    // {
                    //     "type": "MB",
                    //     "reference": "45634-123456789",
                    //     "value": 23.79
                    // }

                    $client = new Client();


                    //----

                    $url = 'https://dad-202324-payments-api.vercel.app/api';

                    if ($validated['type'] == 'C') {
                        $url .= '/debit';
                    } else {
                        $url .= '/credit';
                    }

                    Log::info($url);

                    //----

                    $res = $client->request('POST', $url, [
                        'json' => [
                            'type' => $transaction->payment_type,
                            'reference' => $transaction->payment_reference,
                            'value' => $transaction->value
                        ]
                    ]);


                    // if request is successful then create a credit transaction
                    if ($res->getStatusCode() != 201) {
                        return abort(400, 'Erro no pagamento');
                    }
                }

                $transaction->save();

                return $transaction;
            });

            return response()->json($result, 201);
        } catch (\Exception $e) {
            Log::info($e);
            abort(400, 'Erro no pagamento 2');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
