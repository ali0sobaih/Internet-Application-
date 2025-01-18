<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\services\AdminsUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AdminsUserController extends Controller
{
    private AdminsUserService $adminsUserService;

    public function __construct(AdminsUserService $adminsUserService)
    {
        $this->adminsUserService = $adminsUserService;
    }

    public function showAllUsers():JsonResponse
    {
        $data = [];
        try{
            $data = $this->adminsUserService->showAllUsers();
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch (Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function showAllGroups():JsonResponse
    {
        $data = [];
        try{
            $data = $this->adminsUserService->showAllGroups();
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch (Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function showArchive():JsonResponse
    {
        $data = [];
        try{
            $data = $this->adminsUserService->showArchive();
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch (Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function showUpdates():JsonResponse
    {
        $data = [];
        try{
            $data = $this->adminsUserService->showUpdates();
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch (Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }
}
