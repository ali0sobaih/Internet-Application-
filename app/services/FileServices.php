<?php

namespace App\services;

use App\Http\Requests\FileRequests\checkInRequest;
use App\Models\Archive;
use App\Models\Group;
use App\Models\UserGroup;
use App\Models\UsersUser;
use Faker\Core\Version;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\File;
use Throwable;

class FileServices
{
    public function addFile($group_id,$request):array
    {
        $randomString = Str::random(10);
        $fileName = $randomString . 'V' . 1 . '.' . $request['file']->getClientOriginalExtension();
        Storage::putFileAs('public', $request['file'], $fileName);
        $path = 'uploads/' . $fileName;

        $user_id = Auth::id();
        $user_users = UsersUser::query()->where('user_id',$user_id)->first();
        $author = $user_users['user_name'];

        DB::beginTransaction();

        try{
            $newFile = File::query()->create([
                'name' => pathinfo( $request['file']->getClientOriginalName(), PATHINFO_FILENAME),
                'group_id' => $group_id,
                'path' => $path,
                'status' => 'available',
                'author' => $author
            ]);

            $archive = Archive::query()->create([
                'file_id' => $newFile->id,
                'version' => 1,
                'date' => now()
            ]);

            $user_group = UserGroup::query()->where('group_id',$group_id)
                ->where('user_id', $user_id)
                ->first();

            // because  if the user is an admin it is already approved
            if($user_group['is_admin']){
                $file_approval = File::query()->where('id',$newFile->id)
                ->update([
                    'approved' => 1
                ]);
            }

            DB::commit();

            return [
                'data' => [
                    'file:' => $newFile,
                    'archive:' => $archive
                ],
                'message' => 'File uploaded successfully',
                'code' => 200,
            ];
        }catch(Throwable $e){
            DB::rollBack();
            return ['data' => null, 'message' => 'Error occurred: ' . $e->getMessage(), 'code' => 500];
        }
    }

    public function approveFile($group_id, $id): array
    {
        $file = File::query()->where('id', $id)->first();
        if (!$file) {
            return [
                'data' => null,
                'message' => "File not found!",
                'code' => 404
            ];
        }

        $approved = File::query()->where('id', $id)->update(['approved' => '1']);

        return [
            'data' => $approved,
            'message' => "The file is approved successfully!",
            'code' => 200
        ];
    }


    public function rejectFile($group_id,$id): array
    {
        $file = File::query()->where('id', $id)->first();
        if (!$file) {
            return [
                'data' => null,
                'message' => "File not found!",
                'code' => 404
            ];
        }

        $file->delete();

        return [
            'data' => null,
            'message' => "The file has been rejected and deleted!",
            'code' => 200
        ];
    }


    public function showPendingFiles($group_id): array
    {
        $files = File::query()->where('group_id', $group_id)
            ->where('approved', 0)
            ->get();

        if ($files->isNotEmpty()){
            return [
                'data' => $files,
                "message" => "all the unapproved files!",
                "code" => 200
            ];
        } else {
            return [
                'data' => null,
                "message" => "no unapproved files!",
                "code" => 200
            ];
        }
    }


    public function showApprovedFiles($group_id): array
    {
        $files = File::query()->where('group_id', $group_id)
            ->where('approved', 1)
            ->get();

        if ($files->isEmpty()) {
            return [
                'data' => null, // No data since no files are found.
                "message" => "No approved files found.",
                "code" => 200
            ];
        } else {
            return [
                'data' => $files, // Files exist, so include them in the response.
                "message" => "All the approved files!",
                "code" => 200
            ];
        }
    }


    public function checkIn($files):array
    {
        $file_ids = array_column($files, 'id');



        return [];
    }

    public function checkOut(){}


}
