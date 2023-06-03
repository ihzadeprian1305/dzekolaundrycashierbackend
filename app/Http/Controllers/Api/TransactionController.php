<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use ErrorException;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;

class TransactionController extends Controller
{
    public function fetch(Request $request){
        try{
            if ($request->except(['limit', 'status', 'search', 'latest'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $transaction = Transaction::with('transaction_items.packages', 'customers', 'created_by', 'updated_by');
            
            if($request->limit){
                $transaction->limit($request->limit);
            }
            if($request->status){
                $transaction->where('status', $request->status);
            }
            if($request->search){
                $transaction->whereUuid('id', '%'.$request->search.'%')->orWhereRelation('customers', 'name', 'like', '%'.$request->search.'%')->orWhereRelation('customers', 'phone_number', 'like', '%'.$request->search.'%');
            }
            
            if($request->latest == 'true'){
                $transaction->latest('updated_at');
            } else{
                $transaction->oldest('updated_at');
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Transaksi telah Berhasil Didapat',
                'data' => $transaction->get(),
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
    
    public function post(Request $request){
        try{
        // if ($request->except(['transaction_items', 'transaction_items.*.id', 'total_price', 'status'])) {
        //     return response()->json([
        //         'status' => 401,
        //         'success' => false,
        //         'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
        //     ], 401);
        // }

        $validatedData = Validator::make($request->all(), [
            'customer_id' => 'required',
            'transaction_items' => 'required|array',
            'transaction_items.*.id' => 'exists:packages,id',
            'total_price' => 'required',
            'status' => 'required|in:1,2,3',
        ]);
    
        if ($validatedData->fails()) {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => $validatedData->errors()->first(),
            ], 401);
        }

        $transaction = Transaction::create([
            'customer_id' => $request->customer_id,
            'total_price' => $request->total_price,
            'status' => $request->status,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        foreach($request->transaction_items as $packages){
            if($packages['quantity']){
                TransactionItem::create([
                    'package_id' => $packages['id'],
                    'transaction_id' => $transaction->id,
                    'quantity'=> $packages['quantity'],
                ]);
            }
        }

        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'Data Transaksi telah Berhasil Ditambah dengan Status Sedang Diproses',
            'data' => $transaction->with('transaction_items.packages', 'customers', 'created_by', 'updated_by')->latest()->first(),
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
    
    public function putStatusToReadyToBeTaken(Request $request){
        try{
        if ($request->except(['_method', 'id',])) {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
            ], 401);
        }

        $validatedData = Validator::make($request->all(), [
            'id' => 'required',
        ]);
    
        if ($validatedData->fails()) {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => $validatedData->errors()->first(),
            ], 401);
        }

        Transaction::where('id', $request->id)->update([
            'status' => 2,
        ]);

        $transaction = Transaction::where('id', $request->id)->with('transaction_items.packages', 'customers', 'created_by', 'updated_by')->first();

        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'Status Transaksi Telah Berhasil Diubah dengan Status Siap Diambil',
            'data' => $transaction,
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

    public function putCheckoutCash(Request $request){
        try{
            if ($request->except(['_method', 'id',])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized');
            Config::$is3ds = config('services.midtrans.is_3ds');

            $authorization = base64_encode(Config::$serverKey.':');

            $responseDelete = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic '.$authorization,
            ])->delete('https://api.sandbox.midtrans.com/v1/payment-links/'.$request->id);

            $responseDeleteJSON = json_decode($responseDelete->body());

            if(!$responseDelete->status() == 200 || !$responseDelete->status() == 404){
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $responseDeleteJSON->error_messages[0],
                ], 401);
            }

            Transaction::where('id', $request->id)->update([
                'status' => 3,
                'payment_type' => 'Tunai',
                'payment_status' => 'Selesai',
            ]);
    
            $transaction = Transaction::where('id', $request->id)->with('transaction_items.packages', 'customers', 'created_by', 'updated_by')->first();

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Status Transaksi Telah Berhasil Diubah dengan Status Selesai',
                'data' => $transaction,
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
    
    public function putCheckoutCashless(Request $request){
        try{
            if ($request->except(['_method', 'id',])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            $transactionData = Transaction::where('id', $request->id)->with('transaction_items.packages', 'customers', 'created_by', 'updated_by')->first();

            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized');
            Config::$is3ds = config('services.midtrans.is_3ds');

            $authorization = base64_encode(Config::$serverKey.':');
            $now = Carbon::now()->format('YmdHis');

            $midtrans = [
                'transaction_details' => [
                    'order_id' => $transactionData->id,
                    'gross_amount' => $transactionData->total_price,
                    'payment_link_id' => 'pembayarandzekolaundry-'.$now.'-'.$transactionData->id,
                ],
                'usage_limit' =>  1,
                'enabled_payments' => [
                    'gopay',
                    'shopeepay',
                    'bri_va',
                    'bca_va',
                    'bni_va',
                    'mandiri_va',
                    'bsi_va',
                    'permata_va',
                ],
                'customer_details' => [
                    'first_name' => $transactionData->customers->name,
                    'phone' => '+'.$transactionData->customers->phone_number,
                ],
            ];

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic '.$authorization,
            ])->post('https://api.sandbox.midtrans.com/v1/payment-links', $midtrans);
            
            if($response->status() == 409){
                $responseDelete = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic '.$authorization,
                ])->delete('https://api.sandbox.midtrans.com/v1/payment-links/'.$request->id);

                $responseDeleteJSON = json_decode($responseDelete->body());

                if(!$responseDelete->status() == 200){
                    return response()->json([
                        'status' => 401,
                        'success' => false,
                        'message' => $responseDeleteJSON->error_messages[0],
                    ], 401);
                }

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic '.$authorization,
                ])->post('https://api.sandbox.midtrans.com/v1/payment-links', $midtrans);
            }

            $responseJSON = json_decode($response->body());
            
            Transaction::where('id', $request->id)->update([
                'payment_type' => 'Nontunai',
                'payment_status' => 'Tertunda',
            ]);
            
            if($response->status() != 200){
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $responseJSON->error_messages[0],
                ], 401);
            }

            $data = [
                'transactions' => $transactionData,
                'order_id' => $responseJSON->order_id,
                'payment_url' => $responseJSON->payment_url,
            ];
            
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Tautan Transaksi telah Berhasil Ditambah',
                'data' => $data,
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

    public function checkStatus(Request $request)
    {
        try{
            if ($request->except('id')) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }
    
            $transaction = Transaction::where('id', $request->id)->where('status', 3)->where('payment_type', 'Nontunai')->where('payment_status', 'Selesai')->with('transaction_items.packages', 'customers', 'created_by', 'updated_by')->first();

            if(!$transaction){
                return response()->json([
                    'status' => 401,
                    'success' => true,
                    'message' => 'Status Transaksi Masih Tertunda',
                ], 401);
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Status Transaksi Telah Berubah Menjadi Status Selesai',
                'data' => $transaction,
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

    public function download(Request $request){
        try{
            if ($request->except('id')) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'id' => 'required',
            ]);
        
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            $transaction = Transaction::where('id', $request->id)->first();
            $pdf = Pdf::loadView('transaction_invoice.index', [
                'transaction' => $transaction, 
            ]);
            return $pdf->download('invoice.pdf');
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

    public function delete(Request $request)
    {
        try {
            if ($request->except('id')) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'id' => 'required|string|max:255',
            ]);
    
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            TransactionItem::where('transaction_id', $request->id)->delete();
            Transaction::where('id', $request->id)->delete();


            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Transaksi telah Berhasil Dihapus',
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
}
