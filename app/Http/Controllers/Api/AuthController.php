<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Mail,Hash,File,DB,Helper,Auth;
use App\Mail\UserRegisterVerifyMail;
use App\Models\AppUser;
use App\Models\User;
use App\Models\CheckOtp;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use App\Models\SplashScreen;

// Api Responce
use App\Http\Response\ApiResponse;



class AuthController extends Controller
{

    public function splashScreens(){
        $splash_screens = SplashScreen::select('type', 'heading', 'content', 'image')->get();

        $splash_screens->transform(function ($screen) {
            if ($screen->image) {
                $screen->image = asset($screen->image);
            }
            return $screen;
        });

        return ApiResponse::successResponse($splash_screens, 'Splash Screen Data Found');

    }

    public function sendOtp(Request $request){
        $data = $request->all();

        $commonRules = [
            'full_name' => 'sometimes|string',
            'device_type' => 'sometimes',
            'device_token' => 'sometimes',
            'type' => 'required|in:login,register',
        ];

        $typeSpecificRules = [
            'email' => 'nullable|email',
            'phone' => 'nullable|numeric|digits_between:4,12',
            'country_code' => 'numeric',
        ];

        if ($data['type'] == 'register') {
            $typeSpecificRules['email'] .= '|unique:users';
            $typeSpecificRules['phone'] .= '|unique:users';
            $typeSpecificRules['country_code'] .= '|sometimes';
        }

        $validator = Validator::make($data, array_merge($commonRules, $typeSpecificRules));

        if ($validator->fails()) {
            return ApiResponse::errorResponse($validator->errors()->first());
        }

        try {
            // Generate OTP
            $otp = '1234';
            $otpExpiry = now()->addMinutes(120);
            if (strstr($data['country_code'],"+")) {
                $countrycode = trim($data['country_code'],"+");
            } else {
                $countrycode = $data['country_code'];
            }

            // Begin transaction
            DB::beginTransaction();

            if ($data['type'] == 'login') {
                if(isset($data['phone'])){
                    $user = User::where('phone',$data['phone'])->where('country_code',$countrycode)->where('role','user')->first();
                }else{
                    $user = User::where('email',$data['email'])->where('role','user')->first();
                }

                if (!$user) {
                    return ApiResponse::errorResponse('User does not exist');
                }

                if ($user->status == 'inactive') {
                    return ApiResponse::errorResponse('Your account is not activated yet.');
                }

                $user->update([
                    'device_type' => $data['device_type'],
                    'device_token' => $data['device_token'],
                    'otp' => $otp,
                    'otp_expired' => $otpExpiry,
                ]);

                $checkotp = new CheckOtp();
                $checkotp->country_code = $countrycode ?? null;
                $checkotp->data = $data['phone'] ?? $data['email'];
                $checkotp->otp = $otp;
                $checkotp->otp_expire_time = $otpExpiry;
                $checkotp->save();

                $dataUser['usredetail'] = [
                    'email' => $data['email'] ?? "",
                    'phone' => $data['phone'] ?? "",
                    'country_code' => '+'.$countrycode ?? "",
                    'device_type' => $data['device_type'],
                    'device_token' => $data['device_token'],
                    'type' => $data['type'],
                ];
            } else {

                $checkotp = new CheckOtp();
                $checkotp->country_code = $countrycode ?? null;
                $checkotp->data = $data['phone'] ?? $data['email'];
                $checkotp->otp = $otp;
                $checkotp->otp_expire_time = $otpExpiry;
                $checkotp->save();
                $dataUser = [
                    'full_name' => $data['full_name'],
                    'email' => $data['email'] ?? "",
                    'phone' => $data['phone'] ?? "",
                    'country_code' => '+'.$countrycode ?? "",
                    'device_type' => $data['device_type'],
                    'device_token' => $data['device_token'],
                    'type' => $data['type'],
                ];
            }

            // Commit transaction
            DB::commit();

            $message = isset($data['phone']) ? 'OTP is sent to your phone. Please verify it to complete your registration.' : 'OTP is sent to your email. Please verify it to complete your registration.';
            return ApiResponse::successResponse($dataUser,$message);
        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }

    }


