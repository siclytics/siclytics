<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name'=>'Admin',
            'username'=>'admin',
            'self_extension'=>'djahsjdjksahjkfshkdhskdhska',
            'default_organization_id'=>1122,
            'email'=>'admin@gmail.com',
            'password'=>bcrypt('admin'),
        ]);


        $role = Role::create(['name' => 'admin']);
     
        $permissions = Permission::pluck('id','id')->all();
   
        $role->syncPermissions($permissions);
     
        $user->assignRole([$role->id]);

        $this->call(DefaultBillingRatesSeeder::class);
    }
}
