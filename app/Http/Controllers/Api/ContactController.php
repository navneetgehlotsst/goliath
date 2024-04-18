<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Mail, Hash, File, Auth, DB, TimeHelper, Exception, Session, Redirect, Validator ,Helper;
use Carbon\Carbon;
use App\Models\Contact;

// Api Responce
use App\Http\Response\ApiResponse;



class ContactController extends Controller
{
    public function submitContact(Request $request){
        $input = $request->all();

        // Validation
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return ApiResponse::errorResponse($message);
        }

        try {
            // Create Contact
            $data['contact'] = Contact::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'message' => $input['message'],
            ]);

            $message = 'Thank you for getting in touch!';
            return ApiResponse::successResponse($data, $message);
        } catch (Exception $e) {
            $message = $e->getMessage();
            return ApiResponse::errorResponse($message);
        }

    }
}