    // public function verifyOtp(Request $request){
    //     $data = $request->all();

    //     // Validation
    //     $validator = Validator::make($data, [
    //         'phone' => 'nullable|numeric',
    //         'country_code' => 'nullable|numeric',
    //         'email' => 'nullable|email',
    //         'otp' => 'required|max:4',
    //         'type' => 'required|in:login,register',
    //     ]);

    //     if ($validator->fails()) {
    //         return ApiResponse::errorResponse($validator->errors()->first());
    //     }

    //     // Common user retrieval logic
    //     $userQuery = User::where('otp', $data['otp']);

    //     if (isset($data['phone'])) {
    //         $userQuery->where('phone', $data['phone']);
    //     } else {
    //         $userQuery->where('email', $data['email']);
    //     }

    //     $appuser = $userQuery->first();

    //     if (!$appuser) {
    //         return ApiResponse::errorResponse('Invalid detail.');
    //     }

    //     $currentTimestamp = now()->timestamp;

    //     if ($currentTimestamp > $appuser->otp_expired) {
    //         return ApiResponse::errorResponse('Otp time has expired.');
    //     }

    //     try {
    //         DB::beginTransaction();

    //         // Update user's OTP and OTP expiration
    //         $user = User::find($appuser->id);
    //         $user->update(['otp' => '', 'otp_expired' => null]);

    //         // Make User Login
    //         $input = isset($data['phone']) ? ['phone' => $appuser->phone, 'country_code' => $appuser->country_code] : ['email' => $appuser->email];
    //         $token = JWTAuth::fromUser($user);

    //         $message = $data['type'] == 'login' ? 'Login successfully!' : 'Account created successfully!';
    //         $dataResponse = [
    //             'access_token' => $token,
    //             'token_type' => 'bearer',
    //             'user' => $this->getUserDetail($user->id),
    //         ];

    //         DB::commit();

    //         return ApiResponse::successResponse($dataResponse, $message);
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         return ApiResponse::errorResponse($e->getMessage());
    //     }
    // }


    public function verifyOtp(Request $request){
        $data = $request->all();

        // Validation
        $commonRules = [
            'full_name' => 'sometimes|string',
            'device_type' => 'sometimes',
            'device_token' => 'sometimes',
            'type' => 'required|in:login,register',
        ];

        $typeSpecificRules = [
            'email' => 'nullable|email',
            'phone' => 'nullable|numeric|digits_between:4,12',
            'country_code' => 'numeric',
        ];

        if ($data['type'] == 'register') {
            $typeSpecificRules['email'] .= '|unique:users';
            $typeSpecificRules['phone'] .= '|unique:users';
            $typeSpecificRules['country_code'] .= '|sometimes';
        }

        $validator = Validator::make($data, array_merge($commonRules, $typeSpecificRules));

        if ($validator->fails()) {
            return ApiResponse::errorResponse($validator->errors()->first());
        }

        if (strstr($data['country_code'],"+")) {
            $countrycode = trim($data['country_code'],"+");
        } else {
            $countrycode = $data['country_code'];
        }

        // Common user retrieval logic
        $userQuery = CheckOtp::where('otp', $data['otp']);

        if (isset($data['phone'])) {
            $userQuery->where('data', $data['phone'])->where('country_code', $countrycode);
        } else {
            $userQuery->where('data', $data['email']);
        }

        $otp = $userQuery->first();

        if (!$otp) {
            return ApiResponse::errorResponse('Invalid Otp.');
        }

        $currentTimestamp = now()->timestamp;

        if ($currentTimestamp > $otp->otp_expire_time) {
            return ApiResponse::errorResponse('Otp time has expired.');
        }
        CheckOtp::find($otp->id)->forceDelete();
        try {
            if($data['type'] == 'login'){
                DB::beginTransaction();
                // Otp Delete
                //CheckOtp::where('id', $otp->id)->forceDelete();
                if(isset($data['phone'])){
                    $user = User::where('phone',$data['phone'])->where('country_code',$countrycode)->where('role','user')->first();
                }else{
                    $user = User::where('email',$data['email'])->where('role','user')->first();
                }

            }else{

                $fullName = explode(" ", $data['full_name']);
                $firstName = $fullName[0] ?? '';
                $lastName = $fullName[1] ?? '';


                $user = new User();
                $user->first_name = $firstName;
                $user->last_name = $lastName;
                $user->full_name = $data['full_name'];
                $user->slug = Helper::slug('users', $data['full_name']);
                $user->email = $data['email'] ?? null;
                $user->phone = $data['phone'] ?? null;
                $user->country_code = $countrycode ?? '91';
                $user->device_type = $data['device_type'] ?? null;
                $user->device_token = $data['device_token'] ?? null;
                $user->save();
            }

             // Make User Login
             $input = isset($data['phone']) ? ['phone' => $data['phone'], 'country_code' => $countrycode] : ['email' => $data['email']];
             $token = JWTAuth::fromUser($user);

             $dataResponse = [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'user' => $this->getUserDetail($user->id),
                ];

            $message = $data['type'] == 'login' ? 'Login successfully!' : 'Account created successfully!';
            return ApiResponse::successResponse($dataResponse, $message);
        } catch (Exception $e) {
            DB::rollback();
            return ApiResponse::errorResponse($e->getMessage());
        }
    }

