<?php

namespace App\Http\Controllers;

use App\Mappers\UserMapper;
use App\Models\User\User;
use App\Repositories\UserRepository;
use App\Support\Requests\UserStoreRequest;
use App\Support\Requests\UserUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct(private UserRepository $userRepository, private UserMapper $userMapper)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}",
     *     tags={"Users"},
     *     summary="Show user by ID",
     *     description="Show user by ID",
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          description="ID of user",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="User Details",
     *         @OA\JsonContent(ref="#/components/schemas/UserMapper"),
     *     ),
     * )
     *
     * @param User $user
     *
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return \Response::json(
            $this->userMapper->single($user),
            200,
            []
        );
    }

    /**
     * @OA\Get(
     *     path="/api/users/",
     *     tags={"Users"},
     *     summary="Show users",
     *     description="Show user",
     *     operationId="index",
     *     parameters={},
     *     @OA\Response(
     *         response=200,
     *         description="Users Details",
     *         @OA\JsonContent(ref="#/components/schemas/UserMapper"),
     *     ),
     * )
     *
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::all(); // Retrieve all users from the database
        return response()->json($users); // Return users as JSON response
    }


    /**
     * @OA\Post(
     *     path="/api/users/store",
     *     tags={"Users"},
     *     summary="Create user",
     *     description="Create a new user",
     *     operationId="store",
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/UserStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User created",
     *         @OA\JsonContent(ref="#/components/schemas/UserMapper"),
     *     ),
     *     @OA\Response(response=400, description="User cannot be created"),
     *     @OA\Response(response=422, description="Failed validation of given params"),
     * )
     *
     * @param UserStoreRequest $request
     *
     * @return JsonResponse
     * @throws Exception
     */

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'string|email|unique:users',
            'nickname' => 'string|max:30|unique:users',
            'password' => 'string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'nickname'    => $request->input('nickname'),
            'password' => Hash::make($request->input('password')),
        ]);
        
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }


    /**
     * @OA\Put(
     *     path="/api/users/{user}",
     *     tags={"Users"},
     *     summary="Update user by ID",
     *     description="Update user by ID",
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          description="ID of user",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User after the update",
     *         @OA\JsonContent(ref="#/components/schemas/UserMapper"),
     *     ),
     *     @OA\Response(response=422, description="Failed validation of given params"),
     * )
     *
     * @param UserUpdateRequest $request
     * @param User              $user
     *
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'string|email|unique:users',
            'nickname' => 'string|max:30|unique:users',
            'password' => 'string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = [
            'name'     => trim($request->input('name')),
            'email'    => trim($request->input('email')),
            'nickname'    => trim($request->input('nickname')),
            'password' => Hash::make(trim($request->input('password')) ?: null),
        ];

        $user->fill($data)->save();

        return \Response::json($this->userMapper->single($user));
    }

    /**
     * @OA\Get(path="/api/users/nickname/{nickname}",
     *     tags={"Users"},
     *     summary="Find user by nickname",
     *     description="Find user by nickname",
     *     operationId="showByNickname",
     *     @OA\Parameter(
     *         name="nickname",
     *         in="path",
     *         description="nickname of user that needs to be fetched",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserMapper"),
     *     ),
     *     @OA\Response(response=400, description="Invalid ID supplied"),
     *     @OA\Response(response=404, description="Order not found")
     * )
     */
    public function showByNickname($nickname)
    {
        $user = User::where('nickname', $nickname)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }

}
