<?php


namespace App\services;

use App\Models\Group;
use App\Models\Invitation;
use App\Models\UserGroup;
use App\Models\UsersUser;
use Illuminate\Support\Facades\Auth;

class GroupServices{

    public function createGroup($request): array
    {
        if (Auth::check()) {
            $group = Group::query()->create([
                'name' => $request['name'],
                'creating_date' => now()
            ]);

            $user_id = Auth::id();

            $groupAdmin = UserGroup::query()->create([
                'group_id' => $group->id,
                'user_id' => $user_id,
                'is_admin' => true,
            ]);

            $data = [
                'group data' => $group,
                'user group' => $groupAdmin
            ];

            $message = "the group created Successfully";

            return [
                'data' => $data,
                'message' => $message,
                'code' => 200
            ];
        } else {
            $message = "the group was NOT created Successfully... the user is not registered";

            return [
                'data' => null,
                'message' => $message,
                'code' => 404
            ];
        }
    }

    public function inviteToGroup($request):array
    {
        $invitor = Auth::id();
        $group_id = $request['group_id'];
        $userName = $request['user_name'];

        // Validate the user exists
        $invitedUser = UsersUser::query()->where('user_name', $userName)->first();
        if (!$invitedUser) {
            $message = "User '$userName' does not exist.";
            return [
                'data' => null,
                'message' => $message,
                'code' => 404
            ];
        }

        // Validate if invitor is an admin
        $user_group = UserGroup::query()->where('user_id',$invitor)
                                        ->where('id',$group_id)
                                        ->first();

        if($user_group->is_admin){
            $invitation = Invitation::query()->create([
                'user_name' => $request['user_name'],
                'group_id' => $request['group_id'],
                'date_time' => now(),
                'status' => 'pending'
            ]);

            $data = [
                'invitation' => $invitation
            ];

            $message = "invitation completed Successfully!";
            return [
                'data' => $data,
                'message' => $message,
                'code' => 200
            ];
        }else{
            $message = "invitation Failed!... user is Unauthorised ";
            return [
                'data' => null,
                'message' => $message,
                'code' => 401
            ];
        }

    }

}
