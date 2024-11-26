<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AuthController extends BaseController
{


//    /**
//     * Create a new AuthController instance.
//     *
//     * @return void
//     */
//    public function __construct()
//    {
//        $this->middleware('auth:api', ['except' => ['login', 'profile']]);
//    }



    /**
     * Register a User.
     *
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::where('email', $input['email'])->first();

        if ($user !== null) {
            return $this->sendError('User already exists.', ['USER_ALREADY_EXISTS'], 409);
        } else {
            try {
                $user = User::create($input);
                $success['user'] = $user;
            } catch (\Exception $err) {
                return $this->sendError($err->getMessage(), ['UNKNOWN_ERROR'], 500);
            }

        }

        return $this->sendResponse($success, 'User register successfully.', 201);
    }


    /**
     * Get a JWT via given credentials.
     *
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        $user = User::where('email', $credentials['email'])->first();

        if (!$token = auth()->attempt($credentials)) {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }

        if (!$user->is_active) {
            return $this->sendError('User blocked.', ['error' => 'Blocked'], 403);
        }


        $success = $this->respondWithToken($token);
        $success['name'] = auth()->user()->name;
        $success['id'] = auth()->user()->id;

        return $this->sendResponse($success, 'User login successfully.');
    }

    /**
     * Get the authenticated User.
     *
     */
    public function profile()
    {
        $success = auth()->user();

        return $this->sendResponse($success, 'Refresh token return successfully.');
    }

    /**
     * Log the user out (Invalidate the token).
     *
     */
    public function logout()
    {
        auth()->logout();

        return $this->sendResponse([], 'Successfully logged out.');
    }

    /**
     * Refresh a token.
     *
     */
    public function refresh()
    {

        $success = $this->respondWithToken(auth()->refresh());

        return $this->sendResponse($success, 'Refresh token return successfully.');
    }



//    public function refreshToken(Request $request)
//    {
//
//        $odlToken = $request->input('token');
//
//        if(!$odlToken)
//            return response()->json(null);
//
//        $token = JWTAuth::refresh($odlToken);
//
//        return response()->json(compact('token'));
//    }






    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
