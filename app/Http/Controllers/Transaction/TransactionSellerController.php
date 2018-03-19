<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Transaction;

class TransactionSellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @param Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function index(Transaction $transaction)
    {
        $sellers = $transaction->product->seller;

        return $this->showOne($sellers);
    }

}
