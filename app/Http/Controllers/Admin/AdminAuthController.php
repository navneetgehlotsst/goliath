<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Mail, DB, Hash, Validator, Session, File,Exception;
use App\Models\{
    Question,
    MatchInnings,
    InningsOver,
    OverQuestions,
    CompetitionMatches,
    Prediction,
    User
};

class AdminAuthController extends Controller
{

    public function index()
    {
        try{
            if(Auth::user()) {
                $user = Auth::user();
                if($user->role == "admin") {
                    return redirect()->route('admin.dashboard');
                }else{
                    return back()->with("error","Opps! You do not have access this");
                }
            }else{
                return redirect()->route('admin.login');
            }

        }
        catch(Exception $e){
            return back()->with("error",$e->getMessage());
        }
    }



    public function login()
    {
        return view("admin.auth.login");
    }

    public function registration()
    {
        return view("admin.auth.registration");
    }

    public function postLogin(Request $request)
    {
        try{
            $request->validate([
                "email" => "required",
                "password" => "required",
            ]);
            $user = User::where('role','admin')->where('email',$request->email)->first();
            if($user){
                $credentials = $request->only("email", "password");
                if(Auth::attempt([
                        'email' => $request->email,
                        'password' => $request->password,
                        'role' => function ($query) {
                            $query->where('role','admin');
                        }
                    ]))
                {
                    return redirect()->route("admin.dashboard")->with("success", "Welcome to your dashboard.");
                }
                return back()->with("error","Invalid credentials");
            }else{
                return back()->with("error","Invalid credentials");
            }

        }
        catch(Exception $e){
            return back()->with("error",$e->getMessage());
        }
    }

