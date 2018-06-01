<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{

    public function __construct()
    {
        // protect the index and show controller
        $this->middleware('client.credentials')->only(['store', 'resend']);
		$this->middleware('auth:api')->except(['store', 'verify', 'resend']);
        $this->middleware('transform.input:'. UserTransformer::class)->only(['store', 'update']);
		$this->middleware('scope:manage-account')->only(['show', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        // code 200 means successful
        return $this->showAll($users);
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
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);
        // code 201 mean created
        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {

        return $this->showOne($user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

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
                return  $this->errorResponse('Only verified users can modify the admin field', 409);
            }

            $user->admin = $request->admin;
        }

        // if isDirty() return true, which means that a users did not pass any fields
        if (!$user->isDirty()){
            // code 422 specifies unproccesible entity
            return  $this->errorResponse('You need to specify a different value to update', 422);
        }
        // if all is fine save / update the user, and return the user and a response code
        $user->save();

        return $this->showOne($user, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        $user->delete();
        // code 200 means successful
        return $this->showOne($user, 200);
    }

    public function verify($token){
        // if not found a Model not found Exception will be triggered
        $user = User::where('verification_token', '=', $token)->firstOrFail();

        // change the verified status
        $user->verified = User::VERIFIED_USER;

        // remove the verification_token
        $user->verification_token = null;

        $user->save();

        return $this->showMessage('The account has been verified successfully');
    }

    public function resend(User $user){
//        ensure that the user is not verified
        if ($user->isVerified()){
            return $this->errorResponse('This user is already verified', 409);
        }

        retry(5, function () use ($user){
            Mail::to($user)->send(new UserCreated($user));
        }, 100);

        return $this->showMessage('The verification email has been resend');
    }
}
