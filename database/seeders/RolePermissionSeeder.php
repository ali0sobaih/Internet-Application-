<?php

namespace Database\Seeders;

use App\Models\AdminsUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;


class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']) ;
        $user = Role::create(['name' => 'user']);


        $adminUser1 = User::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        $admin1 = AdminsUser::factory()->create([
            'user_id' => $adminUser1->id,
            'user_name' => 'Admin1',
        ]);



        $adminUser2 = User::factory()->create([
            'email'=> 'admin2@gmail.com',
            'password'=> bcrypt('12345678')
        ]);
        $admin2 = AdminsUser::factory()->create([
            'user_id' => $adminUser2->id,
            'user_name' => 'Admin2'
        ]);


        $adminUser3 = User::factory()->create([
            'email'=> 'admin3@gmail.com',
            'password'=> bcrypt('12345678')
        ]);
        $admin3 = AdminsUser::factory()->create([
            'user_id' => $adminUser3->id,
            'user_name' => 'Admin3'
        ]);

        $adminUser1->assignRole($admin);
        $adminUser2->assignRole($admin);
        $adminUser3->assignRole($admin);
    }
}
