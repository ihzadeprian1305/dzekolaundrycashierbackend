<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stuff;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class StuffController extends Controller
{
    public function fetch(Request $request){
        try{
            if ($request->except(['search'])) {
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
}
