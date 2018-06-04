<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Product;

class ProductBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
		$this->allowedAdminAction();
        $buyers = $product->transactions()
            ->with('buyer')
            ->get()
            ->pluck('buyer')
            ->unique('id')
            ->values();

        return $this->showAll($buyers);

    }
}
