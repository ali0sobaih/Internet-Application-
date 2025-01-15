<?php

namespace App\services;

use App\Http\Requests\FileRequests\checkInRequest;
use App\Models\Archive;
use App\Models\Editor;
use App\Models\Group;
use App\Models\UserGroup;
use App\Models\UsersUser;
use Faker\Core\Version;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\File;
use PharIo\Manifest\Author;
use Throwable;
use ZipArchive;

class FileServices
{
    public function addFile($group_id,$request):array
    {
        $randomString = Str::random(10);
        $fileName = $request['name'] . 'V' . 1 . '.' . $request['file']->getClientOriginalExtension();
        Storage::putFileAs('public', $request['file'], $fileName);


        $user_id = Auth::id();
        $user_users = UsersUser::query()->where('user_id',$user_id)->first();
        $author = $user_users['user_name'];

        DB::beginTransaction();

        try{
            $newFile = File::query()->create([
                'name' => $request['name'],
                'group_id' => $group_id,
                'path' => $randomString,
                'status' => 'available',
                'author' => $author
            ]);

            $archive = Archive::query()->create([
                'file_id' => $newFile->id,
                'version' => 1,
                'date' => now(),
                'operation' => 'upload'
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

        if ($file->approved != '0') {
            return [
                'data' => null,
                'message' => "The file has already been processed (approved or rejected).",
                'code' => 200
            ];
        }

        $approved = File::query()->where('id', $id)->update(['approved' => '1']);

        return [
            'data' => $approved,
            'message' => "The file is approved successfully!",
            'code' => 200
        ];
    }



    public function rejectFile($group_id, $id): array
    {
        $file = File::query()->where('id', $id)->first();

        if (!$file) {
            return [
                'data' => null,
                'message' => "File not found!",
                'code' => 404
            ];
        }

        if ($file->approved != 0) {
            return [
                'data' => null,
                'message' => "The file has already been processed (approved or rejected).",
                'code' => 200
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

        if ($files -> isEmpty()) {
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

    public function checkIn(array $files): array
    {
        $fileIds = array_map(fn($file) => $file['file_id'], $files);
        $fileVersions = array_map(fn($file) => $file['version'], $files);

        try {
            DB::beginTransaction();

            $availableFiles = File::whereIn('id', $fileIds)
                ->where('status', 'available')
                ->where('approved', 1)
                ->lockForUpdate()
                ->get();
            echo $availableFiles;

            if ($availableFiles->count() !== count($fileIds)) {
                throw new \Exception('One or more files are not available.');
            }

            $paths = [];
            foreach ($availableFiles as $file) {
                $fileVersion = $files[array_search($file->id, $fileIds)]['version'];
                $paths[] = $file->name . 'V' . $fileVersion . '.txt';
            }

            foreach ($fileIds as $fileId) {
                File::where('id', $fileId)->update(['status' => 'reserved']);

                $archive = Archive::query()->create([
                    'file_id' => $fileId,
                    'version' => $fileVersion = $files[array_search($file->id, $fileIds)]['version'],
                    'date' => now(),
                    'operation' => 'checkIn'
                ]);

                Editor::query()->create([
                    'archive_id' => $archive['id'],
                    'user_id' => Auth::id()
                ]);

            }


            $zipFilePath = $this->createZip($paths);

            DB::commit();

            return [
                'data' => [
                    'file_name' => basename($zipFilePath),
                    'file_path' => $zipFilePath,
                ],
                'message' => 'All files are available and reserved. Download initiated.',
                'code' => 200,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



    private function createZip(array $paths): string
    {
        // Temporary file path for the ZIP
        $zipFileName = storage_path('app/temp_files/reserved_files_' . time() . '.zip');

        // Initialize ZipArchive
        $zip = new ZipArchive;
        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($paths as $path) {
                // Construct the full path from storage/app/public
                $filePath = storage_path('app/public/' . $path);

                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($filePath));
                } else {
                    throw new \Exception("File not found: " . $filePath);
                }
            }
            $zip->close();
        } else {
            throw new \Exception('Failed to create ZIP file.');
        }

        return $zipFileName;
    }


    /*private function createZip($files): string
    {
        // Temporary file path for the ZIP
        $zipFileName = storage_path('app/temp_files/reserved_files_' . time() . '.zip');

        // Initialize ZipArchive
        $zip = new ZipArchive;
        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                // Assuming the `path` column stores the file's location
                $filePath = storage_path('app/' . $file->path); // Update path logic as per your app's file storage
                if (file_exists($filePath)) {
                    // Add file to the ZIP archive
                    $zip->addFile($filePath, basename($filePath));
                }
            }
            $zip->close();
        } else {
            throw new \Exception('Failed to create ZIP file.');
        }

        return $zipFileName;
    }*/


    /*public function checkIn(array $files): array
    {

        $fileIds = array_map(fn($file) => $file['file_id'], $files);

        // Query the database to find files with status = "available"
        $availableFiles = File::whereIn('id', $fileIds)
            ->where('status', 'available')
            ->get();

        // Check if all files are available
        if ($availableFiles->count() !== count($fileIds)) {
            throw new \Exception('Not all files are available.');
        }

        foreach ($fileIds as $fileId){
            $file = File::query()
                ->where('id',$fileId)
                ->update([
                    'status' => 'reserved'
                ]);
        }

        // Return the available files as an array
        return [
            'data' => null,
            'message' => 'All files are available.',
            'code' => 200,
        ];
    }*/


    public function checkOut($request): array
    {
        try {
            DB::beginTransaction();

            $file_id = $request->file_id;

            $file = File::where('id', $file_id)->first();

            if (!$file || $file->status !== 'reserved') {
                return [
                    'data' => null,
                    'message' => 'File cannot be checked out. It is not in reserved status.',
                    'code' => 400,
                ];
            }

            $uploadedFile = $request->file;
            $uploadedFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $uploadedFileExtension = $uploadedFile->getClientOriginalExtension();
            $expectedFileName = pathinfo($file->name, PATHINFO_FILENAME);

            if ($uploadedFileName !== $expectedFileName || strtolower($uploadedFileExtension) !== 'txt') {
                return [
                    'data' => null,
                    'message' => 'Uploaded file name or extension is invalid. the name and extension should be the same as when you checked in on this file!',
                    'code' => 400,
                ];
            }

            File::where('id', $file_id)->update(['status' => 'available']);

            $lastVersion = Archive::query()
                ->where('file_id', $file_id)
                ->orderBy('version', 'desc')
                ->first();

            $newVersion = $lastVersion ? $lastVersion->version + 1 : 1;

            $archive = Archive::query()->create([
                'file_id' => $file_id,
                'version' => $newVersion,
                'date' => now(),
                'operation' => 'checkOut',
            ]);

            Editor::query()->create([
                'archive_id' => $archive->id,
                'user_id' => Auth::id(),
            ]);

            $newFileName = pathinfo($file->name, PATHINFO_FILENAME) . 'V' . $newVersion . '.' . $uploadedFileExtension;

            Storage::putFileAs('public', $uploadedFile, $newFileName);

            DB::commit();

            return [
                'data' => null,
                'message' => 'File checked out successfully.',
                'code' => 200,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



    public function viewVersions($request): array
    {
        $fileId = $request->route("fileId");

        $versions = Archive::query()
            ->where('file_id', $fileId)
            ->orderBy('version', 'desc')
            ->distinct()
            ->pluck('version'); // Fetch only the 'version' column

        if ($versions->isNotEmpty()) {
            return [
                'data' => $versions,
                'message' => 'List of versions for the file',
                'code' => 200
            ];
        } else {
            return [
                'data' => null,
                'message' => 'No versions found!',
                'code' => 404
            ];
        }
    }




}
