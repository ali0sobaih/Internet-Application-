<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\services\UserGroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class UserGroupController extends Controller
{
    private UserGroupService $userGroupService;

    public function __construct(UserGroupService $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    public function showMyGroups(): JsonResponse
    {
        $data = [];
        try{
            $data = $this->userGroupService->showMyGroups();
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message );
        }
    }

    public function showOneGroup($groupId): JsonResponse
    {
        $data = [];
        try{
            $data = $this->userGroupService->showOneGroup($groupId);
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message );
        }
    }
}
