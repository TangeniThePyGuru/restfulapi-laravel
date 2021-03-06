<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuyerCategoryController extends ApiController
{
	public function __construct()
	{
		parent::__construct();
		$this->middleware('scope:read-general')->only('index');
		$this->middleware('can:view,buyer')->only('index');
	}

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
