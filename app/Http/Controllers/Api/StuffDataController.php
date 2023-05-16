<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stuff;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StuffDataController extends Controller
{
    public function fetch(Request $request){
        try{
            if ($request->except('search')) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }
            
            $stuffProcess = Stuff::orderBy('name');

            if($request->search){
                $stuffProcess->where('name', 'like', '%'.$request->search.'%')->orWhere('type', 'like', '%'.$request->search.'%');
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Barang telah Berhasil Didapat',
                'data' => $stuffProcess->get(),
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
            if ($request->except(['name', 'price', 'type'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255|unique:stuffs,name,null,id,deleted_at,null',
                'price' => 'required|min:3|max:8',
                'type' => 'required|in:Kilogram,Buah,Potong,Botol,Set,Liter,Rol,Kotak,Lembar,Pak',
            ]);
    
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            Stuff::create([
                'name' => $request->name,
                'price' => $request->price,
                'type' => $request->type,
            ]);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Barang telah Berhasil Ditambah',
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
            if ($request->except(['_method', 'id', 'name', 'price', 'type'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'id' => 'required|string|max:255',
                'name' => 'required|string|min:2|max:255|unique:stuffs,name,'.$request->id.',id,deleted_at,null',
                'price' => 'required|min:3|max:8',
                'type' => 'required|in:Kilogram,Buah,Potong,Botol,Set,Liter,Rol,Kotak,Lembar,Pak',
            ]);
    
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            Stuff::where('id', $request->id)->update([
                'name' => $request->name,
                'price' => $request->price,
                'type' => $request->type,
            ]);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Barang telah Berhasil Diubah',
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

            Stuff::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Barang telah Berhasil Dihapus',
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
