<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\AdminException;

class CheckAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        $user_id = Auth::id();

        if (!$user_id) {
            throw new AdminException("Unauthorized", 401);
        }

        $group_id = $request->route('group_id');

        $user_group = UserGroup::query()
            ->where('group_id', $group_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$user_group || !$user_group->is_admin) {
            throw new AdminException();
        }

        return $next($request);
    }
}
