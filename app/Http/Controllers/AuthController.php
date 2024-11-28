<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\services\UserServices;
use App\Http\Responses\Response;
use Throwable;


class AuthController extends Controller
{
    private UserServices $userService ;

    public function __construct(UserServices $userService)
    {
        $this->userService = $userService ;
    }
    
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = [];
        try{
            $data = $this->userService->register($request->validated());
            return Response::Success($data['data'],$data['message'] );

        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message );
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = [];
        try{
            $data = $this->userService->login($request);
            return Response::Success($data['user'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message );
        }
    }

    public function logout(): JsonResponse
    {
        $data = [];
        try{
            $data = $this->userService->logout();
            return Response::Success($data['user'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message );
        }
    }
}
