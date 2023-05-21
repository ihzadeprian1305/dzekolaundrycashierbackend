<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function fetch(Request $request){
        try{
            if ($request->except(['type', 'search'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'type' => 'string|in:Kiloan,Potongan'
            ]);

            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            $packageProcess = Package::orderBy('name');
            
            if($request->type){
                $packageProcess->where('type', $request->type);
            }
            if($request->search){
                $packageProcess->where('name', 'like', '%'.$request->search.'%');
            }
            
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Paket telah Berhasil Didapat',
                'data' => $packageProcess->get(),
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
