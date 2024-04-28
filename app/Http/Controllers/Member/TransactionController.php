<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function store(Request $request)
    {

        $package = Package::find($request->package_id);
        $costumer = auth()->user();
        $transaction = Transaction::create([
            'package_id' => $package->id,
            'user_id' => $costumer->id,
            'amount' => $package->price,
            'transaction_code' => strtoupper(Str::random(10)),
            'status' => 'pending'
        ]);



        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$isSanitized = (bool) env('MIDTRANS_IS_SANITIZED');
        \Midtrans\Config::$is3ds = (bool) env('MIDTRANS_IS_3DS');

        $params = array(
            'transaction_details' => array(
                'order_id' => $transaction->transaction_code,
                'gross_amount' => $transaction->amount,
            ),
            'customer_details' => array(
                'first_name' => $costumer->name,
                'last_name' => $costumer->name,
                'email' => $costumer->email,
            ),
        );

        $createMidtransTransaction = \Midtrans\Snap::createTransaction($params);
        $midtransRedirectUrl = $createMidtransTransaction->redirect_url;

        return redirect($midtransRedirectUrl);
    }
}
