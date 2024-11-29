<?php

namespace App\services;


use App\Models\User;
use App\Models\UsersUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;



class UserServices
{
    public function register($request) : array
    {
        $user = User::query()->create([
            'email' => $request['email'],
            'password' => Hash::make($request['password'])
        ]);

        $user_role = Role::query()->where('name' , 'user')->first();
        $user -> assignRole($user_role);

        $user->load('roles');

        $user = User::query()->find($user['id']);
        $user = $this->appendRolesAndPermissions($user);
        $user['token'] = $user->createToken("token")->plainTextToken;

        $users_user = UsersUser::query()->create([
            'user_id' => $user->id,
            'user_name' => $request['user_name'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
        ]);

        $message = "User created Successfully!!";

        $data = [
            'user' => $user ,
            'additional details' => $users_user,
        ];

        return [
            'data' => $data,
            'message' => $message,
        ];

    }

    public function login($request): array
    {
        $user = User::query()->where('email',$request['email'])->first();

        if(!is_null($user)){

            if(!Auth::attempt($request->only(['email', 'password']))) {
                $message = 'email and password are not in our records';
                $code = 401;
            }else{
                $user = $this->appendRolesAndPermissions($user);
                $user['token'] = $user->createToken('token')->plainTextToken;
                $message = 'user logged in successfully!';
                $code = 200;
            }
        }else{
            $message = 'user not found';
            $code = 404;
        }

        return ['user' => $user , 'message' => $message , 'code' => $code];
    }

    public function logout(): array
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();

            $message = 'Logged out successfully!';
            $code = 200;
        } else {
            $message = 'User not found';
            $code = 404;
        }

        return ['user' => $user, 'message' => $message, 'code' => $code];
    }


    private function appendRolesAndPermissions($user)
    {
        $roles = [];
        foreach ($user -> roles as $role){
            $roles[] = $role -> name;
        }

        unset($user['roles']);
        $user['roles'] = $roles;

        $permissions = [];
        foreach ($user -> permissions as $permission){
            $permissions[] = $permission -> name;
        }

        unset($user['permissions']);
        $user['permissions'] = $permissions;

        return $user;

    }
}

