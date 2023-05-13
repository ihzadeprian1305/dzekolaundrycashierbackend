<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function callback()
    {
        try{
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        $notification = new Notification();
        
        $status = $notification->transaction_status;
        $type = $notification->payment_type;
        $fraud = $notification->fraud_status;
        $order_id = $notification->order_id;

        $oi = substr($order_id, 0, -14);

        $transaction = Transaction::findOrFail($oi);

        if($status == 'capture'){
            if($type == 'credit_card'){
                if($fraud == 'challenge'){
                    $transaction->payment_status = 'Tertunda';
                }else{
                    $transaction->status = 3;
                    $transaction->payment_status = 'Selesai';
                }
            }
        }
        else if($status == 'settlement'){
            $transaction->status = 3;
            $transaction->payment_status = 'Selesai';
        }
        else if($status == 'pending'){
            $transaction->payment_status = 'Tertunda';
        }
        else if($status == 'deny'){
            $transaction->payment_status = 'Dibatalkan';
        }
        else if($status == 'expire'){
            $transaction->payment_status = 'Dibatalkan';
        }
        else if($status == 'cancel'){
            $transaction->payment_status = 'Dibatalkan';
        }

        $transaction->save();

        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'Callback telah Berhasil Diterima',
        ], 200);
    } catch(QueryException $error){
        return response()->json([
            'status' => 401,
            'success' => false,
            'message' => $error,
        ], 401);
    } catch(Exception $error){
        return response()->json([
            'status' => 401,
            'success' => false,
            'message' => $error,
        ], 401);
    }
    }
    
    public function success()
    {
        return view('midtrans.success');
    }
    
    public function unfinish()
    {
        return view('midtrans.unfinish');
    }
    
    public function error()
    {
        return view('midtrans.error');
    }
}
