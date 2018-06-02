<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\Transformers\ProductTransformer;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:'. ProductTransformer::class)->only(['store', 'update']);
		$this->middleware('auth:api');
		$this->middleware('scope:manage-product')->except('index');

		$this->middleware('can:view,seller')->only('index');
		$this->middleware('can:sell,seller')->only('store');
		$this->middleware('can:edit-product,seller')->only('update');
		$this->middleware('can:delete-product,seller')->only('destroy');
    }

	/**
	 * @param Seller $seller
	 * @return \Illuminate\Http\JsonResponse
	 * @throws AuthorizationException
	 */
    public function index(Seller $seller)
    {
    	if (request()->user()->tokenCan('read-general') || request()->user()->tokenCan('manage-products')){
			$products = $seller->products;

			return $this->showAll($products);
		}

		throw new AuthorizationException('Invalid scope(s)');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
          'name' => 'required',
          'description' => 'required',
          'quantity' => 'required|integer|min:1',
          'image' => 'required|image'
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] = $request->image->store(); // store('path', 'fileSystem') takes the default values if non supplied
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return $this->showOne($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => 'integer|min:1',
            'status' => 'in:'.Product::AVAILABLE_PRODUCT .','.Product::UNAVAILABLE_PRODUCT,
            'image' => 'image'
        ];

        $this->validate($request, $rules);

        // checks if the the provided seller is owner of this products
        $this->checkSeller($seller, $product);

        // if checkSeller is successful
        $product->fill($request->only([
            'name',
            'description',
            'quantity'
        ]));

        if ($request->has('status')){
            $product->status = $request->status;
            // if a product is available that it should also belong to categories
            if ($product->isAvailable() && $product->categories()->count() == 0){
                // code 409 == conflict
                return $this->errorResponse('An active product must have at least one category', 409);
            }
        }

        // check if new image has been uploaded
        if  ($request->hasFile('image')){
            // deletes the old file
           Storage::delete($product->image);
            // updates with the new file
           $product->image = $request->image->store();
        }

        // represents that nothing has changed
        if ($product->isClean()){
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        // if something changed update the product
        $product->save();

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);

        $product->delete();
        Storage::delete($product->image);

        return $this->showOne($product);
    }

    protected function checkSeller(Seller $seller, Product $product){
//        dd($seller->id.'-'.$product->seller->id);
        if ($seller->id != $product->seller->id){
            // HttpException is from SymphonyComponent
            throw new HttpException(422,'The specified seller is not the actual seller of the product');
        }
    }

}
