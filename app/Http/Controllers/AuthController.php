<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\ResetPasswordRequest;

class AuthController extends Controller {
    
    public function register(UserRegisterRequest $request) {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->respondWithToken($user, 'User registered successfully', 201);
    }

    public function login(UserLoginRequest $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login credentials',
            ], 401);
        }

        return $this->respondWithToken(Auth::user(), 'Login successful');
    }

    public function logout(Request $request) {

        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out',
        ], 200);
    }

    public function sendResetLinkEmail(Request $request) {

        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'site_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'email' => $validator->errors()], 400);
        }

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'status' => __($status)], 200)
            : response()->json(['success' => false, 'email' => __($status)], 400);
    }   
    

    public function resetPassword(ResetPasswordRequest $request) {        

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user) use ($request) {
                $user->password = Hash::make($request->password);
                $user->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'message' => 'Your password has been reset successfully.',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'There was an error resetting your password. Please try again.',
        ], 500);
    }

    protected function respondWithToken($user, $message = '', $status = 200) {
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => $message,
            'token' => $token,
            'user' => new UserResource($user),
        ], $status);
    }
}

