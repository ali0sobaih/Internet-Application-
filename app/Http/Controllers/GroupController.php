<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequests\AcceptJoinRequestRequest;
use App\Http\Requests\GroupRequests\CreateGroupRequest;
use App\Http\Requests\GroupRequests\DeleteGroupRequest;
use App\Http\Requests\GroupRequests\InviteUserRequest;
use App\Http\Responses\Response;
use App\services\GroupServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class GroupController extends Controller
{
    private GroupServices $groupServices;

    public function __construct(GroupServices $groupServices)
    {
        $this-> groupServices = $groupServices;
    }

    public function createGroup(CreateGroupRequest $request): JsonResponse
    {
        $data = [];
        try{
            $data = $this->groupServices->createGroup($request);
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message );
        }
    }

    public function inviteToGroup(InviteUserRequest $request): JsonResponse
    {
        $data = [];
        try{
            $data = $this->groupServices->inviteToGroup($request);
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message );
        }
    }

    public function showInvitations(): JsonResponse
    {
        $data = [];
        try{
            $data = $this->groupServices->showInvitations();
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message );
        }
    }

    public function acceptInvitation($id): JsonResponse
    {
        $data = [];
        try{
            $data = $this->groupServices->acceptInvitation($id);
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message );
        }
    }


    public function acceptJoinRequest(AcceptJoinRequestRequest $request): JsonResponse
    {
        $data = [];
        try {
        $data = $this->groupServices->acceptJoinRequest($request);
        return Response::Success($data['data'], $data['message'], $data['code']);
    } catch (Throwable $th) {
         $message = $th->getMessage();
         return Response::Error($data, $message);
        }
    }

    public function deleteGroup(DeleteGroupRequest $request): JsonResponse
    {
    $data = [];
    try {

        $data = $this->groupServices->deleteGroup($request);
        return Response::Success($data['data'], $data['message'], $data['code']);

    } catch (Throwable $th) {
        $message = $th->getMessage();
        return Response::Error($data, $message);
    }
    }


}
