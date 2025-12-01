<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
       
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $managerRole = Role::create(['name' => 'Manager']);
        $cashierRole = Role::create(['name' => 'Cashier']);


        Permission::create(['name' => 'view dashboard']);
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'pos sales']);
        Permission::create(['name' => 'delete items']);


        $superAdminRole->givePermissionTo(Permission::all());

        $managerRole->givePermissionTo([
            'view dashboard',
            'manage products',
            'view reports',
            'pos sales'
        ]);

        $cashierRole->givePermissionTo(['pos sales']);


        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'), // পাসওয়ার্ড: password
        ]);


        $user->assignRole('Super Admin');
    }
}
