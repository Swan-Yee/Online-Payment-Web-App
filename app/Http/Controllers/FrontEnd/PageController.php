<?php

namespace App\Http\Controllers\FrontEnd;

use App\Helpers\UUIDGenerate;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransferValidate;
use App\Http\Requests\UpdatePassword;
use App\Notifications\GeneralNotification;
use App\Transcation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class PageController extends Controller
{
    public function home(){
        $user = Auth::guard('web')->user();
        return view('frontend.home',compact('user'));
    }

    public function profile(){
        $user = Auth::guard('web')->user();
        return view('frontend.profile',compact('user'));
    }

    public function updatePassword(){
        return view('frontend.update_password');
    }

    public function updatePasswordStore(UpdatePassword $request){
        $old_password = $request->old_password;
        $new_password = $request->new_password;

        $user= Auth::guard('web')->user();

        if(Hash::check($old_password, $user->password)){
            $user->password = Hash::make($new_password);
            $user->update();

        $title = "Change Password";
        $message = "Your Password Change Successfully";
        $sourceable_id = $user->id;
        $sourceable_type = User::class;
        $web_link = url('profile');
        $deep_link = [
                'target' => 'profile',
                'parameter' => null
            ];

        Notification::send([$user], new GeneralNotification($title,$message,$sourceable_id,$sourceable_type,$web_link,$deep_link));


        return redirect()->route('profile')->with('create','Successfully Updated!');
        }

        return back()->withErrors(['fail'=>'Old password is not corret.']);
    }

    public function wallet(){
        $authUser = auth()->guard('web')->user();
        return view('frontend.wallet',compact('authUser'));
    }

    public function transfer(){
        $authUser = auth()->guard('web')->user();
        return view('frontend.transfer',compact('authUser'));
    }

    public function transferConfirm(TransferValidate $request){
        $authUser= auth()->guard('web')->user();
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $to_account = User::where('phone',$to_phone)->first();
        $from_account = $authUser;

        $str = $to_phone.$amount.$description;
        $hash_value2 = hash_hmac('sha256',trim($str),'magicpay#123');

        if($hash_value !== $hash_value2){
            return back()->withErrors(['err'=>'The given data is invalid'])->withInput();
        }

        // Check Amount
        if($amount < 1000){
            return back()->withErrors(['amount'=>'The Amount must be at least 1000 MMK.'])->withInput();
        }

        // check amount of wallet
        if($amount > $authUser->wallet->amount){
            return back()->withErrors(['amount'=>"Not enough money in your account!"]);
        }

        // Check user send userself or not
        if($authUser->phone == $to_phone){
            return back()->withErrors(['to_phone'=>"Don't send your account yourself"])->withInput();
        }

        // Check To Account
        if(!$to_account){
            return back()->withErrors(['to_phone'=>'To account is invalid'])->withInput();
        }

        // Check Both Two account have wallet?
        if(!$from_account->wallet || !$to_account->wallet){
            return back()->withErrors(['fail'=>'Something wrong. The given data is invalid'])->withInput();
        }

        return view('frontend.transfer_confirm',compact('from_account','to_account','amount','description','hash_value'));
    }

    public function transferComplete(Request $request){
        $authUser = auth()->guard('web')->user();

        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $from_account = $authUser;
        $to_account = User::where('phone',$to_phone)->first();

        $str = $to_phone.$amount.$description;

        $hash_value2 = hash_hmac('sha256',trim($str),'magicpay#123');

        if($hash_value !== $hash_value2){
            return back()->withErrors(['err'=>'The given data is invalid'])->withInput();
        }

        // Check Amount is Over 1k or not
        if($amount < 1000){
            return back()->withErrors(['amount'=>'The Amount must be at least 1000 MMK.'])->withInput();
        }

        // check amount of wallet
        if($amount > $authUser->wallet->amount){
            return back()->withErrors(['amount'=>"Not amount money in your account!"]);
        }

        // Check user send userself or not
        if($authUser->phone == $to_phone){
            return back()->withErrors(['to_phone'=>"Don't send your account yourself"])->withInput();
        }

        // Check To Account
        if(!$to_account){
            return back()->withErrors(['to_phone'=>'To account is invalid'])->withInput();
        }

        // Check Both Two account have wallet?
        if(!$from_account->wallet || !$to_account->wallet){
            return back()->withErrors(['fail'=>'Something wrong. The given data is invalid'])->withInput();
        }

        DB::beginTransaction();
        try{
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount',$amount);
            $from_account_wallet->update();

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount',$amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerate::refNumber();

            $from_account_transaction = new Transcation;
            $from_account_transaction->ref_no = $ref_no;
            $from_account_transaction->trx_id = UUIDGenerate::trxId();
            $from_account_transaction->user_id = $from_account->id;
            $from_account_transaction->type = 2;
            $from_account_transaction->amount = $amount;
            $from_account_transaction->source_id = $to_account->id;
            $from_account_transaction->description = $description;
            $from_account_transaction->save();

            $to_account_transaction = new Transcation;
            $to_account_transaction->ref_no = $ref_no;
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount;
            $to_account_transaction->source_id = $from_account->id;
            $to_account_transaction->description = $description;
            $to_account_transaction->save();

      // From Noti
            $title = 'E-money Transfered!';

            $message = 'Your wallet transfered ' . number_format($amount) . ' MMK to ' . $to_account->name . ' ( ' . $to_account->phone . ' )';

            $sourceable_id = $from_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/' . $from_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $to_account_transaction->trx_id
                ],
            ];

            Notification::send([$from_account], new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link,$deep_link));

            // To Noti
            $title = 'E-money Received!';
            $message = 'Your wallet received ' . number_format($amount) . ' MMK from ' . $from_account->name . ' ( ' . $from_account->phone . ' )';
            $sourceable_id = $to_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/' . $to_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $to_account_transaction->trx_id
                ],
            ];

            Notification::send([$to_account], new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link,$deep_link));

            DB::commit();
            return redirect()->route('transaction.detail',$from_account_transaction->trx_id)->with('transfer_success','Successfully Transfer');
        }catch(\Exception $error){
            return back()->withErrors(['fail'=>'Somethings wrong!' . $error->getMessage()])->withInput();
        }
    }

    public function toAccountVerify(Request $request){
        $authUser = auth()->guard('web')->user();

        if($authUser->phone != $request->phone){
            $user=User::where('phone',$request->phone)->first();
            if($user){
                return response()->json([
                    'status' => 'success',
                    'data' => $user,
                ]);
            };
        }
        else{
           return response()->json([
                   'status' => 'err',
               ]);
        }

        return response()->json([
            'status' => 'fail'
        ]);
    }

    public function passwordCheck(Request $request){
        if(!$request->password){
            return response()->json([
                'status' => 'fail',
                'message' => 'Please Fail the Password'
            ]);
        }

        $authUser = auth()->guard('web')->user();
        if(Hash::check($request->password, $authUser->password)){
            return response()->json([
                'status' => 'success',
                'message' => 'The password is correct!'
            ]);
        }
            return response()->json([
                'status' => 'fail',
                'message' => 'Password is incorrect'
            ]);
    }

    public function transaction(Request $request){
        $authUser = auth()->guard('web')->user();
        $transactions = Transcation::where('user_id',$authUser->id)->orderBy('created_at','desc');

        if($request->type){
            $transactions = $transactions->where('type',$request->type);
        }

        if($request->date){
            $transactions = $transactions->whereDate('created_at',$request->date);
        }
        $transactions = $transactions->paginate(5);

        return view('frontend.transaction',compact('transactions'));
    }

    public function transactionDetail($trx_id){
        $authUser= auth()->guard('web')->user();
        $transaction = Transcation::with('user','source')->where('user_id',$authUser->id)->where('trx_id',$trx_id)->first();
        return view('frontend.transaction_detail',compact('transaction'));
    }

    public function transferHash(Request $request){
        $str = $request->to_phone.$request->amount.$request->description;

        $hash_value = hash_hmac('sha256',trim($str),'magicpay#123');

        return response()->json([
            'status' => 'success',
            'data' => $hash_value,
        ]);
    }

    public function qrReceive(){
        $authUser = Auth()->guard('web')->user();
        return view('frontend.receive_qr',compact('authUser'));
    }

    public function qrScan(){
        return view('frontend.scan_qr');
    }

    public function scanPayForm(Request $request){
        $from_account = Auth()->guard('web')->user();
        $to_account = User::where('phone',$request->phone)->first();
        if(!$to_account){
            return back()->withErrors(['fail'=>'QR is valid']);
        }

        return view('frontend.scan_pay_form',compact('to_account','from_account'));
    }

    public function scanPayConfirm(TransferValidate $request){
        $authUser= auth()->guard('web')->user();
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $to_account = User::where('phone',$to_phone)->first();
        $from_account = $authUser;

        $str = $to_phone.$amount.$description;
        $hash_value2 = hash_hmac('sha256',trim($str),'magicpay#123');

        if($hash_value !== $hash_value2){
            return back()->withErrors(['err'=>'The given data is invalid'])->withInput();
        }

        // Check Amount
        if($amount < 1000){
            return back()->withErrors(['amount'=>'The Amount must be at least 1000 MMK.'])->withInput();
        }

        // check amount of wallet
        if($amount > $authUser->wallet->amount){
            return back()->withErrors(['amount'=>"Not enough money in your account!"]);
        }

        // Check user send userself or not
        if($authUser->phone == $to_phone){
            return back()->withErrors(['to_phone'=>"Don't send your account yourself"])->withInput();
        }

        // Check To Account
        if(!$to_account){
            return back()->withErrors(['to_phone'=>'To account is invalid'])->withInput();
        }

        // Check Both Two account have wallet?
        if(!$from_account->wallet || !$to_account->wallet){
            return back()->withErrors(['fail'=>'Something wrong. The given data is invalid'])->withInput();
        }

        return view('frontend.qr_transfer_confirm',compact('from_account','to_account','amount','description','hash_value'));
    }

    public function scanPayComplete(Request $request){
        $authUser = auth()->guard('web')->user();

        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $from_account = $authUser;
        $to_account = User::where('phone',$to_phone)->first();

        $str = $to_phone.$amount.$description;

        $hash_value2 = hash_hmac('sha256',trim($str),'magicpay#123');

        if($hash_value !== $hash_value2){
            return back()->withErrors(['err'=>'The given data is invalid'])->withInput();
        }

        // Check Amount is Over 1k or not
        if($amount < 1000){
            return back()->withErrors(['amount'=>'The Amount must be at least 1000 MMK.'])->withInput();
        }

        // check amount of wallet
        if($amount > $authUser->wallet->amount){
            return back()->withErrors(['amount'=>"Not enough money in your account!"]);
        }

        // Check user send userself or not
        if($authUser->phone == $to_phone){
            return back()->withErrors(['to_phone'=>"Don't send your account yourself"])->withInput();
        }

        // Check To Account
        if(!$to_account){
            return back()->withErrors(['to_phone'=>'To account is invalid'])->withInput();
        }

        // Check Both Two account have wallet?
        if(!$from_account->wallet || !$to_account->wallet){
            return back()->withErrors(['fail'=>'Something wrong. The given data is invalid'])->withInput();
        }

        DB::beginTransaction();
        try{
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount',$amount);
            $from_account_wallet->update();

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount',$amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerate::refNumber();

            $from_account_transaction = new Transcation;
            $from_account_transaction->ref_no = $ref_no;
            $from_account_transaction->trx_id = UUIDGenerate::trxId();
            $from_account_transaction->user_id = $from_account->id;
            $from_account_transaction->type = 2;
            $from_account_transaction->amount = $amount;
            $from_account_transaction->source_id = $to_account->id;
            $from_account_transaction->description = $description;
            $from_account_transaction->save();

            $to_account_transaction = new Transcation;
            $to_account_transaction->ref_no = $ref_no;
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount;
            $to_account_transaction->source_id = $from_account->id;
            $to_account_transaction->description = $description;
            $to_account_transaction->save();

            // From Noti
            $title = 'E-money Transfered!';

            $message = 'Your wallet transfered ' . number_format($amount) . ' MMK to ' . $to_account->name . ' ( ' . $to_account->phone . ' )';

            $sourceable_id = $from_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/' . $from_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $from_account_transaction->trx_id
                ],
            ];

            Notification::send([$from_account], new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link,$deep_link));

            // To Noti
            $title = 'E-money Received!';
            $message = 'Your wallet received ' . number_format($amount) . ' MMK from ' . $from_account->name . ' ( ' . $from_account->phone . ' )';
            $sourceable_id = $to_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/' . $to_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $to_account_transaction->trx_id
                ],
            ];

            Notification::send([$to_account], new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link,$deep_link));

            DB::commit();
            return redirect()->route('transaction.detail',$from_account_transaction->trx_id)->with('transfer_success','Successfully Transfer');
        }catch(\Exception $error){
            return back()->withErrors(['fail'=>'Somethings wrong!' . $error->getMessage()])->withInput();
        }
    }
}
