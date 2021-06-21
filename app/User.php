<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



class User extends Authenticatable
{
    use Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function microposts()
    {
           return $this->hasMany(Micropost::class);
    }
    
        public function followings()
    {
           return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

        public function followers()
    {
           return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
        public function favorites(){
            return $this->belongsToMany(User::class,'favorites', 'user_id', 'micropost_id')->withTimestamps();
        }
        
    
    
    
    
    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
        public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
            public function favorite($userId)
    {
        // すでにfavoriteしているかの確認
        $exist = $this->is_favoriting($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにfavoriteしていれば何もしない
            return false;
        } else {
            // 未favoriteであればfavoriteする
            $this->favoritings()->attach($userId);
            return true;
        }
    }
        
            public function unfavorite($userId)
    {
        // すでにfavoriteしているかの確認
        $exist = $this->is_favoriting($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // すでにfavoriteしていればfavoriteを外す
            $this->favoritings()->detach($userId);
            return true;
        } else {
            // 未favoriteであれば何もしない
            return false;
        }
    }
    
        public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
        public function is_favoriting($userId)
    {
        // favorite中ユーザの中に $userIdのものが存在するか
        return $this->favoritings()->where('micropost_id', $userId)->exists();
    }

    
    
    
    //      * このユーザとフォロー中ユーザの投稿に絞り込む。
    //  */
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    
    
        public function loadRelationshipCounts()
    {
        $this->loadCount('microposts','followings','followers','favorites');
    }

}
