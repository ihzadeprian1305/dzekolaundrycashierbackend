<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expenditure;
use App\Models\Transaction;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RecapitulationController extends Controller
{
    public function fetch(Request $request){
        try{
            if ($request->except(['show_by', 'date', 'monthAndYear'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $today = Carbon::today();
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::SUNDAY);
            $endOfWeek = Carbon::now()->endOfWeek(Carbon::SATURDAY);

            if($request->show_by == null || $request->date == null || ($request->show_by == 'Hari' && $request->date == null) || ($request->show_by == 'Bulan' && $request->date == null) || $request->show_by == 'Hari Ini'){
                $transactionTotal = Transaction::where('status', '3')->whereDate('updated_at', $today)->sum('total_price');
                $transactionCount = Transaction::where('status', '3')->whereDate('updated_at', $today)->count();
                $expenditureTotal = Expenditure::whereDate('updated_at', $today)->sum('total_price');
                $expenditureCount = Expenditure::whereDate('updated_at', $today)->count();
                $transactionItem = DB::table('transactions')
                    ->select('total_price', 'updated_at',  DB::raw("'transaction' as type"))->where('status', '3')->whereDate('updated_at', $today)->where('deleted_at', null);
                $expenditureItem = DB::table('expenditures')
                    ->select('total_price', 'updated_at',  DB::raw("'expenditure' as type"))->whereDate('updated_at', $today)->where('deleted_at', null);
            }
            if($request->show_by == '1 Minggu Terakhir'){    
                $transactionTotal = Transaction::where('status', '3')->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->sum('total_price');
                $transactionCount = Transaction::where('status', '3')->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->count();
                $expenditureTotal = Expenditure::whereBetween('updated_at', [$startOfWeek, $endOfWeek])->sum('total_price');
                $expenditureCount = Expenditure::whereBetween('updated_at', [$startOfWeek, $endOfWeek])->count();
                $transactionItem = DB::table('transactions')
                    ->select('total_price', 'updated_at',  DB::raw("'transaction' as type"))->where('status', '3')->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->where('deleted_at', null);
                $expenditureItem = DB::table('expenditures')
                    ->select('total_price', 'updated_at',  DB::raw("'expenditure' as type"))->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->where('deleted_at', null);
            }
            if($request->show_by == '2 Minggu Terakhir'){    
                $transactionTotal = Transaction::where('status', '3')->whereBetween('updated_at', [$startOfWeek->subWeek(1), $endOfWeek])->sum('total_price');
                $transactionCount = Transaction::where('status', '3')->whereBetween('updated_at', [$startOfWeek->subWeek(1), $endOfWeek])->count();
                $expenditureTotal = Expenditure::whereBetween('updated_at', [$startOfWeek->subWeek(1), $endOfWeek])->sum('total_price');
                $expenditureCount = Expenditure::whereBetween('updated_at', [$startOfWeek->subWeek(1), $endOfWeek])->count();
                $transactionItem = DB::table('transactions')
                    ->select('total_price', 'updated_at',  DB::raw("'transaction' as type"))->where('status', '3')->whereBetween('updated_at', [$startOfWeek->subWeek(1), $endOfWeek])->where('deleted_at', null);
                $expenditureItem = DB::table('expenditures')
                    ->select('total_price', 'updated_at',  DB::raw("'expenditure' as type"))->whereBetween('updated_at', [$startOfWeek->subWeek(1), $endOfWeek])->where('deleted_at', null);
            }
            if($request->show_by == 'Tanggal' && $request->date){
                $date = Carbon::parse($request->date);
                $transactionTotal = Transaction::where('status', '3')->whereDate('updated_at', $date)->sum('total_price');
                $transactionCount = Transaction::where('status', '3')->whereDate('updated_at', $date)->count();
                $expenditureTotal = Expenditure::whereDate('updated_at', $date)->sum('total_price');
                $expenditureCount = Expenditure::whereDate('updated_at', $date)->count();
                $transactionItem = DB::table('transactions')
                    ->select('total_price', 'updated_at',  DB::raw("'transaction' as type"))->where('status', '3')->whereDate('updated_at', $date)->where('deleted_at', null);
                $expenditureItem = DB::table('expenditures')
                    ->select('total_price', 'updated_at',  DB::raw("'expenditure' as type"))->whereDate('updated_at', $date)->where('deleted_at', null);
            }
            if($request->show_by == 'Bulan' && $request->date){
                $month = Carbon::parse($request->date)->format('m');
                $year = Carbon::parse($request->date)->format('Y');
                $transactionTotal = Transaction::where('status', '3')->whereMonth('updated_at', $month)->whereYear('updated_at', $year)->sum('total_price');
                $transactionCount = Transaction::where('status', '3')->whereMonth('updated_at', $month)->whereYear('updated_at', $year)->count();
                $expenditureTotal = Expenditure::whereMonth('updated_at', $month)->whereYear('updated_at', $year)->sum('total_price');
                $expenditureCount = Expenditure::whereMonth('updated_at', $month)->whereYear('updated_at', $year)->count();
                $transactionItem = DB::table('transactions')
                    ->select('total_price', 'updated_at',  DB::raw("'transaction' as type"))->where('status', '3')->whereMonth('updated_at', $month)->whereYear('updated_at', $year)->where('deleted_at', null);
                $expenditureItem = DB::table('expenditures')
                    ->select('total_price', 'updated_at',  DB::raw("'expenditure' as type"))->whereMonth('updated_at', $month)->whereYear('updated_at', $year)->where('deleted_at', null);
            }
                
            $total = $transactionTotal - $expenditureTotal;
            
            $recapitulationItem = $transactionItem->unionAll($expenditureItem)->orderByDesc('updated_at')->get();

            $transactionExpenditureTotal = $transactionTotal+$expenditureTotal == 0 ? 0 : ($transactionTotal+$expenditureTotal);

            if($transactionExpenditureTotal == 0){
                $transactionPercentage = 0;
                $expenditurePercentage = 0;
            }else{
                $transactionPercentage = round($transactionTotal / ($transactionTotal+$expenditureTotal) * 100, 1);
                $expenditurePercentage = round($expenditureTotal / ($transactionTotal+$expenditureTotal) * 100, 1);
            }

            if($total >= 0){
                $status = 'Untung';
            }else{
                $status = 'Rugi';
            }

            $data = [
                'transaction_total' => $transactionTotal,
                'transaction_percentage' => $transactionPercentage,
                'transaction_count' => $transactionCount,
                'expenditure_total' => $expenditureTotal,
                'expenditure_percentage' => $expenditurePercentage,
                'expenditure_count' => $expenditureCount,
                'total' => $total,
                'status' => $status,
                'recapitulation_items' => $recapitulationItem,
            ];

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Rekapan telah Berhasil Didapat',
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
}
