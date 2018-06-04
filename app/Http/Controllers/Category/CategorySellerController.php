<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategorySellerController extends ApiController
{
    /**
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Category $category)
    {
		$this->allowedAdminAction();
        // values() removes any empty elements that might exist
        $sellers = $category->products()->with('seller')
            ->get()
            ->pluck('seller')
            ->unique('id')
            ->values();
        return $this->showAll($sellers);
    }

}
