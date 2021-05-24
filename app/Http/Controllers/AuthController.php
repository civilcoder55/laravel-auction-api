<?php

namespace App\Http\Controllers;

use App\Http\Resources\TokenCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    private function createToken($user)
    {
        return ['token' => ['token' => $user->createToken('api-auth')->plainTextToken, 'expiration' => Date::now()->addMinutes(config('sanctum.expiration'))]];
    }

    /**
     * @OA\Post(
     *      path="/register",
     *      operationId="registerUser",
     *      tags={"Users"},
     *      summary="Register new user",
     *      description="Returns New User data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(mediaType="application/x-www-form-urlencoded",@OA\Schema(ref="#/components/schemas/RegisterUserRequest")),
     *
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data"
     *      )
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ])->fresh();

        return (new UserResource($user))->additional($this->createToken($user))->response()->setStatusCode(201);

    }

    /**
     * @OA\Post(
     *      path="/login",
     *      operationId="loginUser",
     *      tags={"Users"},
     *      summary="Login exsisting user",
     *      description="Returns User data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(mediaType="application/x-www-form-urlencoded",@OA\Schema(ref="#/components/schemas/LoginUserRequest")),
     *
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Credentials Error"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid Data"
     *      )
     * )
     */

    public function login(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where(['email' => $data['email']])->first();
        if (!$user) {
            return response()->json([
                'message' => 'Credentials not match',
            ], 401);
        }
        if (!Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Credentials not match',
            ], 401);
        }

        return (new UserResource($user))->additional($this->createToken($user))->response()->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *      path="/logout",
     *      operationId="logoutUser",
     *      tags={"Users"},
     *      summary="Logout user",
     *      description="Revoke User tokens",
     *      security={{ "Bearer":{} }},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(mediaType="application/json",@OA\Schema(type="object",@OA\Property(property="message",type="string",example="Tokens Revoked"))),
     *
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid Data"
     *      )
     * )
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Tokens Revoked',
        ], 200);
    }

    /**
     * @OA\Get(
     *      path="/me",
     *      operationId="InfoUser",
     *      tags={"Users"},
     *      summary="User info",
     *      description="Returns User Data",
     *      security={{ "Bearer":{} }},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid Data"
     *      )
     * )
     */
    public function info()
    {
        $user = auth()->user();
        return (new UserResource($user))->additional(['issuedTokens' => new TokenCollection($user->tokens)]);
    }
}
