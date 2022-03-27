<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\UUIDGenerate;
use App\Http\Controllers\Controller;
use App\Transcation;
use App\User;
use App\Wallet;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class WalletController extends Controller
{
    public function index(){
        return view('backend.wallet.index');
    }

    public function ssd(){
        $wallets = Wallet::with('user');
        return DataTables::of($wallets)
        ->addColumn('account_person',function($each){
            $user = $each->user;
            if($user){
                return '<p><strong>Name</strong> : '.$user->name.'</p><p><strong>Email</strong> : '.$user->email.'</p><p><strong>Phone</strong> : '.$user->phone.'</p>';
            }
                return '-';
        })
        ->editColumn('created_at',function($each){
            return Carbon::parse($each->created_at)->format('Y-m-d H:i:s');
        })
        ->editColumn('updated_at',function($each){
            return Carbon::parse($each->updated_at)->format('Y-m-d H:i:s');
        })
        ->editColumn('amount',function($each){
            return number_format($each->amount);
        })
        ->rawColumns(['account_person'])
        ->make(true);
    }

    public function addAmount(){
        $users = User::orderBy('name')->get();
        return view('backend.wallet.addAmount',compact('users'));
    }

    public function storeAddAmount(Request $request){
        $request->validate(
            [
                'user_id' => 'required|exists:App\User,id',
                'amount' => 'required|integer',
            ],
            [
                'user_id.required' => "Need To choose User"
            ]
        );

        DB::beginTransaction();
        try{
            $to_account = User::with('wallet')->where('id',$request->user_id)->firstOrFail();
            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount',$request->amount);
            $to_account_wallet->update();

            $to_account_transaction = new Transcation;
            $to_account_transaction->ref_no = UUIDGenerate::refNumber();
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $request->amount;
            $to_account_transaction->source_id = 0;
            $to_account_transaction->description = $request->description;
            $to_account_transaction->save();

            // send Noti
            $title = 'E-money Received From Magicy!';
            $message = 'Your wallet received ' . number_format($request->amount) . ' MMK from Magicy';
            $sourceable_id = 0;
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
            return redirect()->route('admin.wallet.index')->with('transfer_success','Successfully Fail');
        }catch(\Exception $error){
            return back()->withErrors(['fail'=>'Somethings wrong!' . $error->getMessage()])->withInput();
        }
    }

    public function reduceAmount(){
        $users = User::orderBy('name')->get();
        return view('backend.wallet.reduceAmount',compact('users'));
    }

    public function storeReduceAmount(Request $request){
        $request->validate(
            [
                'user_id' => 'required|exists:App\User,id',
                'amount' => 'required|integer',
            ],
            [
                'user_id.required' => "Need To choose User"
            ]
        );

        DB::beginTransaction();
        try{
            $to_account = User::with('wallet')->where('id',$request->user_id)->firstOrFail();

            if($to_account->wallet->amount < $request->amount){
                throw new Exception('The Amount is greater than wallet amount');
            }

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->decrement('amount',$request->amount);
            $to_account_wallet->update();

            $to_account_transaction = new Transcation;
            $to_account_transaction->ref_no = UUIDGenerate::refNumber();
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 2;
            $to_account_transaction->amount = $request->amount;
            $to_account_transaction->source_id = 0;
            $to_account_transaction->description = $request->description;
            $to_account_transaction->save();

            // send Noti
            $title = 'E-money Reduce By Magicy!';
            $message = 'Your balance amount reduce by ' . number_format($request->amount) . ' MMK by Magicy';
            $sourceable_id = 1;
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
            return redirect()->route('admin.wallet.index')->with('transfer_success','Successfully Fail');
        }catch(\Exception $error){
            return back()->withErrors(['fail'=>'Somethings wrong!' . $error->getMessage()])->withInput();
        }
    }
}
