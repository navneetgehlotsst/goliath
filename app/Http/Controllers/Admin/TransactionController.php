<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Mail,Hash,File,Auth,DB,Helper,Exception,Session,Redirect;
use Carbon\Carbon;
use App\Mail\ContactMail;

class TransactionController extends Controller
{
    public function index()
    {
        return view('admin.transactions.index');
    }

    public function getalltransaction(Request $request){
        $transactions = Transaction::select('transactions.id','transactions.user_id','transactions.amount','transactions.transaction_id','transactions.transaction_type','users.full_name')->join('users', 'transactions.user_id', '=', 'users.id')->orderBy('id','desc')->get();
        return response()->json(['data' => $transactions]);
    }
}
