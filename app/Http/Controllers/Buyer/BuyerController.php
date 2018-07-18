<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class BuyerController extends ApiController
{
	public function __construct()
	{
		parent::__construct();

		$this->middleware('auth:api');
		$this->middleware('scope:read-general')->only('index');
		$this->middleware('can:view,buyer')->only('show');
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function index()
    {
    	$this->allowedAdminAction();
        $buyers = Buyer::has('transactions')->get();

        return  $this->showAll($buyers);
    }


    /**
     * Display the specified resource.
     *
     * @param  Buyer $buyer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Buyer $buyer)
    {
//        $buyer = Buyer::has('transactions')->findOrFail($id);

        return $this->showOne($buyer);
    }

}
