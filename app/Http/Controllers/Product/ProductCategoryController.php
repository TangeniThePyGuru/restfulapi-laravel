<?php

namespace App\Http\Controllers\Product;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Product $product, Category $category){
        // attach -> attaches the category and duplicates it if it existed already
        // sync -> removes everything and lives this one
        // syncWithoutDetach -> attaches the category without removing and duplicating
        $product->categories()->syncWithoutDetaching([$category->id]);


        return $this->showAll($product->categories);
    }

}
