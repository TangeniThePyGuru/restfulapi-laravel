<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryBuyerController extends ApiController
{
    /**
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Category $category)
    {
        $buyers = $category->products()
            ->whereHas('transactions')
            ->with('transactions.buyer')
            ->get()
            ->pluck('transactions')
            ->collapse() // collapses an array
            ->pluck('buyer')
            ->unique('id')
            ->values();
        return $this->showAll($buyers);
    }
}
