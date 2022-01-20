<?php

namespace App\Repositories\Api;

use App\Models\{
    User,
    EmailTemplate,
    UserDevice,
};
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Config;
use App\Helpers\{
    Helper,
    LoginHelper
};

class LoginRepository
{

    protected $emailTemplateModel, $userModel, $userDeviceModel, $helper, $loginHelper;

    public function __construct(EmailTemplate $EmailTemplate, User $user, UserDevice $userDevice, Helper $helper, LoginHelper $loginHelper)
    {
        $this->emailTemplateModel = $EmailTemplate;
        $this->userModel = $user;
        $this->userDeviceModel = $userDevice;
        $this->loginHelper = $loginHelper;
        $this->helper = $helper;
    }

    /**
     * User Signup
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signUp($request)
    {
        try {
            $user_data = $this->userModel->where(function ($query) use ($request) {
                $query->where('email', $request->email)
                    ->orWhere('username', $request->username);
            })->count();
            if ($user_data == 0) {
                $user = new $this->userModel;
                $user->username = $request->username;
                $user->email = $request->email;
                $user->password = !empty($request->password) ? Hash::make($request->password) : '';
                $user->name = $request->name;
                $user->user_type = $request->user_type;
                $user->account_type = $request->account_type;
                $user->date_of_birth = Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d');
                $user->mobile_number = $request->mobile_number;
                $user->referral_code = $request->referal_code;
                $user->user_type = $this->userModel::CUSTOMER;
                $user->otp = $this->helper->createOtp();
                $user->otp_expire_time = Carbon::now()->addMinutes(15);
                $user->is_active = $this->userModel::IS_INACTIVE;
                if ($request->account_type != $this->userModel::NORMAL_SIGNUP) {
                    $user->social_id = $request->account_type_id;
                    $user->is_active = $this->userModel::IS_ACTIVE;
                }
                $user->save();

                if (!empty($request->profile) && $request->hasFile('profile')) {
                    $image_name = time() . '.' . $request->profile->extension();
                    $folder_name = 'user_profile/' . $user->user_id;
                    $collection_name = $this->userModel::MEDIA_COLLECTION;
                    $this->helper->storeMediaFile($user, $request->profile, $folder_name, $collection_name, $image_name);

                    $user->profile_image = $image_name;
                    $user->save();
                }

                $device_token = new $this->userDeviceModel;
                $device_token->user_id = $user->user_id;
                $device_token->device_type = $user->device_type;
                $device_token->device_token = $user->device_token;
                $device_token->save();


                $token = $user->createToken('auth_token')->plainTextToken;

                $user_data = $this->getResponseData($token, $user, $request);

                if ($request->account_type == $this->userModel::NORMAL_SIGNUP) {
                    $response = $this->loginHelper->sendEmail($this->emailTemplateModel::ACCOUNT_VERIFICATION, $request, $data['otp']);

                    if ($response['code'] == 200) {
                        return [
                            'message' => trans("api_message.signup_verify_account"),
                            'response' => 200,
                            'data' => [
                                'access_token' => $token,
                                'email' => $user->email
                            ]
                        ];
                    } else {
                        return ['message' => $response['message'], 'response' => 201];
                    }
                } else {

                    return ['message' => trans("api_message.social_signup_success"), 'response' => 200, 'data' => $user_data];
                }
            } else {
                return ['message' => trans("api_message.username_already_exist"), 'response' => 201];
            }
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'response' => 201];
        }
    }

    /**
     * User Login
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login($request)
    {
        try {
            if ($request->account_type == $this->userModel::NORMAL_SIGNUP) {

                $user_data = $this->userModel->where(function ($query) use ($request) {
                    $query->where('email', $request->username)
                        ->orWhere('username', $request->username);
                })->first();
                if (!empty($user_data)) {
                    if ($user_data->is_active == $this->userModel::IS_INACTIVE && $user_data->deactivated_by == '1') {
                        return ['message' => trans('api_message.inactive_account_error'), 'response' => 201, 'data' => ['is_active' => $user_data->is_active, 'deactivated_by' => $user_data->deactivated_by]];
                    }

                    if ($user_data->is_email_verified == '0') {

                        $user_data->otp = $this->helper->createOtp();
                        $user_data->otp_expire_time = Carbon::now()->addMinutes(15);
                        $user_data->save();
                        $response = $this->loginHelper->sendEmail($this->emailTemplateModel::ACCOUNT_VERIFICATION, $user_data, $user_data->otp);
                        return ['message' => trans('api_message.email_not_verified'), 'response' => 200, 'data' => ['email' => $user_data->email, 'is_email_verified' => $user_data->is_email_verified]];
                    }

                    if (!empty($user_data->password) && empty($user_data->account_type_id)) {
                        if (!Hash::check($request->password, $user_data->password)) {
                            return ['message' => trans('api_message.invalid_login_details'), 'response' => 201, 'user_status' => ''];
                        }
                    } else {
                        if (!empty($user_data->account_type_id)) {
                            return ['message' => str_replace('{{SOCIAL}}', Config::get('constants.account_type')[$user_data->account_type], ' Sign up from social'), 'response' => 201, 'user_status' => ''];
                        }
                    }

                    $login_data = $this->loginHelper->checkLogin($user_data, $request);
                    if ($login_data['code'] == 200) {
                        $user_data->is_active = $this->userModel::IS_ACTIVE;
                        $token = $user_data->createToken('auth_token')->plainTextToken;
                        $user_datas = $this->getResponseData($token, $user_data, $request);
                        return [
                            'message' => trans("api_message.login_success"),
                            'response' => 200,
                            'data' => $user_datas
                        ];
                    } else {
                        return ['message' => trans("api_message.something_went_wrong"), 'response' => 201];
                    }
                } else {
                    return ['message' => trans("api_message.no_account_found"), 'response' => 201];
                }
            }
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'response' => 201];
        }
    }

    /**
     * User Response Common Function
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResponseData($token, $new_user, $request)
    {
        try {
            $image1 = (!empty($new_user->profile_image)) ? $new_user->getFirstMediaUrl($this->userModel::MEDIA_COLLECTION) : asset('admin_assets/images/default.png');
            $image = ($image1 != '') ? $image1 : $new_user->profile_image;

            $user_data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                "user_id" => $new_user->user_id,
                "is_email_verified" => $new_user->is_email_verified,
                "user_image" => $image,
                "name" => $new_user->name,
                "username" => $new_user->username,
                "email" => $new_user->email,
                "date_of_birth" => $new_user->date_of_birth,
                "mobile_number" => $new_user->mobile_number,
                "is_active" => $new_user->is_active,
                "about" => $new_user->about_me,
                "referral_code" => $new_user->referral_code,
                "profile_completed" => $new_user->is_profile_complete,
                "deactivated_by" => $new_user->deactivated_by,
            ];
            return $user_data;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'response' => 201];
        }
    }

    /**
     * Verify Otp
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp($request)
    {
        try {
            $user_data = $this->userModel->where('email', $request->email)->where('otp', $request->otp)->first();
            if (!empty($user_data)) {
                $current_time = Carbon::now()->timestamp;
                $otp_expire = Carbon::parse($user_data->otp_expire_time)->timestamp;
                $diff = $current_time - $otp_expire;
                if ($diff > 0) {
                    return ['message' => trans("api_message.otp_expired"), 'response' => 201];
                } else {
                    $login_data = $this->loginHelper->checkLogin($user_data, $request);
                    $token = $user_data->createToken('auth_token')->plainTextToken;
                    $user_data->otp = '';
                    $user_data->otp_expire_time = NULL;
                    $user_data->is_active = '1';
                    $user_data->is_email_verified = '1';
                    $user_data->save();
                    $user_datas = $this->getResponseData($token, $user_data, $request);
                    return ['message' => trans("api_message.verification_success"), 'data' => $user_datas, 'response' => 200];
                }
            } else {
                return ['message' => trans("api_message.invalid_otp"), 'response' => 201];
            }
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'response' => 201];
        }
    }

    /**
     * Resend Otp
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp($request)
    {
        try {
            $user_data = $this->userModel->where('email', $request->email)->first();

            if (!empty($user_data)) {
                $user_data->otp = $this->helper->createOtp();
                $user_data->otp_expire_time = Carbon::now()->addMinutes(15);
                $user_data->save();
                $response = $this->loginHelper->sendEmail($this->emailTemplateModel::RESEND_OTP, $request, $user_data->otp);
                if ($response['code'] == 200) {
                    return ['message' => trans("api_message.otp_resend_success"), 'response' => 200];
                } else {
                    return ['message' => $response['message'], 'response' => 201];
                }
            } else {
                return ['message' => trans("api_message.user_not_found"), 'response' => 201];
            }
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'response' => 201];
        }
    }

    /**
     * Forget Password
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPassword($request)
    {
        try {
            $user_data = $this->userModel->where('email', $request->email)->first();
            if (!empty($user_data)) {
                if (!empty($user_data->social_id)) {
                    return ['message' => str_replace('{{SOCIAL}}', Config::get('constants.account_type')[$user_data->account_type], "{{SOCIAL}} Social forget password"), 'response' => 201];
                } else {
                    $user_data->otp = $this->helper->createOtp();
                    $user_data->otp_expire_time = Carbon::now()->addMinutes(15);
                    $user_data->save();
                    $response = $this->loginHelper->sendEmail($this->emailTemplateModel::FORGET_PASSWORD, $request, $user_data->otp);
                    if ($response['code'] == 200) {
                        return ['message' => trans("api_message.forget_password_sent_otp"), 'response' => 200];
                    } else {
                        return ['message' => $response['message'], 'response' => 201];
                    }
                }
            } else {
                return ['message' => trans("api_message.no_account_found_with_email"), 'response' => 201];
            }
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'response' => 201];
        }
    }

    /**
     * Reset Password
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword($request)
    {
        try {
            $user_data = $this->userModel->where('email', $request->email)->first();
            if (!Hash::check($request->password, $user_data->password)) {
                $user_data->password = Hash::make($request->password);
                if ($user_data->save()) {
                    return ['message' => trans("api_message.reset_password_success"), 'response' => 200];
                }
                return ['message' => trans("api_message.reset_password_fail"), 'response' => 200];
            } else {
                return ['message' => trans("api_message.old_and_new_not_same"), 'response' => 201];
            }
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'response' => 201];
        }
    }
}
