<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerDataController extends Controller
{
    public function fetch(Request $request){
        try{
            if ($request->except(['skip', 'take', 'search'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }
            
            $customerProcess = Customer::orderBy('name');

            // if($request->skip){
            //     $customerProcess->skip($request->skip);
            // }
            if($request->take){
                $customerProcess->take($request->take);
            }
            if($request->search){
                $customerProcess->where('name', 'like', '%'.$request->search.'%')->orWhere('phone_number', 'like', '%'.$request->search.'%');
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Pelanggan telah Berhasil Didapat',
                'data' => $customerProcess->get()->skip(3),
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

    public function post(Request $request)
    {
        try {
            if ($request->except(['name', 'phone_number'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255',
                'phone_number' => ['required','min:10','max:16',Rule::unique('customers', 'phone_number')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
            ]);
    
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            Customer::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
            ]);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Pelanggan telah Berhasil Ditambah',
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
    
    public function put(Request $request)
    {
        try {
            if ($request->except(['_method', 'id', 'name', 'phone_number'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'id' => 'required|string|max:255',
                'name' => 'required|string|min:2|max:255',
                'phone_number' => ['required','min:10','max:16',Rule::unique('customers', 'phone_number')->ignore($request->id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
            ]);
    
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            Customer::where('id', $request->id)->update([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
            ]);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Pelanggan telah Berhasil Diubah',
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

            Customer::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Pelanggan telah Berhasil Dihapus',
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
