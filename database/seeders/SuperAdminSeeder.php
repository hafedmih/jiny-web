<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->updateOrInsert([
            'email'=> 'super@admin.com'
          ],[
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'email' => 'super@admin.com',
            'password' => '$2y$10$CBtSr/Ixk3eL/V3NCuifLOgZsC3dtfgMxlrWLpAwH1pwe0FcDMpEC',
            'active' => '1',
            'created_at' => NOW(),
            'updated_at' => NOW()
        ]);

        DB::table('roles')->updateOrInsert([
            'name' => 'Super Admin'
        ],[
            'name' => 'Super Admin',
            'guard_name' => 'web',
            'display_name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Super Admin',
            'created_at' => NOW(),
            'updated_at' => NOW()
        ]);

        DB::table('roles')->updateOrInsert([
            'name' => 'user'
        ],[
            'name' => 'user',
            'guard_name' => 'web',
            'display_name' => 'User',
            'slug' => 'user',
            'description' => 'User',
            'created_at' => NOW(),
            'updated_at' => NOW()
        ]);

        DB::table('roles')->updateOrInsert([
            'name' => 'driver'
        ],[
            'name' => 'driver',
            'guard_name' => 'web',
            'display_name' => 'Driver',
            'slug' => 'driver',
            'description' => 'Driver',
            'created_at' => NOW(),
            'updated_at' => NOW()
        ]);

        DB::table('model_has_roles')->updateOrInsert([
            'model_id' => 1
        ],[
            'role_id' => 1,
            'model_type' => 'App\Models\User',
            'model_id' => 1
        ]);

        
    }
}
