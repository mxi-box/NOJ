<?php

namespace App\Models\Eloquent;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use PDO;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table='users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable=[
        'name', 'email', 'password', 'avatar', 'contest_account'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden=[
        'password', 'remember_token', 'tokens'
    ];

    public function banneds() {
        return $this->hasMany('App\Models\Eloquent\UserBanned');
    }

    public function announcements() {
        return $this->hasMany('App\Models\Eloquent\Announcement');
    }

    public function permissions() {
        return $this->hasMany('App\Models\Eloquent\UserPermission');
    }

    public function hasPermission($permissionID){
        return ($this->permissions()->where(['permission_id'=>$permissionID])->count())>0;
    }
}
