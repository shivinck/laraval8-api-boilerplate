<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use JWTAuth;
use App\Helpers\Response;
use App\Helpers\Service;

class AuthController extends Controller {

    public function __construct() {
        $this->middleware('jwt.verify', ['except' => ['signup', 'signupVerify', 'login']]);
    }

    public function signup(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'mobile' => ['required', 'string', 'max:191', Rule::unique('users')->where(function ($query) use ($request) {
                return $query->where('account_status', 'active');
            })],
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|confirmed|min:5',
        ]);

        if ($validator->fails()) {
            return Response::sendResponse(['errors' => $validator->errors()], 'There was an error with your submission.', 422);
        }

        $user = User::updateOrcreate(['mobile' => $request->post('mobile')], array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        if ($user) {
            $response = Service::sendOTP($request->post('mobile'));
            if ($response) {
                return Response::sendResponse(['user' => $user, 'verify_id' => $response->Details], 'User has been registered successfully.', 201);
            }
        }

        return Response::sendError([], 'Something went wrong, Please try again.', 500);
    }

    public function signupVerify(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'verify_id' => 'required|string|max:50',
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Response::sendResponse(['errors' => $validator->errors()], 'There was an error with your submission.', 422);
        }

        $response = Service::verifyOTP($request->post('verify_id'), $request->post('code'));

        if ($response && $response->Status === 'Success') {
            $user = User::find($request->post('user_id'));
            $user->account_status = 'active';
            $user->mobile_verified_at = date("Y-m-d H:i:s", strtotime('now'));
            $user->save();
            $token = JWTAuth::fromUser($user);

            if ($token) {
                return Response::sendResponse(['user' => $user, 'token' => $this->createNewToken($token)], 'Your signup has been successfully completed.', 201);
            }

        } else {
            return Response::sendError([], 'Your code is incorrect. Please try again.', 400);
        }

        return Response::sendError([], 'Something went wrong, Please try again.', 500);
    }


    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|min:10|max:10',
            'password' => 'required|string|min:5',
        ]);

        $user = User::where('mobile', $request->post('mobile')) -> first();

        if ($validator->fails()) {
            return Response::sendResponse(['errors' => $validator->errors()], 'There was an error with your submission. Please try again.', 422);
        }

        if (!$token = JWTAuth::attempt(array_merge($validator->validated(), ['account_status' => 'active']))) {
            return Response::sendResponse([], 'Invalid email or password.', 422);
        }

        return Response::sendResponse(['user' => auth()->user(), 'token' => $this->createNewToken($token)], 'You have been successfully logged into the application.', 201);
    }

    protected function createNewToken($token) {
        return array(
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        );
    }

    public function user() {
        return Response::sendResponse(['user' => auth()->user()], 'Auth data has been successfully populated.');
    }

}
