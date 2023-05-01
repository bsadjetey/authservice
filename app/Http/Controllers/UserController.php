<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request){

    }
    public function register(Request $request)
    {

        try {
            $rules = [
                'name' => 'required',
                'email' => 'required|unique:users',
                'phone_number' => 'required|unique:users',
                'password' => 'required|min:6'
            ];
            $validate = $this->validateRequests($request, $rules);
            if (!$validate['passed']) {
                return $this->respondWithStatus(0, $validate['message'], []);
            }
            $user = User::firstOrCreate([
                "name" => $request->name,
                "username" => $request->email,
                "phone_number" => $request->phone_number,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "active_flag" => ($request->has("active_flag")) ? 1 : 0,
                "created_by_id" => $this->userID(),
                "updated_by_id" => $this->userID(),
            ]);
            if ($user) {
                return $this->respondWithStatus(1, "User Account created successfully", $user->fresh());
            } else {
                return $this->respondWithStatus(0, "Sorry account couldn't be created.", []);
            }
        } catch (\Exception $e) {
            return $this->respondWithStatus(-99,"Sorry some error occured ".$e->getMessage(),[]);
        }
    }

    public function login(Request $request)
    {
//        dd($request->all());
        $credentials = $request->only('username', 'password');
//        dd($credentials);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->respondWithStatus(0, "Unauthorized Account", [], 401);
                //                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $user = Auth::user();
            $roles = $user->getRoleNames();
            $permissions = $user->getAllPermissions()->pluck('name');
            $results = array("user"=>$user,"roles"=>$roles,"permissions"=>$permissions);

            return $this->respondWithToken($token,$results);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function me(Request $request)
    {
        return response()->json(Auth::user());
    }
}
