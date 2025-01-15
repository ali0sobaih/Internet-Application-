<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Exceptions\AdminException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function usersuser()
    {
        return $this->hasOne(UsersUser::class);
    }

    public function adminsuser()
    {
        return $this->hasOne(AdminsUser::class);
    }

    public function editor()
    {
        return $this->hasMany(Editor::class);
    }


    public function isAdmin():bool
    {
        return $this->hasRole('admin');
    }

    public function isUser($group):bool
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        if (Auth::check() &&  $user->hasRole('user')){
            return true;
        }else{
            return false;
        }
    }

    public function isAdminGroup($group):bool
    {
        $user_id = Auth::id();

        $user_group = UserGroup::query()
            ->where('group_id', $group)
            ->where('user_id', $user_id)
            ->first();

        if ($user_group && $user_group->is_admin) {
            return true;
        }else{
            return false;
        }

    }

    public function isUserGroup($group):bool
    {
        $user_id = Auth::id();

        $user_group = UserGroup::query()
            ->where('group_id', $group)
            ->where('user_id', $user_id)
            ->first();

        if ($user_group) {
            return true;
        }else{
            return false;
        }

    }

}
