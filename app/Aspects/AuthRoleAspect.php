<?php

namespace App\Aspects;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthRoleAspect extends Aspect
{

    public function before(Request $request, array $parameters = [])

    {

        $role = $parameters[0] ?? null;
        $group = $request->route("group_id");
        if (!$role) {
            return response()->json([
                'message' => 'Access Denied: Role is required',
            ], 403);
        }

        if ($role === 'admin' && !$request->user()->isAdmin()) {
            abort(403, 'Unauthorized. user must be super-admin to access this endpoint');
        }

        if ($role === 'user' && !$request->user()->isUser($group)) {
            abort(403, 'Unauthorized. Only group members can access this resource.');
    }

        if ($role === 'adminGroup' && !$request->user()->isAdminGroup($group)) {
            abort(403, 'Unauthorized. Only group admin can access this resource.');
        }

        if ($role === 'userGroup' && !$request->user()->isUserGroup($group)) {
            abort(403, 'Unauthorized. Only group user can access this resource.');
        }
    }



}
