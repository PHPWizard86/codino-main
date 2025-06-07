<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User; // Assuming User model is in App\Models
use Tymon\JWTAuth\Facades\JWTAuth; // Use the facade
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiAuthController extends Controller
{
    public function __construct()
    {
        // Apply jwt.auth middleware to all methods in this controller
        // except for login and register.
        // Note: Laravel 8+ uses 'auth:api' for Sanctum by default,
        // for JWT, the middleware might be just 'jwt.auth' or similar
        // depending on how tymon/jwt-auth configures it.
        // The default guard for 'api' was set to 'jwt' in config/auth.php.
        \$this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * User Registration.
     */
    public function register(Request \$request)
    {
        \$validator = Validator::make(\$request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|unique:users,phone', // Added phone
        ]);

        if (\$validator->fails()) {
            return response()->json(['errors' => \$validator->errors()], 422);
        }

        // Fetch Free plan ID (assuming Plan model and seeder exist)
        // \$freePlan = \App\Models\Plan::where('name', 'Free')->first(); // Need to create Plan model

        \$user = User::create([
            'username' => \$request->username,
            'name' => \$request->name,
            'email' => \$request->email,
            'password' => Hash::make(\$request->password),
            'phone' => \$request->phone,
            // 'plan_id' => \$freePlan ? \$freePlan->id : null, // Add after Plan model is set up
        ]);

        // Note: Email verification event could be dispatched here if desired.
        // event(new \Illuminate\Auth\Events\Registered(\$user));


        // Log the user in and generate token (optional after registration, or require login)
        try {
            if (!\$token = JWTAuth::fromUser(\$user)) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
        } catch (JWTException \$e) {
            return response()->json(['error' => 'could_not_create_token', 'details' => \$e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'User successfully registered',
            'user' => \$user,
            'access_token' => \$token,
            'token_type' => 'bearer',
            // 'expires_in' => auth('api')->factory()->getTTL() * 60 // If using auth('api') helper
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ], 201);
    }

    /**
     * User Login.
     */
    public function login(Request \$request)
    {
        \$validator = Validator::make(\$request->all(), [
            'email' => 'required|email', // Or 'username' => 'required|string'
            'password' => 'required|string',
        ]);

        if (\$validator->fails()) {
            return response()->json(['errors' => \$validator->errors()], 422);
        }

        \$credentials = \$request->only('email', 'password'); // Or 'username', 'password'

        try {
            if (!\$token = JWTAuth::attempt(\$credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException \$e) {
            return response()->json(['error' => 'Could not create token', 'details' => \$e->getMessage()], 500);
        }

        return \$this->respondWithToken(\$token);
    }

    /**
     * Get the authenticated User.
     */
    public function me()
    {
        try {
            \$user = JWTAuth::parseToken()->authenticate();
            if (!\$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException \$e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException \$e) {
            return response()->json(['error' => 'Token invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException \$e) {
            return response()->json(['error' => 'Token absent or could not be parsed'], 401);
        }

        return response()->json(\$user);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException \$e) {
            // Something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'Failed to logout, please try again.', 'details' => \$e->getMessage()], 500);
        }
    }

    /**
     * Refresh a token.
     */
    public function refresh()
    {
        try {
            return \$this->respondWithToken(JWTAuth::refresh(JWTAuth::getToken()));
        } catch (JWTException \$e) {
            return response()->json(['error' => 'Refresh token failed', 'details' => \$e->getMessage()], 500);
        }
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken(\$token)
    {
        return response()->json([
            'access_token' => \$token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60, // Default is 1 hour
            'user' => JWTAuth::setToken(\$token)->authenticate() // Get user details along with token
        ]);
    }
}
