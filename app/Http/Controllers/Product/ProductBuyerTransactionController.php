<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Transaction;
use App\Transformers\TransactionTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:'. TransactionTransformer::class)->only(['store']);
		$this->middleware('auth:api')->only(['store']);
		$this->middleware('scope:purchase-product')->only(['store']);
    }

    public function store(Request $request, Product $product, User $buyer){


        $rules = [
            'quantity_id' => 'required|integer|min:1'
        ];

        $this->validate($request, $rules);

        // make sure that the seller is different from the buyer
        if ($buyer->id == $product->seller->id){
            return $this->errorResponse('The buyer must be different from the seller', 409);
        }

        // buyer needs to be verified
        if (!$buyer->isVerified()){
            return $this->errorResponse('The buyer must be a verified user', 409);
        }

        // seller needs to be verified
        if (!$product->seller->isVerified()){
            return $this->errorResponse('The seller must be a verified user', 409);
        }

        // product needs to be available
        if (!$product->isAvailable()){
            return $this->errorResponse('The product is not available', 409);
        }

        // product quantiity needs to be less than the purchased quantity
        if ($product->quantity < $request->quantity){
            return $this->errorResponse('The product does not have enough units for this transaction', 409);
        }

        return DB::transaction(function () use ($request, $product, $buyer){
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id
            ]);


            return $this->showOne($transaction, 201);
        });
    }
}
