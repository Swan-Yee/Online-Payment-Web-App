<?php

namespace App\Http\Controllers\Api;

use App\Helpers\UUIDGenerate;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransferValidate;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\NotificationDetailResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransactionDetailResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Notifications\GeneralNotification;
use App\Transcation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class PageController extends Controller
{
    public function profile(){
        $user = auth()->user();

        $data= new ProfileResource($user);
        return success("message",$data);
    }

    public function transaction(Request $request){
        $authUser = auth()->user();
        $transactions = Transcation::with('user', 'source')->orderBy('created_at', 'DESC')->where('user_id', $authUser->id);

        if($request->type){
            $transactions = $transactions->where('type',$request->type);
        }

        if($request->date){
            $transactions = $transactions->whereDate('created_at',$request->date);
        }

        $transactions = $transactions->paginate(5);

        $data = TransactionResource::collection($transactions)->additional(['result'=>1,'message'=>'success']);

        return $data;

        return success('Success',$data);
    }

    public function transactionDetail($id){
        $user = Auth()->user();
        $transactions = Transcation::with('user','source')->where('trx_id',$id)->where('user_id',$user->id)->first();

        // return $transactions;

        $data = new TransactionDetailResource($transactions);

        return $data;

        return success('Success',$data);
    }

    public function noti(){
        $user = auth()->user();
        $notis = $user->notifications()->paginate(5);

        $data = NotificationResource::collection($notis)->additional(['result' => 1, 'message' => 'success']);

        return $data;
    }

    public function notiDetail($id){
        $authUser = auth()->user();
        $notification = $authUser->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        $data = new NotificationDetailResource($notification);
        return success('success', $data);
    }

    public function accountVerify(Request $request){

        $authUser = auth()->user();

        if($authUser->phone != $request->phone){
            $user=User::where('phone',$request->phone)->first();
            if($user){
                $data = new UserResource($user);
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                ]);
            };
        }
        else{
           return response()->json(['status' => 'err']);
        }

        return response()->json([
            'status' => 'fail'
        ]);
    }

    public function transferConfirm(TransferValidate $request){
        $authUser= auth()->user();

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

        return success('success', [
            'from_name' => $from_account->name,
            'from_phone' => $from_account->phone,

            'to_name' => $to_account->name,
            'to_phone' => $to_account->phone,

            'amount' => $amount,
            'description' => $description,
            'hash_value' => $hash_value,
        ]);
    }

    public function transferComplete(Request $request){
        $authUser = auth()->user();

        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $from_account = $authUser;
        $to_account = User::where('phone',$to_phone)->first();

        $str = $to_phone.$amount.$description;

        $hash_value2 = hash_hmac('sha256',trim($str),'magicpay#123');
        if($hash_value !== $hash_value2){
            return fail('The given data is invalid',null);
        }

        // Check Amount is Over 1k or not
        if($amount < 1000){
            return fail('The Amount must be at least 1000 MMK.',null);
        }

        // check amount of wallet
        if($amount > $authUser->wallet->amount){
            return fail('Not amount money in your account!',null);
        }

        // Check user send userself or not
        if($authUser->phone == $to_phone){
            return fail("Don't send your account yourself",null);
        }

        // Check To Account
        if(!$to_account){
            return fail("To account is invalid",null);
        }

        // Check Both Two account have wallet?
        if(!$from_account->wallet || !$to_account->wallet){
            return fail("Something wrong. The given data is invalid",null);
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
            return success('Successfully Transfer',[
                'trx_id'=>$from_account_transaction->trx_id
            ]);
        }catch(\Exception $error){
            return fail('Somethings wrong!',null);
        }
    }

    public function scanPayForm(Request $request){
        $to_account = User::where('phone',$request->phone)->first();
        if(!$to_account){
            return fail('QR is Valid',null);
        }

        return success('Successfully Scan',[
            'To_account'=>$to_account
        ]);
    }

    public function scanPayConfirm(TransferValidate $request){
        $authUser= auth()->user();
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $to_account = User::where('phone',$to_phone)->first();
        $from_account = $authUser;

        $str = $to_phone.$amount.$description;
        $hash_value2 = hash_hmac('sha256',trim($str),'magicpay#123');

        if($hash_value !== $hash_value2){
            return fail('The given data is invalid',null);
        }

        // Check Amount
        if($amount < 1000){
            return fail('The Amount must be at least 1000 MMK.',null);
            return back()->withErrors(['amount'=>'The Amount must be at least 1000 MMK.'])->withInput();
        }

        // check amount of wallet
        if($amount > $authUser->wallet->amount){
            return fail('Not enough money in your account!',null);
        }

        // Check user send userself or not
        if($authUser->phone == $to_phone){
            return fail("Don't send your account yourself",null);
        }

        // Check To Account
        if(!$to_account){
            return fail('To account is invalid',null);
        }

        // Check Both Two account have wallet?
        if(!$from_account->wallet || !$to_account->wallet){
            return fail('Something wrong. The given data is invalid',null);
        }

        return success('success', [
            'from_name' => $from_account->name,
            'from_phone' => $from_account->phone,

            'to_name' => $to_account->name,
            'to_phone' => $to_account->phone,

            'amount' => $amount,
            'description' => $description,
            'hash_value' => $hash_value,
        ]);
    }

    public function scanPayComplete(Request $request){
        $authUser = auth()->user();

        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $from_account = $authUser;
        $to_account = User::where('phone',$to_phone)->first();

        $str = $to_phone.$amount.$description;

        $hash_value2 = hash_hmac('sha256',trim($str),'magicpay#123');

        if($amount < 1000){
            return fail('The Amount must be at least 1000 MMK.',null);
        }

        // check amount of wallet
        if($amount > $authUser->wallet->amount){
            return fail('Not amount money in your account!',null);
        }

        // Check user send userself or not
        if($authUser->phone == $to_phone){
            return fail("Don't send your account yourself",null);
        }

        // Check To Account
        if(!$to_account){
            return fail("To account is invalid",null);
        }

        // Check Both Two account have wallet?
        if(!$from_account->wallet || !$to_account->wallet){
            return fail("Something wrong. The given data is invalid",null);
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
            return success('Successfully Transfer',[
                'trx_id'=>$from_account_transaction->trx_id
            ]);
        }catch(\Exception $error){
            return fail('Somethings wrong!',null);
        }
    }

}
