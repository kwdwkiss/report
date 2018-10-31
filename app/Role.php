<?php

namespace App;

class Role extends \Spatie\Permission\Models\Role
{
    public static $initData = [
        [
            'id' => 1,
            'name' => '超级管理员',
            'guard_name' => 'admin',
            'perms' => ['*'],
        ],
        [
            'id' => 2,
            'name' => '客服',
            'guard_name' => 'admin',
            'no_perms' => [
                'statement|profile',
                'statement|index',
            ],
        ],
    ];

    public static function initCreate()
    {
        $data = static::$initData;

        foreach ($data as $item) {
            $id = $item['id'];
            $name = $item['name'];
            $guard_name = $item['guard_name'];
            $perms = $item['perms'] ?? null;
            $no_perms = $item['no_perms'] ?? null;

            $role = static::updateOrCreate([
                'id' => $id
            ], compact('name', 'guard_name'));

            $role->permissions()->detach();
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
    }
}
