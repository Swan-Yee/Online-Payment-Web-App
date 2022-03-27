<?php

namespace App\Helpers;

use App\Wallet;
use App\Transcation;

class UUIDGenerate{
    public static function accountNumber(){
        $number = mt_rand(1000000000000000,99999999999999999);

        if(Wallet::where('account_number',$number)->exists()){
            self::accountNumber();
        }
        return $number;
    }

    public static function refNumber(){
        $number = mt_rand(100000000000,999999999999);

        if(Transcation::where('ref_no',$number)->exists()){
            self::refNumber();
        }

        return $number;
    }

    public static function trxId(){
        $number = mt_rand(100000000000,999999999999);

        if(Transcation::where('trx_id',$number)->exists()){
            self::trxId();

        }

        return $number;
    }
}
