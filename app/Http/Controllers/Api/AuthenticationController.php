<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDatum;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthenticationController extends Controller
{
    public function signup(Request $request){
        try{
            if ($request->except(['name', 'username', 'email', 'phone_number', 'address', 'password', 'user_level_id', 'profile_images'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255',
                'email' => ['required','string','email:dns','max:255',Rule::unique('users', 'email')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                'username' => ['required','string','min:8','max:255',Rule::unique('users', 'username')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                'phone_number' => 'required|string|min:10|max:16',
                'address' => 'required|string|min:4|max:512',
                'password' => 'required|string|min:8|max:255',
                'user_level_id' => 'required',
                'image_profile' => 'image|file|max:2048'
            ]);
        
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            if ($request->file('profile_image')) {
                $profileImage = $request->file('profile_profile')->store('profile_images');
            }

            $userDatumProcess = UserDatum::create([
                'name' => $request->name,
                'profile_image' => $profileImage,
                'phone_number' => $request->phone_number,
            ]);

            $userDatumLastID = $userDatumProcess->id;

            User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_level_id' => $request->user_level_id,
                'user_datum_id' => $userDatumLastID,
            ]);

            $user = User::where('email',$request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'status' => 200,
                'success' => true,
                'token_type' => 'Bearer',
                'access_token' => $tokenResult,
                'user' => $user,
            ], 401);
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

    public function signin(Request $request)
    {
        try {
            if ($request->except(['emailandusername', 'password'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $validatedData = Validator::make($request->all(), [
                'emailandusername' => 'required',
                'password' => 'required',
            ]);

            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            if (filter_var($request->emailandusername, FILTER_VALIDATE_EMAIL)) {
                $credentialData = [
                    'email' => $request->emailandusername,
                    'password' => $request->password,
                ];
            } else {
                $credentialData = [
                    'username' => $request->emailandusername,
                    'password' => $request->password,
                ];
            }

            if (!Auth::attempt($credentialData)) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Gagal untuk Masuk',
                ], 401);
            }

            if (filter_var($request->emailandusername, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email', $request->emailandusername)->first();
            } else {
                $user = User::where('username', $request->emailandusername)->first();
            }

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('User Access Token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'success' => true,
                'token_type' => 'Bearer',
                'access_token' => $tokenResult,
                'data' => $user,
            ], 200);
        } catch (QueryException $error) {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => $error,
            ], 401);
        } catch (Exception $error) {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => $error,
            ], 401);
        }
    }

    public function signout(Request $request){
        try {
            if(!$request->headers){
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Biarkan Kosong',
                ], 401);
            }

            if (!Auth::check()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Anda Belum Masuk Sebelumnya',
                ], 401);
            }

            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Token telah Dihapus',
            ], 200);
        } catch (QueryException $error) {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => $error,
            ], 401);
        } catch (Exception $error) {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => $error,
            ], 401);
        }
    }

    public function updateprofile(Request $request){
        try {
            if ($request->except(['_method', 'name', 'phone_number', 'address', 'profile_image', 'delete_image'])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }

            $userDatumID = User::where('id', $request->user()->id)->first();

            if($request->file('profile_image') && $request->delete_image == 'false'){
                $validatedData = Validator::make($request->all(), [
                    'name' => 'required|string|min:2|max:255',
                    'phone_number' => ['required','string','min:10','max:16',Rule::unique('user_data', 'phone_number')->ignore($userDatumID->user_datum_id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                    'address' => 'required|string|min:4|max:512',
                    'profile_image' => 'image|file|max:2048',
                ]);
        
                if ($validatedData->fails()) {
                    return response()->json([
                        'status' => 401,
                        'success' => false,
                        'message' => $validatedData->errors()->first(),
                    ], 401);
                }
        
                $userDatum = User::where('id', $request->user()->id)->first();

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
                    'phone_number' => ['required','string','min:10','max:16',Rule::unique('user_data', 'phone_number')->ignore($userDatumID->user_datum_id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                    'address' => 'required|string|min:4|max:512',
                ]);
        
                if ($validatedData->fails()) {
                    return response()->json([
                        'status' => 401,
                        'success' => false,
                        'message' => $validatedData->errors()->first(),
                    ], 401);
                }
        
                $userDatum = User::where('id', $request->user()->id)->first();

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
                    'phone_number' => ['required','string','min:10','max:16',Rule::unique('user_data', 'phone_number')->ignore($userDatumID->user_datum_id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                    'address' => 'required|string|min:4|max:512',
                ]);
        
                if ($validatedData->fails()) {
                    return response()->json([
                        'status' => 401,
                        'success' => false,
                        'message' => $validatedData->errors()->first(),
                    ], 401);
                }
        
                $userDatum = User::where('id', $request->user()->id)->first();

                UserDatum::where('id', $userDatum->user_datum_id)->update([
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                ]);
                
            }

            $user = User::where('id', $request->user()->id)->first();

            $tokenResult = $user->createToken('User Access Token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data telah Berhasil Diubah',
                'token_type' => 'Bearer',
                'access_token' => $tokenResult,
                'data' => $user,
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

    public function updateaccount(Request $request)
    {
        try {
            if ($request->except(['_method', 'email', 'username', 'old_password', 'new_password', 'password_confirmation',])) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Mohon Masukkan Menggunakan Kolom Form yang Benar',
                ], 401);
            }
    
            $validatedData = Validator::make($request->all(), [
                'email' => ['required','max:255','email:dns',Rule::unique('users', 'email')->ignore($request->user()->id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                'username' => ['required','min:8','max:255',Rule::unique('users', 'username')->ignore($request->user()->id, 'id')->where(fn (Builder $query) => $query->where('deleted_at', null,))],
                'old_password' => 'nullable|min:8|required_with:new_password',
                'new_password' => 'nullable|min:8|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'nullable|min:8',
            ]);
    
            if ($validatedData->fails()) {
                return response()->json([
                    'status' => 401,
                    'success' => false,
                    'message' => $validatedData->errors()->first(),
                ], 401);
            }

            $userDatum = User::where('id', $request->user()->id)->first();

            if(($request->old_password && $request->new_password && $request->password_confirmation) != null){
                if(!Hash::check($request->old_password, $userDatum->password)){
                    return response()->json([
                        'status' => 401,
                        'success' => false,
                        'message' => 'Periksa Kembali Kredensial Akun Anda',
                    ], 401);
                }
                $user = [
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => bcrypt($request->new_password),
                ];
            }else{
                $user = [
                    'email' => $request->email,
                    'username' => $request->username,
                ];
            }

            User::where('id', $request->user()->id)->update($user);
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'Data telah Berhasil Diubah',
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
