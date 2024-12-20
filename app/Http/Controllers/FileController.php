<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequests\AddFileRequest;
use App\Http\Requests\FileRequests\checkInRequest;
use App\Http\Responses\Response;
use App\services\FileServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class FileController extends Controller
{
    private FileServices $fileServices;

    public function __construct(FileServices $fileServices)
    {
        $this-> fileServices = $fileServices;
    }

    public function addFile($group_id,AddFileRequest $request):JsonResponse
    {
        $data = [];
        try{
            $data = $this->fileServices->addFile($group_id,$request);
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function approveFile($group_id,$id):JsonResponse
    {
        $data = [];
        try{
            $data = $this->fileServices->approveFile($group_id,$id);
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function showApprovedFiles($group_id):JsonResponse
    {
        $data = [];
        try{
            $data = $this->fileServices->showApprovedFiles($group_id);
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function showPendingFiles($id):JsonResponse
    {
        $data = [];
        try{
            $data = $this->fileServices->showPendingFiles($id);
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function rejectFile($group_id,$id):JsonResponse
    {
        $data = [];
        try{
            $data = $this->fileServices->rejectFile($group_id,$id);
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function checkIn(checkInRequest $request):JsonResponse
    {
        $data = [];
        try{
            $data = $this->fileServices->checkIn($request);
            return Response::Success($data['data'],$data['message'],$data['code']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }
}
