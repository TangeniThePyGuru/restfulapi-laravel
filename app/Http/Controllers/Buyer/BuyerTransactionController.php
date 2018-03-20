<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuyerTransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @param Buyer $buyer
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        // one to many relation
        $transactions = $buyer->transactions;

        return $this->showAll($transactions);
    }

}
