<?php


namespace App\services;

use App\Models\Group;
use App\Models\Invitation;
use App\Models\UserGroup;
use App\Models\UsersUser;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

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

    public function inviteToGroup($group_id,$request):array
    {
        $invitor = Auth::id();
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

        $invitation = Invitation::query()->create([
            'user_name' => $request['user_name'],
            'group_id' => $group_id,
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


    public function deleteGroup($group_id): array
    {
        $group = Group::query()->findOrFail($group_id);

        // Admin check is handled by middleware
        $group->delete();

        return [
            'data' => null,
            'message' => "The group was deleted successfully.",
            'code' => 200
        ];
    }

    public function acceptInvitation($group_id):array
    {
        $user_id = Auth::id();
        $user_users = UsersUser::query()->where('user_id',$user_id)->first();
        $user_name = $user_users['user_name'];

        DB::beginTransaction();

        try{
            $user_group = UserGroup::query()->create([
                'group_id' => $group_id,
                'user_id' => $user_id,
                'is_admin' => false
            ]);

            $invitation = Invitation::query()
                ->where('user_name', $user_name)
                ->where('group_id', $group_id)
                ->first();

            if ($invitation) {
                $invitation->update([
                    'status' => 'accepted'
                ]);
            } else {
                DB::rollBack();
                return ['data' => null, 'message' => 'you are not invited','code' => 403];
            }

            $message = "Invitation accepted, you are a member of the group now";

            $data = [
                'invitation data' => $invitation,
                'group data' => $user_group
            ];
            DB::commit();

            return[
                'data' => $data,
                'message' => $message,
                'code' => 200
            ];
        }catch (Throwable $e) {
            DB::rollBack();
            return ['data' => null, 'message' => 'Error occurred: ' . $e->getMessage(), 'code' => 500];
        }
    }

    public function rejectInvitation($group_id){
        $user_id = Auth::id();
        $user_users = UsersUser::query()->where('user_id',$user_id)->first();
        $user_name = $user_users['user_name'];

        DB::beginTransaction();

        try{
            $invitation = Invitation::query()->where('user_name',$user_name)
                ->where('group_id',$group_id)
                ->update([
                    'status' => 'rejected'
                ]);


            $message = "Invitation rejected successfully";

            $data = [
                'invitation data' => $invitation
            ];
            DB::commit();

            return[
                'data' => $data,
                'message' => $message,
                'code' => 200
            ];
        }catch (Throwable $e) {
            DB::rollBack();
            return ['data' => null, 'message' => 'Error occurred: ' . $e->getMessage(), 'code' => 500];
        }
    }




}
