<?php

namespace App\services;

use App\Models\File;
use App\Models\Group;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;

class UserGroupService
{
    public function showMyGroups(): array
    {
        $userId = Auth::id();

        $userGroups = UserGroup::query()
            ->where('user_id', $userId)
            ->get();

        // better way
        $groups = $userGroups->map(function ($userGroup) {
            return Group::query()->where('id', $userGroup->group_id)->first();
        });

        // other way
        /*$groups = [];
        $i=0;
        foreach ($myGroups as $group){
            $groups[$i] = Group::query()
                ->where('id', $group->group_id)
                ->first();
            $i++;
        }*/

        if ($groups->isNotEmpty()) {
            return [
                'data' => $groups,
                'message' => 'All the groups you are in',
                'code' => 200
            ];
        } else {
            return [
                'data' => null,
                'message' => 'You are not in any group',
                'code' => 404
            ];
        }
    }


    public function showOneGroup($group_id):array
    {
        $id = Auth::id();
        $Group = UserGroup::query()
            ->where('user_id', $id)
            ->where('group_id', $group_id)
            ->first();

        $files = File::query()
            ->where('group_id', $group_id)
            ->where('approved',1)
            ->get();

        $data = [
            'group information' => $Group,
            'files in group' => $files
        ];

        if($Group){
            return [
                'data' => $data,
                'message' => 'group information',
                'code' => 200
            ];
        }else{
            return [
                'data' => null,
                'message' => 'group not found!',
                'code' => 404
            ];
        }

    }

    public function showMyFiles($group_id)
    {
        $user_id = Auth::id();

        $files = File::query()
            ->join('archives', 'files.id', '=', 'archives.file_id')
            ->join('editors', 'archives.id', '=', 'editors.archive_id')
            ->where('files.group_id', $group_id)
            ->where('files.status', 'reserved')
            ->where('archives.operation', 'checkIn')
            ->where('editors.user_id', $user_id)
            ->select('files.*')
            ->get();

        if ($files->isEmpty()) {
            return [
                'data' => null,
                "message" => "No approved files found.",
                "code" => 200
            ];
        } else {
            return [
                'data' => $files,
                "message" => "All the approved files!",
                "code" => 200
            ];
        }
    }


}
