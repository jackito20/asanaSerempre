<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  public function signup(Request $request)
  {
    $request->validate([
        'name'     => 'required|string',
        'email'    => 'required|string|email|unique:users',
        'password' => 'required|string|confirmed',
    ]);
    $user = new User([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => bcrypt($request->password),
    ]);
    $user->save();
    return response()->json([
        'message' => 'Successfully created user!'], 201);
  }

  public function getLoginAsana()
  {

    $route = \Socialite::with('asana')->stateless()->redirect()->getTargetUrl();
    return response()->json(['route' => $route], 200);

  }

    public function login()
  {

    /*$request->validate([
        'email'       => 'required|string|email',
        'remember_me' => 'boolean',
    ]);*/

    /*if(!$request->has('code')) {
      $route = \Socialite::with('asana')->stateless()->redirect()->getTargetUrl();
      return response()->json(['route' => $route], 200);
      //return \Socialite::with('github')->redirect();
    }*/
    //dd(\Socialite::with('asana')->stateless()->user()->tasks());
    $data = \Socialite::with('asana')->stateless()->user();

    $user = User::where('email', 'LIKE', $data->email)->first();


    if($user) Auth::login($user);

    if (!Auth::check()) {
      return response()->json([
          'message' => 'Unauthorized'], 401);
    }
    //$user = $request->user();
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->token;
    /*if ($request->remember_me) {
      $token->expires_at = Carbon::now()->addWeeks(1);
    }*/
    $token->save();

    $user->assana_access = encrypt($data->token);
    $user->save();

    return response()->json([
        'access_token' => $tokenResult->accessToken,
        'asana_token' =>$data->accessTokenResponseBody
    ]);
    /*$request->validate([
        'email'       => 'required|string|email',
        'password'    => 'required|string',
        'remember_me' => 'boolean',
    ]);
    $credentials = request(['email', 'password']);
    if (!Auth::attempt($credentials)) {
      return response()->json([
          'message' => 'Unauthorized'], 401);
    }
    $user = $request->user();
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->token;
    if ($request->remember_me) {
      $token->expires_at = Carbon::now()->addWeeks(1);
    }
    $token->save();
    return response()->json([
        'access_token' => $tokenResult->accessToken,
        'token_type'   => 'Bearer',
        'expires_at'   => Carbon::parse(
            $tokenResult->token->expires_at)
            ->toDateTimeString(),
    ]);*/
  }

  public function logout(Request $request)
  {
    $request->user()->token()->revoke();
    return response()->json(['message' =>
        'Successfully logged out']);
  }

  public function user(Request $request)
  {
    return response()->json($request->user());
  }
}
