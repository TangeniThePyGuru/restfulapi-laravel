<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = Seller::all();

        return $this->showAll($sellers);
    }


    /**
     * Display the specified resource.
     *
     * @param  Seller $seller
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {
//        $seller = Seller::has('products')->findOrFail($id);

        return $this->showOne($seller);
    }

}
