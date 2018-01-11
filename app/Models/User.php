<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    //应用积分算法
    use Traits\ActiveUserHelper;
    //redis应用 用户最后登录时间
    use Traits\LastActivedAtHelper;
 //我们还需在 User 中使用 laravel-permission 提供的 Trait —— HasRoles，此举能让我们获取到扩展包提供的所有权限和角色的操作方法。
    use HasRoles;
//引用trait notifiable 类，并将其中notify方法更改属性，和名字laravelNotify,相当于重写
    use Notifiable {
        notify as protected laravelNotify;
    }

    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }
        $this->increment('notification_count');
        //
        $this->laravelNotify($instance);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
 //建立一对多的贴关系
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    //一个用户可以用很多评论
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }


    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }
    //标记已经读状态
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        //dd( $this->unreadNotifications);
        $this->unreadNotifications->markAsRead();
    }

//设置了一个修改器  当我们尝试在模型上设置 password 的值时，该修改器将被自动调用：
    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if (strlen($value) != 60) {

            // 不等于 60，做密码加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }
//设置了一个修改器  当我们尝试在模型上设置 Avatar 的值时，该修改器将被自动调用：
    public function setAvatarAttribute($path)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! starts_with($path, 'http')) {

            // 拼接完整的 URL
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }


}
