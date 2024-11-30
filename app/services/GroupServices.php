<?php


namespace App\services;

use App\Http\Requests\GroupRequests\AcceptJoinRequestRequest;
use App\Http\Requests\GroupRequests\DeleteGroupRequest;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\UserGroup;
use App\Models\UsersUser;
use Exception;
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

    public function acceptJoinRequest(AcceptJoinRequestRequest $request): array
    {
        if (Auth::check()) {
            $invitorId = Auth::id();
            $groupId = $request->group_id;
            $userName = $request->user_id;

            $userGroup = UserGroup::where('group_id', $groupId)
                ->where('user_id', $invitorId)
                ->first();

            if ($userGroup) {

                $invitation = Invitation::where('group_id', $groupId)
                    ->where('user_name', $userName)
                    ->where('status', 'pending')
                    ->first();

                if ($invitation) {

                    $invitation->update(['status' => 'accepted']);

                    return [
                        'data' => null,
                        'message' => "User has been accepted into the group.",
                        'code' => 200
                    ];
                }

                return [
                    'data' => null,
                    'message' => "No pending invitation found for this user.",
                    'code' => 404
                ];
            } else {
                return [
                    'data' => null,
                    'message' => "You are not authorized to accept this request.",
                    'code' => 401
                ];
            }
        }

        return [
            'data' => null,
            'message' => "The action was NOT completed successfully... the user is not registered.",
            'code' => 404
        ];
    }


    public function showInvitations():array
    {
        $id = Auth::id();
        $user = UsersUser::query()->where('user_id',$id)
            ->get()->first();

        $invitations = Invitation::query()->where('user_name',$user["user_name"])
            ->where('status',"pending")
            ->get()->all();

        if($invitations){
            $data = [
                'invitations' => $invitations
            ];
            $message = "all invitations";
            return[
                'data' => $data,
                'message' => $message,
                'code' => 200
            ];
        }else{
            return[
                'data' => null,
                'message' => "No invitation found for this user",
                'code' => 404
            ];
        }
    }


    public function deleteGroup(DeleteGroupRequest $request): array
    {
        if (Auth::check()) {
            $groupId = $request->group_id;
            $group = Group::findOrFail($groupId);

            $userGroup = UserGroup::where('group_id', $groupId)
                ->where('user_id', Auth::id())
                ->first();

            if ($userGroup && $userGroup->is_admin) {

                $group->delete();

                return [
                    'data' => null,
                    'message' => "The group was deleted successfully.",
                    'code' => 200
                ];
            }

            return [
                'data' => null,
                'message' => "You are not authorized to delete this group.",
                'code' => 403
            ];
        }

        return [
            'data' => null,
            'message' => "The action was NOT completed successfully... the user is not registered.",
            'code' => 404
        ];
    }

}
