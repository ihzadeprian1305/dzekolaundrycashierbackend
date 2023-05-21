<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDatum;
use App\Models\UserLevel;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeDataController extends Controller
{
    public function fetch(Request $request)
    {
        try {
            if ($request->except([ 'search'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $employeeUser = User::whereRelation('user_levels', 'name', 'Karyawan');
            
            if($request->search){
                $employeeUser->whereRelation('user_data', 'name', 'like', '%'.$request->search.'%');
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Karyawan telah Berhasil Didapat',
                'data' => $employeeUser->orderBy(UserDatum::select('name')
                ->whereColumn('user_data.id', 'users.user_datum_id'))->get(),
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
            if ($request->except(['name', 'phone_number', 'address', 'profile_image', 'email', 'username', 'password', 'password_confirmation',])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }
    
            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255',
                // 'phone_number' => ['required','string','min:10','max:16',Rule::unique('user_data', 'phone_number')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                'phone_number' => 'required|string|min:10|max:16',
                'address' => 'required|string|min:4|max:512',
                'profile_image' => 'image|file|max:2048',
                'email' => ['required','max:255','email:dns',Rule::unique('users', 'email')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                'username' => ['required','min:8','max:255',Rule::unique('users', 'username')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:8',
            ]);
    
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }
    
            if($request->file('profile_image')){
                $profileImage = $request->file('profile_image')->store('profile_images');
                $userDatum = UserDatum::create([
                        'name' => $request->name,
                        'phone_number' => $request->phone_number,
                        'address' => $request->address,
                        'profile_image' => $profileImage,
                    ]);
                }else{
                $userDatum = UserDatum::create([
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                ]);
                    
                }
    
            $userDatumLastID = $userDatum->id;
            $userLevelOwner = UserLevel::where('name', 'Karyawan')->first();
            
            User::create([
                'email' => $request->email,
                'username' => $request->username,
                'password' => bcrypt($request->new_password),
                'user_level_id' => $userLevelOwner->id,
                'user_datum_id' => $userDatumLastID,
            ]);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Karyawan telah Berhasil Ditambah',
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
            if ($request->except(['_method', 'id', 'name', 'phone_number', 'address', 'profile_image', 'email', 'username', 'password', 'password_confirmation', 'delete_image'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $userDatumID = User::where('id', $request->id)->first();

            if($request->file('profile_image') && $request->delete_image == 'false'){
                $validatedData = Validator::make($request->all(), [
                    'name' => 'required|string|min:2|max:255',
                    // 'phone_number' => ['required','string','min:10','max:16',Rule::unique('user_data', 'phone_number')->ignore($userDatumID->user_datum_id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                    'phone_number' => 'required|string|min:10|max:16',
                    'address' => 'required|string|min:4|max:512',
                    'profile_image' => 'nullable|sometimes|image|file|max:2048',
                    'email' => ['required','max:255','email:dns',Rule::unique('users', 'email')->ignore($request->id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                    'username' => ['required','min:8','max:255',Rule::unique('users', 'username')->ignore($request->id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                    'password' => 'nullable|min:8|required_with:password_confirmation|same:password_confirmation',
                    'password_confirmation' => 'nullable|min:8',
                ]);
        
                if ($validatedData->fails()) {
                    return response()->json([
                        'status' => 401,
                        'success' => false,
                        'message' => $validatedData->errors()->first(),
                    ], 401);
                }

                $userDatum = User::where('id', $request->id)->first();
        
                if($userDatum->user_data->profile_image){
                    Storage::delete($userDatum->user_data->profile_image);
                }
                
                $profileImage = $request->file('profile_image')->store('profile_images');
                UserDatum::where('id', $userDatum->user_datum_id)->update([
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                    'profile_image' => $profileImage,
                ]);
            }else if(empty($request->file('profile_image')) && $request->delete_image == 'true'){
                $validatedData = Validator::make($request->all(), [
                    'name' => 'required|string|min:2|max:255',
                    // 'phone_number' => ['required','string','min:10','max:16',Rule::unique('user_data', 'phone_number')->ignore($userDatumID->user_datum_id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                    'phone_number' => 'required|string|min:10|max:16',
                    'address' => 'required|string|min:4|max:512',
                ]);
        
                if ($validatedData->fails()) {
                    return response()->json([
                        'status' => 401,
                        'success' => false,
                        'message' => $validatedData->errors()->first(),
                    ], 401);
                }
        
                $userDatum = User::where('id', $request->id)->first();

                if($userDatum->user_data->profile_image){
                    Storage::delete($userDatum->user_data->profile_image);
                }

                UserDatum::where('id', $userDatum->user_datum_id)->update([
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                    'profile_image' => null,
                ]);
            }else{
                $validatedData = Validator::make($request->all(), [
                    'name' => 'required|string|min:2|max:255',
                    // 'phone_number' => ['required','string','min:10','max:16',Rule::unique('user_data', 'phone_number')->ignore($userDatumID->user_datum_id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                    'phone_number' => 'required|string|min:10|max:16',
                    'address' => 'required|string|min:4|max:512',
                    'email' => ['required','max:255','email:dns',Rule::unique('users', 'email')->ignore($request->id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                    'username' => ['required','min:8','max:255',Rule::unique('users', 'username')->ignore($request->id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                    'password' => 'nullable|min:8|required_with:password_confirmation|same:password_confirmation',
                    'password_confirmation' => 'nullable|min:8',
                ]);
        
                if ($validatedData->fails()) {
                    return response()->json([
                        'status' => 401,
                        'success' => false,
                        'message' => $validatedData->errors()->first(),
                    ], 401);
                }

                $userDatum = User::where('id', $request->id)->first();

                UserDatum::where('id', $userDatum->user_datum_id)->update([
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                ]);
            }

            if(($request->password && $request->password_confirmation) != null){
                $user = [
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => bcrypt($request->password),
                ];
            }else{
                $user = [
                    'email' => $request->email,
                    'username' => $request->username,
                ];
            }

            User::where('id', $request->id)->update($user);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Karyawan telah Berhasil Diubah',
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
            if ($request->except(['id'])) {
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

            $userDatum = User::where('id', $request->id)->first();
            UserDatum::where('id', $userDatum->user_datum_id)->delete();
            User::where('id', $request->id)->delete();
            
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data Karyawan telah Berhasil Dihapus',
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
