<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use GuzzleHttp;
use Illuminate\Support\Collection;

class ApiAuthController extends ApiController
{
   public function getToken(Request $request){

       $rules = [
           'password' => 'required',
           'username' => 'required|email'
       ];

       $this->validate($request, $rules);

       $http = new GuzzleHttp\Client;

       $response = $http->post(url('oauth/token'), [
           'form_params' => [
               'grant_type' => 'password',
               'client_id' => 3,
               'client_secret' => 'WVlcS4uaBqHdURk68F1FkBtgUQpnFJ2TOQikFv9m',
               'username' => $request->username,
               'password' => $request->password,
               'scope' => !$request->scopes ? '*': $request->scopes,
           ],
       ]);


      $user = $http->post(url('user'), [
           'headers' => [
               'Authorization' => 'Bearer '.json_decode((string) $response->getBody())->access_token
           ]
       ]);

        Collection::make(['user' => (string) $user->getBody(), 'auth' => (string) $response->getBody() ]);

       return json_decode(Collection::make(['user' => json_decode((string) $user->getBody(), true),
                                            'auth' => json_decode((string) $response->getBody()) ]), true);
    }
}