    public function postRegistration(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|min:6",
        ]);

        $data = $request->all();
        $check = $this->create($data);

        return redirect("admin.dashboard")->with("success","Great! You have Successfully loggedin");
    }

    public function create(array $data)
    {
        return User::create([
            "name" => $data["name"],
            "email" => $data["email"],
            "password" => Hash::make($data["password"]),
        ]);
    }

    public function showForgetPasswordForm()
    {
        return view("admin.auth.forgot-password");
    }

    public function submitForgetPasswordForm(Request $request)
    {
        try{
            $request->validate([
                "email" => "required|email|exists:users",
            ]);

            $token = Str::random(64);

            DB::table("password_resets")->insert([
                "email" => $request->email,
                "token" => $token,
                "created_at" => Carbon::now(),
            ]);

            $new_link_token = url("admin/reset-password/" . $token);
            Mail::send("admin.email.forgot-password",["token" => $new_link_token, "email" => $request->email],
                function ($message) use ($request) {
                    $message->to($request->email);
                    $message->subject("Reset Password");
                }
            );
            return redirect()->route("admin.login")->with("success","We have e-mailed your password reset link!");
        }
        catch(Exception $e){
            return back()->with("error",$e->getMessage());
        }

    }

    public function showResetPasswordForm($token)
    {
        try{
            $user = DB::table("password_resets")->where("token", $token)->first();
            $email = $user->email;
            return view("admin.auth.reset-password", ["token" => $token,"email" => $email,]);
        }
        catch(Exception $e){
            return back()->with("error",$e->getMessage());
        }
    }

    public function submitResetPasswordForm(Request $request)
    {
        try{
            $request->validate([
                "email" => "required|email|exists:users",
                "password" => "required|string|min:6|confirmed",
                "password_confirmation" => "required",
            ]);

            $updatePassword = DB::table("password_resets")->where(["email" => $request->email,"token" => $request->token])->first();

            if (!$updatePassword) {
                return back()->withInput()->with("error", "Invalid token!");
            }

            $user = User::where("email", $request->email)->update(["password" => Hash::make($request->password)]);

            DB::table("password_resets")->where(["email" => $request->email])->delete();

            return redirect()->route("admin.login")->with("success","Your password has been changed successfully!");
        }
        catch(Exception $e){
            return back()->with("error",$e->getMessage());
        }
    }

    public function changePassword()
    {
        return view("admin.auth.change-password");
    }

    public function updatePassword(Request $request)
    {
        try{
            $request->validate([
                "old_password" => "required",
                "new_password" => "required|confirmed",
            ]);
            #Match The Old Password
            if (!Hash::check($request->old_password, auth()->user()->password)) {
                return back()->with("error", "Old Password Doesn't match!");
            }
            #Update the new Password
            User::whereId(auth()->user()->id)->update([
                "password" => Hash::make($request->new_password),
            ]);
            return back()->with("success", "Password changed successfully!");
        }
        catch(Exception $e){
            return back()->with("error",$e->getMessage());
        }
    }



    public function logout()
    {
        try{
            Session::flush();
            Auth::logout();
            return redirect()->route("admin.login")->withSuccess('Logout Successful!');
        }
        catch(Exception $e){
            return back()->with("error",$e->getMessage());
        }
    }

    public function adminProfile()
    {
        try {
            $user = Auth::user();
            return view("admin.auth.profile", ['user' => $user]);
        } catch (\Throwable $e) {
            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }

    }

    public function updateAdminProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $data = $request->all();

            $validator = Validator::make($data, [
                "first_name" => "required",
                "last_name" => "required",
                "phone" => "required|min:9|unique:users,phone," . $user->id,
                "email" => "required|email|unique:users,email," . $user->id,
                "avatar" => "sometimes|image|mimes:jpeg,jpg,png|max:5000"
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput($request->all())->withErrors($validator);
            }

            DB::beginTransaction();

            if ($request->hasFile('avatar')) {
                $user->avatar = $this->handleAvatarUpload($request->file('avatar'));
            }

            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'full_name' => $request->first_name . " " . $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Profile updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function adminDashboard()
    {
        // User count Query
        $userCount = Cache::remember('active_user_count', now()->addMinutes(10), function() {
            return User::where('status', 'active')->count();
        });

        // Total Prediction Count
        $predictionMonthCount = Cache::remember('total_prediction_count', now()->addMinutes(10), function() {
            return Prediction::groupBy('predictions.over_id')->count();
        });

        // Get Top 5 latest predicted matches
        $latestPredictions = Cache::remember('latest_predictions', now()->addMinutes(10), function() {
            return Prediction::with('competitionMatch')
                ->groupBy('predictions.match_id')
                ->where('status','complete')
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get();
        });

        // monthly prediction
        $monthpredicted = [];
        $currentDate = now();

        for ($i = 0; $i < 12; $i++) {
            $monthYear = $currentDate->copy()->subMonths($i)->format('Y-m');


            $monthlypredicteduser = Prediction::select(
                'user_id',
                'over_id',
                DB::raw('MONTHNAME(created_at) AS Month'),
                DB::raw('SUM(IF(result = "W", 1, 0)) AS win_count')
            )
            ->whereYear('created_at', Carbon::parse($monthYear)->year)
            ->whereMonth('created_at', Carbon::parse($monthYear)->month)
            ->groupBy('user_id', 'over_id')
            ->get();



            $total_golith_winner = 0;
            $total_winner = 0;
            $total_loser = 0;

            foreach ($monthlypredicteduser as $predictedvalue) {
                if($predictedvalue->win_count == 8) {
                    $total_golith_winner ++;
                } elseif ($predictedvalue->win_count < 8 && $predictedvalue->win_count >= 5) {
                    $total_winner++;
                } else {
                    $total_loser++;
                }
            }

            $monthpredicted[$i]['month'] = $monthYear;
            $monthpredicted[$i]['total_golith_winner'] = $total_golith_winner;
            $monthpredicted[$i]['total_winner'] = $total_winner;
            $monthpredicted[$i]['total_loser'] = $total_loser;

        }
        // Sort the $monthpredicted array by month in ascending order
        usort($monthpredicted, function($a, $b) {
            return strcmp($a['month'], $b['month']);
        });

        // Initialize arrays to store sorted data
        $month = [];
        $golith_winner = [];
        $winner = [];
        $loser = [];

        // Populate sorted data into respective arrays
        foreach ($monthpredicted as $key => $value) {
            $month[$key] = $value['month'];
            $golith_winner[$key] = $value['total_golith_winner'];
            $winner[$key] = $value['total_winner'];
            $loser[$key] = $value['total_loser'];
        }
        // Convert PHP arrays to JavaScript arrays
        $monthjson = json_encode($month);
        $golithwinnerjson = json_encode($golith_winner);
        $winnerjson = json_encode($winner);
        $loserjson = json_encode($loser);


        return view("admin.dashboard.index" , compact('userCount','predictionMonthCount','latestPredictions','monthjson','golithwinnerjson','winnerjson','loserjson'));
    }

    private function handleAvatarUpload($file)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $folder = 'uploads/user/';
        $path = public_path($folder);

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $file->move($path, $filename);

        return $folder . $filename;
    }

}
