<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dotenv\Parser\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make( $request->all(),[
            'name'=>'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password'=>'required|string|min:8',
        ]);

        if ($validate->fails()){
            return response()->json($validate->errors(),422);
        }

 


        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=> Hash::make($request->password)
        ]);
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['user'=>$user,'token'=>$token]);

    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            // Révoquez uniquement le token actuel
            $request->user()->currentAccessToken()->delete();
    
            return response()->json(['message' => 'Logged out successfully'], 200);
        }
        return response()->json(['message'=>'erreur'],401);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email'=>'required',
            'password'=>'required'
        ]);
        if ($validate->fails()){
            return response()->json($validate->errors(),422);
        }
        $user = User::where('email',$request->email)->first();
        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => ['mail ou mots de passe incorrect']
            ],401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['user'=>$user,'token'=>$token],200);


    }

    public function delete(Request $request)
    {
        $user = $request->user();
        if ($user){
            $user->delete();
            return response()->json(['message'=>'user deleted'],200);
        }
        return response()->json(['message'=>'user not found'],404);
    }
}
