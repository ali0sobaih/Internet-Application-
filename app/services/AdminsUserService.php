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

    public function showArchive(): array
    {
        $archives = Archive::query()
            ->leftJoin('editors', 'archives.id', '=', 'editors.archive_id')
            ->select('archives.*', 'editors.id as editor_id', 'editors.user_id as editor_user_id', 'editors.created_at as editor_created_at')
            ->get()
            ->map(function ($archive) {
                return [
                    'id' => $archive->id,
                    'file_id' => $archive->file_id,
                    'version' => $archive->version,
                    'date' => $archive->date,
                    'operation' => $archive->operation,
                    'created_at' => $archive->created_at,
                    'updated_at' => $archive->updated_at,
                    'editor' => [
                        'id' => $archive->editor_id,
                        'user_id' => $archive->editor_user_id,
                        'created_at' => $archive->editor_created_at,
                    ],
                ];
            });

        if ($archives->isNotEmpty()) {
            $data = [];
            foreach ($archives as $archive) {
                $data[] = [ // Use $data[] to append each archive to the array
                    'Archive id' => $archive['id'],
                    'File id' => $archive['file_id'],
                    'Version' => $archive['version'],
                    'Date' => $archive['date'],
                    'Operation' => $archive['operation'],
                    'Editor' => $archive['editor']['id'] ?? null, // Safely access editor ID
                ];
            }

            return [
                'data' => $data, // Return the entire $data array
                'message' => 'All archive records with editor data',
                'code' => 200,
            ];
        } else {
            return [
                'data' => null,
                'message' => 'No archive records found',
                'code' => 404,
            ];
        }
    }




}
