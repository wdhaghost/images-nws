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
        $request->user->tokens()->delete();
        return response()->json(['message','Déconnexion']);
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
}