    public function register(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'full_name' => 'required|string',
            'email' => 'nullable|email|unique:users',
            'phone' => 'nullable|numeric|digits_between:4,12|unique:users',
            'country_code' => 'sometimes|numeric',
            'device_type' => 'sometimes',
            'device_token' => 'sometimes',
            'type' => 'required|in:login,register',
        ], [
            'email.email' => 'Invalid email format. Please try again',
            'email.unique' => 'This email is already registered. Please sign in or use a different email',
            'phone.phone' => 'Invalid phone format. Please try again',
            'phone.unique' => 'This phone is already registered. Please sign in or use a different phone',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return ApiResponse::errorResponse($message);
        }

        try {
            $fullName = explode(" ", $data['full_name']);
            $firstName = $fullName[0] ?? '';
            $lastName = $fullName[1] ?? '';

            // Generate OTP
            $otp = '1234';
            $otpExpiry = now()->addMinutes(120);

            // Begin transaction
            DB::beginTransaction();

            // Delete existing user based on email or phone
            if (isset($data['phone'])) {
                AppUser::where('phone', $data['phone'])->delete();
            } else {
                AppUser::where('email', $data['email'])->delete();
            }

            // Create new user
            $appUser = new AppUser();
            $appUser->first_name = $firstName;
            $appUser->last_name = $lastName;
            $appUser->full_name = $data['full_name'];
            $appUser->slug = Helper::slug('users', $data['full_name']);
            $appUser->email = $data['email'] ?? null;
            $appUser->phone = $data['phone'] ?? null;
            $appUser->country_code = $data['country_code'] ?? '91';
            $appUser->otp = $otp;
            $appUser->otp_expired = $otpExpiry;
            $appUser->device_type = $data['device_type'] ?? null;
            $appUser->device_token = $data['device_token'] ?? null;
            $appUser->save();

            // Commit transaction
            DB::commit();

            // Prepare response
            $datauser['user'] = $appUser;
            $message = isset($data['phone']) ? 'Otp is sent on your phone! Please verify otp to complete your registration' : 'Otp is sent on your email! Please verify otp to complete your registration';

            return ApiResponse::successResponsenoData($message);

        } catch (Exception $e) {
            // Rollback transaction in case of exception
            DB::rollBack();

            $message = $e->getMessage();
            return ApiResponse::errorResponse($message);
        }

    }


    public function login(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => 'sometimes|email',
            'phone' => 'sometimes|numeric|digits_between:4,12',
            'country_code' => 'sometimes|numeric',
            'device_type' => 'required|in:ios,android',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return ApiResponse::errorResponse($message);
        }

        try {
            if(isset($data['phone'])){
                $user = User::where('phone',$data['phone'])->where('role','user')->first();
            }else{
                $user = User::where('email',$data['email'])->where('role','user')->first();
            }

            if (!$user) {
                $message = 'User not exists';
                return ApiResponse::errorResponse($message);
            }

            if ($user->status == 'inactive') {
                $message = 'Your account is not activated yet.';
                return ApiResponse::errorResponse($message);
            }
            $code = '1234';
            $futureDate = now()->addMinutes(120);

            $user->update([
                'device_type' => $data['device_type'],
                'device_token' => $data['device_token'],
                'otp' => $code,
                'otp_expired' => $futureDate,
            ]);

            $messresponce = isset($data['phone']) ? 'Otp is sent on your phone! Please verify otp to complete your registration' : 'Otp is sent on your email! Please verify otp to complete your registration';

            $datauser['user'] = $user;

            return ApiResponse::successResponsenoData($messresponce);
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return ApiResponse::errorResponse($message);
        }
    }

    public function getUser()
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user) {
                $message = 'User not found.';
                return ApiResponse::errorResponse($message);
            }
            else{
                $message = 'User found successfully.';
                $datauser['user'] = $this->getUserDetail($user->id);
                return ApiResponse::successResponse($datauser, $message);
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            return ApiResponse::errorResponse($message);
        }
    }


    public function updateProfile(Request $request){
        $data   =   $request->all();
        $id = auth()->user()->id;

        $validator = Validator::make($data, [
            'full_name' => 'sometimes|string',
            'email'     =>  'sometimes|email|unique:users,email,'.$id,
            'phone'     =>  'sometimes|numeric|digits_between:4,12|unique:users,phone,'.$id,
            'avatar'  =>  'sometimes|mimes:jpeg,jpg,png|max:5000',
            'device_type' => 'sometimes',
            'device_token' => 'sometimes',
        ]);


        if($validator->fails()) {
            $message = $validator->errors()->first();
            return ApiResponse::errorResponse($message);
        }

        try{

            $user = User::find($id);
            foreach($data as $key => $value){
                if(!empty($data[$key])){
                    if($key == 'avatar'){
                        $file = $request->file('avatar');
                        if($file){
                            $filename   = time().$file->getClientOriginalName();
                            $filename   =  Helper::cleanImage($filename);
                            $folder = 'uploads/user/';
                            $path = public_path($folder);
                            if(!File::exists($path)) {
                                File::makeDirectory($path, $mode = 0777, true, true);
                            }
                            $file->move($path, $filename);
                            $user->avatar   = $folder.$filename;
                        }
                    }
                    else{
                        $user->$key = $value;
                    }
                }
            }
            $user->save();

            $message = 'Profile updated successfully!';
            $datauser['user'] = $this->getUserDetail($user->id);
            return ApiResponse::successResponse($datauser, $message);
        }
        catch(Exception $e){
            $message = $e->getMessage();
            return ApiResponse::errorResponse($message);
        }
    }


    public function getUserDetail($user_id){
        $user = User::select('id','full_name','email','phone','country_code')->where('id',$user_id)->first();

        $user->id = $user->id ?? "";
        $user->full_name = $user->full_name ?? "";
        $user->email = $user->email ?? "";
        $user->phone = $user->phone ?? "";
        $user->country_code = '+'.$user->country_code ?? "";

        return $user;
    }

    public function logout() {
        JWTAuth::parseToken()->invalidate(true);
        $message = 'User successfully signed out.';
        return ApiResponse::successResponsenoData($message);
    }

    public function deleteAccount()
    {
        try{
            DB::beginTransaction();
            $user = Auth::user();
            $user->email = uniqid().'_delete_'.$user->email;
            $user->phone = uniqid().'_delete_'.$user->phone;
            $user->status = 'inactive';
            $user->save();
            DB::commit();
            User::find($user->id)->delete();
            Auth::logout();
            $message = 'Account deleted successfully.';
            return ApiResponse::successResponsenoData($message);

        }
        catch(Exception $e){
            DB::rollback();
            $message = $e->getMessage();
            return ApiResponse::errorResponse($message);
        }


    }
}
