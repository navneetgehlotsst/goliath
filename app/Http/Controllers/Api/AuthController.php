<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Mail,Hash,File,DB,Helper,Auth;
use App\Mail\UserRegisterVerifyMail;
use App\Models\AppUser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use App\Models\SplashScreen;



class AuthController extends Controller
{

    public function splashScreens(){
        $base_url = asset('/');
        $splash_screens = SplashScreen::select('type','heading','content','image')->get();
        foreach ($splash_screens as $key => $screen) {
            if($screen['image']){
                $screen['image'] = $base_url.$screen['image'];
            }
        }
        return response()->json([
            'status' => true,
            'data' => $splash_screens,
        ],200);

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
        ],[
            'email.email' => 'Invalid email format. Please try again',
            'email.unique' => 'This email is already registered. Please sign in or use a different email',
            'phone.phone' => 'Invalid phone format. Please try again',
            'phone.unique' => 'This phone is already registered. Please sign in or use a different phone',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' =>  $validator->errors()->first(),
            ],200);
        }
        try
        {
            $fullname = $words = explode(" ", $data['full_name']);
            $firstname = $fullname['0']?? '';
            $lastname = $fullname['1']?? '';

            // $code = rand(1000,9999);
            $code = '1234';
            $date = date('Y-m-d H:i:s');
            $currentDate = strtotime($date);
            $futureDate = $currentDate+(60*120);

            if(isset($data['phone'])){
                AppUser::where('phone',$data['phone'])->delete();
            }else{
                AppUser::where('email',$data['email'])->delete();
            }

            $app_user = new AppUser();
            $app_user->first_name = $firstname;
            $app_user->last_name = $lastname;
            $app_user->full_name = $data['full_name'];
            $app_user->slug = Helper::slug('users',$data['full_name']);
            $app_user->email = $request->email?? null;
            $app_user->phone = $request->phone?? null;
            $app_user->country_code = $request->country_code?? '91';
            $app_user->otp = $code;
            $app_user->otp_expired = $futureDate;
            $app_user->device_type = $request->device_type;
            $app_user->device_token = $request->device_token;
            $app_user->save();

            if(isset($data['phone'])){
                $messresponce = 'Otp is sent on your phone! Please verify otp to complete your registration';
            }else{
                $messresponce = 'Otp is sent on your email! Please verify otp to complete your registration';
            }

            return response()->json([
                'status' => true,
                'message' => $messresponce,
            ],200);

        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ],200);
        }

    }

    public function verifyOtp(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'phone' => "sometimes|numeric",
            'country_code' => "sometimes|numeric",
            'email' => "sometimes|email",
            'otp' => "required|max:4",
            'type'=>'required|in:login,register',
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' =>  $validator->errors()->first(),
            ],200);
        }


        $date = date('Y-m-d H:i:s');
        $currentTime = strtotime($date);
            if ($data['type'] == "login") {
            if(isset($data['phone'])){
                $appuser = User::where('phone',$data['phone'])->where('otp',$data['otp'])->first();
            }else{
                $appuser = User::where('email',$data['email'])->where('otp',$data['otp'])->first();
            }
        }else {
            if(isset($data['phone'])){
                $appuser = AppUser::where('phone',$data['phone'])->where('otp',$data['otp'])->first();
            }else{
                $appuser = AppUser::where('email',$data['email'])->where('otp',$data['otp'])->first();
            }
        }

        if(!$appuser){
            return response()->json([
                'status' => false,
                'message'=>'Invalid detail.'
            ],200);

        }
        if($currentTime > $appuser->otp_expired){
            return response()->json([
                'status' => true,
                'message' =>  'Otp time is expired.',
            ],200);
        }

        try{
            if ($data['type'] != "login") {
                DB::beginTransaction();
                $user = new User();
                $user->first_name = $appuser->first_name;
                $user->last_name = $appuser->last_name;
                $user->full_name = $appuser->full_name;
                $user->email = $appuser->email ?? null;
                $user->slug = $appuser->slug;
                $user->phone = $appuser->phone ?? null;
                $user->device_type = $appuser->device_type ?? '';
                $user->device_token = $appuser->device_token ?? '';
                $user->role = 'user';
                $user->status = 'active';
                $user->save();
                DB::commit();
            }else{
                $user = User::find($appuser->id);
                $user->otp = "";
                $user->otp_expired = "";
                $user->save();
            }


            // Mail::to($user->email)->send(new UserRegisterVerifyMail($user));
            //============ Make User Login ==========//

            if(isset($data['phone'])){
                $input['phone'] = $appuser->phone;
                $input['country_code'] = $appuser->country_code;
            }else{
                $input['email'] = $appuser->email;
            }
            $token = JWTAuth::fromUser($user);
            if ($data['type'] != "login") {
                $appuser->delete();
            }

            if ($data['type'] == "login") {
                $messagelogin = 'Login successfully!';
            }else{
                $messagelogin = 'Account created successfully!';
            }

            return response()->json([
                'status' => true,
                'message'=>$messagelogin,
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => $this->getUserDetail($user->id),
            ],200);

        }catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ],200);
        }

    }


    public function login(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' => 'sometimes|email',
            'phone' => 'sometimes|numeric|digits_between:4,12',
            'country_code' => 'sometimes|numeric',
            'device_type'=>'required|in:ios,android',
            'device_token'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ],200);
        }

        try
        {
            if(isset($data['phone'])){
                $user = User::where('phone',$data['phone'])->where('role','user')->first();
            }else{
                $user = User::where('email',$data['email'])->first();
            }
            if(!$user){
                return response()->json([
                    'status' => false,
                    'message' => 'User not exists',
                ]);
            }

            if($user->status == 'inactive'){
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not activacted yet.',
                ]);
            }


            // $code = rand(1000,9999);
            $code = '1234';
            $date = date('Y-m-d H:i:s');
            $currentDate = strtotime($date);
            $futureDate = $currentDate+(60*120);
            $user = User::find($user->id);
            $user->device_type = $data['device_type'];
            $user->device_token = $data['device_token'];
            $user->otp = $code;
            $user->otp_expired = $futureDate;
            $user->save();

            if(isset($data['phone'])){
                $messresponce = 'Otp is sent on your phone! Please verify otp to complete your registration';
            }else{
                $messresponce = 'Otp is sent on your email! Please verify otp to complete your registration';
            }

            return response()->json([
                'status' => true,
                'message' => $messresponce,
            ],200);
        }
        catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ],200);
        }
    }

    public function getUser()
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user) {
                return response()->json([
                    'status' => false,
                    'message'=>'User not found.'
                ],200);
            }
            else{
                return response()->json([
                    'status' => true,
                    'message' => 'User found successfully.',
                    'user' => $this->getUserDetail($user->id),
                ],200);
            }
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ],200);
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
            return response()->json(array(
                'status' => false,
                'message' =>  $validator->errors()->first()
            ),200);
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

            return response()->json(array(
                'status' => true,
                'message' => 'Profile updated successfully!',
                'user' => $this->getUserDetail($user->id),
            ),200);
        }
        catch(Exception $e){
            return response()->json(array(
                'status' => false,
                'message' => $e->getMessage()
            ),200);
        }
    }


    public function getUserDetail($user_id){
        $user = User::where('id',$user_id)->first();
        return $user;
    }

    public function logout() {
        JWTAuth::parseToken()->invalidate(true);
        return response()->json(array(
            'status' => true,
            'message' => 'User successfully signed out.'
        ),200);
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
            return response()->json(array(
                'status' => true,
                'message' => 'Account deleted successfully.'
            ),200);

        }
        catch(Exception $e){
            DB::rollback();
            return response()->json(array(
                'status' => false,
                'message' => $e->getMessage()
            ),200);
        }


    }
}
