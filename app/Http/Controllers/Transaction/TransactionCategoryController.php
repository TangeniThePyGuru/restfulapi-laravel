<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionCategoryController extends ApiController
{
    /**
     * TransactionCategoryController constructor.
     */
    public function __construct()
    {
        // protect the index and show controller
        $this->middleware('client.credentials')->only(['index']);
    }
    /**
     * Display a listing of the resource.
     * @param Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function index(Transaction $transaction)
    {
        $categories = $transaction->product->categories;

        return $this->showAll($categories);
    }

}
