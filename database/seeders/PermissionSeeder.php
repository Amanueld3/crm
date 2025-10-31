<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'api');
        $path = app_path('Models');

        $files = File::files($path);
        $modelNames = [];
        foreach ($files as $file) {
            $modelNames[] = strtolower(pathinfo($file->getFilename(), PATHINFO_FILENAME));
        }

        foreach ($modelNames as $modelName) {
            Permission::firstOrCreate(['name' => 'create:' . $modelName, 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'read:' . $modelName, 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'detail:' . $modelName, 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'update:' . $modelName, 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'delete:' . $modelName, 'guard_name' => $guard]);
        }

        Permission::firstOrCreate(['name' => 'view_pulse', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'active-inactive:user', 'guard_name' => $guard]);

        Permission::firstOrCreate(['name' => 'assign:role', 'guard_name' => $guard]);

        Permission::firstOrCreate(['name' => 'attach:permission', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'detach:permission', 'guard_name' => $guard]);

        Permission::firstOrCreate(['name' => 'read:activity-log', 'guard_name' => $guard]);

        $permissionPrefixes = [
            'read_',
            'create_',
            'update_',
            'delete_',
        ];

        $superAdmin = Role::where('name', 'super_admin')->first();
        $superAdmin->syncPermissions(Permission::all());

        $superAdmin = Role::where('name', 'Super_Admin')->first();
        $superAdmin->permissions()->detach();
        $superAdminPermissions = Permission::pluck('name')->toArray();
        // foreach ($permissionPrefixes as $prefix) {
        //     $superAdminPermissions[] = "{$prefix}role";
        //     $superAdminPermissions[] = "{$prefix}permission";
        //     $superAdminPermissions[] = "{$prefix}user";
        // }

        // $superAdminPermissions[] = "assign_role";
        // $superAdminPermissions[] = "attach_permission";
        // $superAdminPermissions[] = "detach_permission";
        // $superAdminPermissions[] = "read_activity_log";
        // $superAdminPermissions[] = "view_pulse";

        $superAdmin->givePermissionTo($superAdminPermissions);
    }
}
