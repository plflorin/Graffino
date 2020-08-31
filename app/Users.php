<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';

    protected  $primaryKey = 'id';

    public function comments(){
        return $this->hasMany('App\Comments', 'user_id');
    }
}
