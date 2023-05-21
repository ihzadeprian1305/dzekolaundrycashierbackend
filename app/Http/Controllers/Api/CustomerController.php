<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function fetch(Request $request){
        try{
            if ($request->except([ 'search'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $customerProcess = Customer::orderBy('name');

            if($request->search){
                $customerProcess->where('name', 'like', '%'.$request->search.'%')->orWhere('phone_number', 'like', '%'.$request->search.'%');
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Pelanggan telah Berhasil Didapat',
                'data' => $customerProcess->get(),
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
