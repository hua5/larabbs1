<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    public function before($user, $ability)
	{
        // 如果用户拥有管理内容的权限的话，即授权通过
        //插件 权限管理的方法 can
        if ($user->can('manage_contents')) {
            return true;
        }
	}
}
