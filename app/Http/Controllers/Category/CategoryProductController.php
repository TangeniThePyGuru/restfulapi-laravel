<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;

class CategoryProductController extends ApiController
{
    public function __construct()
    {
        // protect the index controller
        $this->middleware('client.credentials')->only(['index']);
    }

    /**
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Category $category)
    {
        // direct many to many relation
        $products = $category->products;

        return $this->showAll($products);
    }

}
