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
            return $this->belongsToMany(Micropost::class,'favorites', 'user_id', 'micropost_id')->withTimestamps();
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
    
            public function favorite($micropostId)
    {
        
        // すでにfavoriteしているかの確認
        $exist = $this->is_favorites($micropostId);

        if ($exist) {
            // すでにfavoriteしていれば何もしない
            return false;
        } else {
            // 未favoriteであればfavoriteする
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
        
            public function unfavorite($micropostId)
    {
        // すでにfavoriteしているかの確認
        $exist = $this->is_favorites($micropostId);

        if ($exist) {
            // すでにfavoriteしていればfavoriteを外す
            $this->favorites()->detach($micropostId);
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
    

        public function is_favorites($micropostId)
    {
        // favorite中micropostの中に $micropostIdのものが存在するか
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
        
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
