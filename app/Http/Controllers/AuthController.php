<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Jobs\SendEmail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Get user
        $userExists = User::where('email', $request->email)->first();
        
        // Check if user exists
        if ($userExists) {
            return response()->json(['message' => 'Email already taken'], 400);
        }

        // Create user if not exists
        $user = new User([
            'name'     => $request->email,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        try {
            DB::beginTransaction();

            // Save user
            $user->save();
            $details = ['name' => $request->name, 'email' => $request->email];

            // Send email to user
            SendEmail::dispatch($details);

            DB::commit();
            
            return response()->json(['message' => 'User successfully registered'], 201);
        } catch (\Exception $error) {
            DB::rollback();
            return response()->json(['message' => $error->getMessage()]);
        } catch (\Throwable $error) {
            DB::rollback();
            return response()->json(['message' => $error->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        // Authentication
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['access_token' => $token], 201);
    }
}
