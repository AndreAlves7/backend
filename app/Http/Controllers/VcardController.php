<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vcard;
use App\Models\Transaction;
use App\Http\Resources\VcardResource;

use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreVcardRequest;
use App\Http\Requests\UpdateVcardRequest;
use Carbon\Carbon;
use App\Http\Resources\CategoryResource;

class VcardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Vcard::class);
        return VcardResource::collection(Vcard::all()->where('deleted_at', NULL));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreVcardRequest $request)
    {
        //
        // $vcard = new Vcard();
        // $vcard->phone_number = $request->phone_number;
        // $vcard->name = $request->name;
        // $vcard->photo_url = $request->photo_url;
        // $vcard->balance = $request->balance;
        // $vcard->max_debit = $request->max_debit;
        error_log($request);
        // ($request);

        //validar com FormRequest
        $vcard = new Vcard();
        $vcard->fill($request->validated());
        $vcard->password = bcrypt($request->password);
        $vcard->blocked = FALSE;
        $vcard->balance = 0;
        $vcard->save();

        return new VcardResource($vcard);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vcard $vcard)
    {
        //show a single vcard
        $this->authorize('view', $vcard);
        if ($vcard->deleted_at == NULL) {
            return new VcardResource($vcard);
        } else {
            return response()->json(['error' => 'Vcard not found'], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVcardRequest $request, Vcard $vcard)
    {
        $this->authorize('update', $vcard);
        // $vcard->update($request->validated()); -> não funciona
        $vcard->fill($request->validated());
        $vcard->max_debit = $request->max_debit;
        $vcard->blocked = $request->blocked;
        $vcard->save();
        return new VcardResource($vcard);
    }

       /**
     * Display a listing of the resources transactions.
     */
    public function getTransactionsByVcard($userId)
    {
        $transactions = Transaction::where('vcard', $userId)->get();
        
        $responseData = ['transactions' => $transactions->toArray()];
    
        return response()->json($responseData);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vcard $vcard)
    {
        //only admins can delete
        $this->authorize('delete', $vcard);

        if ($vcard->balance > 0) {
            return response()->json(['error' => 'Vcard has balance greater than 0'], 403);
        }

        if ($vcard->transactions()->count() > 0) {
            // soft delete all vcard transactions
            $vcard->softDeleteTransactions();
            // soft delete vcard
            $vcard->deleted_at = now();
            $vcard->save();
        } else {
            // hard delete
            $vcard->delete();
        }

        return new VcardResource($vcard);
    }


    public function getDataForStatistics($userId, $userType)
    {
        Log::info('ola');
        $transactions = Transaction::when($userType != 'A', function ($query) use ($userId) {
            $query->where('vcard', $userId);
        })
        ->select('date', 'type', 'value')
        ->get();

        //log transactions
        // Log::info($transactions);
  

        $sumsByDateAndType = collect([]);
        
        foreach ($transactions as $transaction) {
            $key = Carbon::parse($transaction->date)->format('Y-m') . "|{$transaction->type}";

            if (!isset($sumsByDateAndType[$key])) {
                $sumsByDateAndType[$key] = 0;
            }

            $sumsByDateAndType[$key] += $transaction->value;
        }

        $uniqueDates = $transactions->pluck('date')->unique()->values();
        $uniqueTypes = $transactions->pluck('type')->unique()->values();

        $dataForStatistics = [];
        $groupedDates = [];

        foreach ($uniqueDates as $date) {
            $groupedDates[Carbon::parse($date)->format('Y-m')][] = $date;
        }

        foreach ($uniqueTypes as $type) {
            foreach ($groupedDates as $month => $dates) {

                foreach ($dates as $date) {
                    $key = "$month|$type";
                    $sum = $sumsByDateAndType[$key] ?? 0;
                }

                $dataForStatistics[] = [
                    'month' => $month,
                    'type' => $type,
                    'sum_of_values' => round($sum),
                ];
            }
        }

        return response()->json(['data_for_statistics' => $dataForStatistics]);
    }


    public function getTotalUsageOfPaymentMethod($userId, $userType)
    {
        $transactions = Transaction::when($userType != 'A', function ($query) use ($userId) {
                $query->where('vcard', $userId);
            })
            ->select('payment_type', \DB::raw('count(*) as total'))
            ->groupBy('payment_type')
            ->get();
    
        return $transactions;
    }


    public function getTotalUsageAndMaxValue($userId, $userType)
    {
        
        $transactions = Transaction::when($userType != 'A', function ($query) use ($userId) {
                $query->where('vcard', $userId);
            })
            ->select('value')
            ->get();
    
        $totalSum = $transactions->sum('value');
        $maxValueTransaction = $transactions->max('value');
    
        return [    
            'totalSum' => round($totalSum),
            'maxValue' => round($maxValueTransaction),
        ];
    }

    public function getCategoriesByVcard(Vcard $vcard)
    {
        $this->authorize('viewVcardCategories', $vcard);
        return CategoryResource::collection($vcard->categories->all());
    }
}
