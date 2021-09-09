<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\LoginRepository;
use App\Http\Requests\{
    SignupRequest,
    LoginRequest,
    VerifyOtpRequest,
    ForgetPasswordRequest,
    ResetPasswordRequest
};

class LoginController extends Controller {

    protected $loginRepository;

    public function __construct(LoginRepository $loginRepo) {
        $this->loginRepository = $loginRepo;
    }

    /**
     * User Sign up
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(SignupRequest $request) {
        $response = $this->loginRepository->signUp($request);
        return response()->json($response, $response['response']);
    }

    /**
     * User Login
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request) {
        $response = $this->loginRepository->login($request);
        return response()->json($response, $response['response']);
    }

    /**
     * User Forget Password
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPassword(ForgetPasswordRequest $request) {
        $response = $this->loginRepository->forgetPassword($request);
        return response()->json($response, $response['response']);
    }

    /**
     * User Logout
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request) {
        $user = request()->user();
        if (!empty(request()->user())) {
            $user->tokens()->delete();
            return response()->json(['message' => trans("api_message.logout_success"), 'response' => 200], 200);
        } else {
            return response()->json(['message' => trans("api_message.something_went_wrong"), 'response' => 201], 201);
        }
    }

    /**
     * User Verification by otp
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(VerifyOtpRequest $request) {
        $response = $this->loginRepository->verifyOtp($request);
        return response()->json($response, $response['response']);
    }

    /**
     * User Forget Password
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp(ForgetPasswordRequest $request) {
        $response = $this->loginRepository->resendOtp($request);
        return response()->json($response, $response['response']);
    }

    /**
     * User Reset Password
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request) {
        $response = $this->loginRepository->resetPassword($request);
        return response()->json($response, $response['response']);
    }

}
