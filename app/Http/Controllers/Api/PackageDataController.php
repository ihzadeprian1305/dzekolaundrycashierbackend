<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PackageDataController extends Controller
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
            
            $packageProcess = Package::orderBy('name');

            if($request->search){
                $packageProcess->where('name', 'like', '%'.$request->search.'%')->orWhere('type', 'like', '%'.$request->search.'%');
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Paket telah Berhasil Didapat',
                'data' => $packageProcess->get()->skip($request->skip)->take($request->take)->values(),
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
                'name' => ['required','string','min:2','max:255',Rule::unique('packages', 'name')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                'price' => 'required|min:3|max:8',
                'type' => 'required|in:Kiloan,Potongan',
            ]);
    
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            Package::create([
                'name' => $request->name,
                'price' => $request->price,
                'type' => $request->type,
            ]);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Paket telah Berhasil Ditambah',
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
                'name' => ['required','string','min:2','max:255',Rule::unique('packages', 'name')->ignore($request->id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                'price' => 'required|min:3|max:8',
                'type' => 'required|in:Kiloan,Potongan',
            ]);
    
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            Package::where('id', $request->id)->update([
                'name' => $request->name,
                'price' => $request->price,
                'type' => $request->type,
            ]);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Paket telah Berhasil Diubah',
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

            Package::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Paket telah Berhasil Dihapus',
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
