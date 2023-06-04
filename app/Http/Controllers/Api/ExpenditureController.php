<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expenditure;
use App\Models\ExpenditureItem;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenditureController extends Controller
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

            $expenditure = Expenditure::with('expenditure_items.stuffs', 'created_by', 'updated_by');
            
            if($request->search){
                $expenditure->where('id', 'like', '%'.$request->search.'%')->orWhere('information', 'like', '%'.$request->search.'%');
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Pengeluaran telah Berhasil Didapat',
                'data' => $expenditure->latest()->get(),
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
        // if ($request->except(['expenditure_items', 'expenditure_items.*.id', 'total_price', 'status'])) {
        //     return response()->json([
        //         'status' => 401,
        //         'success' => false,
        //         'message' => 'Please Input the Field Correctly',
        //     ], 401);
        // }

        $validatedData = Validator::make($request->all(), [
            'information' => 'required|min:2|max:255',
            'expenditure_items' => 'required|array',
            'expenditure_items.*.id' => 'exists:stuffs,id',
            'total_price' => 'required',
        ]);
    
        if ($validatedData->fails()) {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => $validatedData->errors()->first(),
            ], 401);
        }

        $expenditure = Expenditure::create([
            'information' => $request->information,
            'total_price' => $request->total_price,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        foreach($request->expenditure_items as $stuffs){
            if($stuffs['quantity']){
                ExpenditureItem::create([
                    'stuff_id' => $stuffs['id'],
                    'expenditure_id' => $expenditure->id,
                    'quantity'=> $stuffs['quantity'],
                ]);
            }
        }

        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'Data Pengeluaran telah Berhasil Ditambah',
            'data' => $expenditure->with('expenditure_items.stuffs', 'created_by', 'updated_by')->latest()->first(),
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

            ExpenditureItem::where('expenditure_id', $request->id)->delete();
            Expenditure::where('id', $request->id)->delete();


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
