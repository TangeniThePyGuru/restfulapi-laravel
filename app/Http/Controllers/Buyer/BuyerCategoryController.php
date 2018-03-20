<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuyerCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @param Buyer $buyer
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        // many to many relationships
        // collapse method gives us one list of the current lists
        $categories = $buyer->transactions()->with('product.categories')
            ->get()
            ->pluck('product.categories')
            ->collapse()
            ->unique('id');

        return $this->showAll($categories);
    }

}
