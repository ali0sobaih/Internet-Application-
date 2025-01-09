<?php

namespace App\services;

use App\Models\Archive;
use App\Models\Group;
use App\Models\UserGroup;
use App\Models\UsersUser;

class AdminsUserService
{
    public function showAllUsers():array
    {
        $users = UsersUser::query()->get();

        if($users){
            return[
                'data' => $users,
                'message' => 'all users in system',
                'code' => 200
            ];
        }else{
            return[
                'data' => null,
                'message' => 'no users in system',
                'code' => 404
            ];
        }
    }

    public function showAllGroups():array
    {
        $users = Group::query()->get();

        if($users){
            return[
                'data' => $users,
                'message' => 'all groups in system',
                'code' => 200
            ];
        }else{
            return[
                'data' => null,
                'message' => 'no groups in system',
                'code' => 404
            ];
        }
    }

    public function showArchive():array
    {
        $users = Archive::query()->get();

        if($users){
            return[
                'data' => $users,
                'message' => 'all Archive records',
                'code' => 200
            ];
        }else{
            return[
                'data' => null,
                'message' => 'no Archive records',
                'code' => 404
            ];
        }
    }


}
