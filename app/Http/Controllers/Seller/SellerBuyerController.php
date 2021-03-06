<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Seller;

class SellerBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @param Seller $seller
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
		$this->allowedAdminAction();
        $buyers = $seller->products()
            ->whereHas('transactions')
            ->with('transactions.buyer')
            ->get()
            ->pluck('transactions')
            ->collapse()
            ->pluck('buyer')
            ->unique('id') // should be unique
            ->values(); // exclude the nulls


        return $this->showAll($buyers);
    }

}
