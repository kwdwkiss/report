<?php

namespace Modules\Common\Entities;

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
                'admin@index',
                'admin@create',
                'admin@show',
                'admin@update',
                'admin@delete',

                'permission@index',
                'permission@refresh',

                'role@index',
                'role@show',
                'role@update',

                'taxonomy@index',
                'taxonomy@create',
                'taxonomy@update',
                'taxonomy@delete',

                'statement@profile',
                'statement@index',
                'behavior_log@index',

                'site@get_basic',
                'site@set_basic',

                'user@delete',
                'user@update_api_key',
                'user@update_api_secret',

                'account_report@delete',

                'recharge@create',

                'wechat@set_server',
                'wechat@set_menu',
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
                    $permissions = Permission::where('guard_name', $guard_name)->whereIn('name', $perms)->get();
                }
                $role->syncPermissions($permissions);
            } elseif ($no_perms) {
                $permissions = Permission::where('guard_name', $guard_name)->whereNotIn('name', $no_perms)->get();
                $role->syncPermissions($permissions);
            }
        }

        //清除没有的角色
        Role::query()->whereNotIn('id', $roleIds)->delete();

        static::assignFirstSuperAdmin();
        static::assignNoRolesService();
    }

    public static function refreshSuperAdminPermission()
    {
        $superAdminRole = Role::findByName('super_admin', 'admin');
        $permissions = Permission::where('guard_name', 'admin')->get();

        $superAdminRole->syncPermissions($permissions);
    }

    public static function assignFirstSuperAdmin()
    {
        //初始化id为1的用户为超级管理员
        $superAdmin = Admin::find(1);
        $superAdminRole = Role::findByName('super_admin', 'admin');
        if ($superAdmin && $superAdminRole) {
            $superAdmin->syncRoles($superAdminRole);
        }
    }

    public static function assignNoRolesService()
    {
        //没有角色的admin初始化为客服
        $noRoleAdmins = Admin::doesntHave('roles')->get();
        $serviceRole = Role::findByName('service', 'admin');
        foreach ($noRoleAdmins as $noRoleAdmin) {
            $noRoleAdmin->syncRoles($serviceRole);
        }
    }

    public function isSuperAdmin()
    {
        return $this->name == 'super_admin';
    }
}
