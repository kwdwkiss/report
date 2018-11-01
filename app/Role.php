<?php

namespace App;

class Role extends \Spatie\Permission\Models\Role
{
    public static $initData = [
        [
            'name' => 'super_admin',
            'title' => '超级管理员',
            'guard_name' => 'admin',
            'level' => 1,
            'perms' => ['*'],
        ],
        [
            'name' => 'service',
            'title' => '客服',
            'guard_name' => 'admin',
            'level' => 100,
            'no_perms' => [
                'admin|index',
                'admin|create',
                'admin|show',
                'admin|update',
                'admin|delete',

                'site/get_index',
                'site/set_index',
                'behavior_log/index',

                'taxonomy/index',
                'taxonomy/create',
                'taxonomy/update',
                'taxonomy/delete',

                'statement|profile',
                'statement|index',
            ],
        ],
    ];

    public static function initCreate()
    {
        $data = static::$initData;

        $roleIds = [];
        foreach ($data as $item) {
            $name = $item['name'];
            $title = $item['title'];
            $guard_name = $item['guard_name'];
            $level = $item['level'];

            $perms = $item['perms'] ?? null;
            $no_perms = $item['no_perms'] ?? null;

            $role = static::updateOrCreate([
                'name' => $name
            ], compact('name', 'title', 'guard_name', 'level'));

            $roleIds[] = $role->id;

            if ($perms) {
                if ($perms[0] == '*') {
                    $permissions = Permission::where('guard_name', $guard_name)->get();
                } else {
                    $permissions = Permission::where('guard_name', $guard_name)
                        ->whereIn('name', $perms)->get();
                }
                $role->syncPermissions($permissions);
            } elseif ($no_perms) {
                $permissions = Permission::where('guard_name', $guard_name)
                    ->whereNotIn('name', $no_perms)->get();
                $role->syncPermissions($permissions);
            }
        }

        Role::query()->whereNotIn('id', $roleIds)->delete();

        //初始化id为1的用户为超级管理员
        $superAdmin = Admin::find(1);
        $role = Role::findByName('super_admin', 'admin');
        if ($superAdmin && $role) {
            $superAdmin->syncRoles($role);
        }
    }
}
