<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {

    

    /**
     * @OA\SecurityScheme(
     *     securityScheme="sanctum",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Use Sanctum-generated Bearer Token for authentication"
     * )
     */

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="User's name"),
     *             @OA\Property(property="email", type="string", description="User's email"),
     *             @OA\Property(property="password", type="string", description="User's password"),
     *             @OA\Property(property="password_confirmation", type="string", description="Password confirmation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(UserRegisterRequest $request) {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->respondWithToken($user, 'User registered successfully', 201);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login a user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", description="User's email"),
     *             @OA\Property(property="password", type="string", description="User's password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid login credentials"
     *     )
     * )
     */
    public function login(UserLoginRequest $request) {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid login credentials',
            ], 401);
        }

        return $this->respondWithToken(Auth::user(), 'Login successful');
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout a user",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Successfully logged out',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/password/email",
     *     summary="Send password reset link",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", description="User's email address"),
     *             @OA\Property(property="site_url", type="string", description="URL of the site requesting the reset")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or email does not exist"
     *     )
     * )
     */
    public function sendResetLinkEmail(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'site_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
        ? response()->json(['success' => true, 'status' => __($status)], 200)
        : response()->json(['success' => false, 'error' => __($status)], 400);
    }

    /**
     * @OA\Post(
     *     path="/password/reset",
     *     summary="Reset password",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", description="User's email address"),
     *             @OA\Property(property="password", type="string", description="New password"),
     *             @OA\Property(property="token", type="string", description="Password reset token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password has been reset successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error resetting the password"
     *     )
     * )
     */
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
                'status'  => true,
                'message' => 'Your password has been reset successfully.',
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => 'There was an error resetting your password. Please try again.',
        ], 500);
    }

    protected function respondWithToken($user, $message = '', $status = 200) {
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => $message,
            'token'   => $token,
            'user'    => new UserResource($user),
        ], $status);
    }

}
