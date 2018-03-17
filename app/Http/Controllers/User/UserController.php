<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::all();

        // code 200 means successful
        return response()->json(['data' => $user], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
          'name' => 'required',
          'email' => 'required|email|unique:users',
          'password' => 'required|min:6|confirmed'
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_code'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);
        // code 201 mean created
        return response()->json(['data' => $user], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrfail($id);

        return response()->json(['data' => $user], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrfail($id);

        $rules = [
            'email' => 'email|unique:users,email,' . $user->id ,
            'password' => 'min:6|confirmed',
            'admin' => 'in:'. User::ADMIN_USER, User::REGULAR_USER,
        ];
        // if user id changing name
        if ($request->has('name')){
            $user->name = $request->name;
        }
        // if user is changing email
        if ($request->has('email') && $user->email != $request->email){
            // un-verify user account
            $user->verified = User::UNVERIFIED_USER;
            // give user a new verification code
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        // if user id changing password
        if ($request->has('password')){
            $user->password = bcrypt($request->password);
        }

        // if user is changing roles
        if ($request->has('admin')){
            // if user if not verified
            if (!$user->isVerified()){
                // code 409 specifies a conflict
                return response()->json([
                    'error' => 'Only verified users can modify the admin field','code' => '409'
                ], 409);
            }

            $user->admin = $request->admin;
        }

        // if isDirty() return true, which means that a users did not pass any fields
        if (!$user->isDirty()){
            // code
            return response()->json([
                'error' => 'You need to specify a different value to update','code' => '422'
            ], 422);
        }
        // if all is fine save / update the user, and return the user and a response code
        $user->save();

        return response()->json(['data' => $user], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();
        // code 200 means successful
        return response()->json(['data' => $user], 200);
    }
}
