<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instagram extends Model
{
    protected $table = 'instagram';

    protected  $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public function tags(){
        return $this->hasMany('App\Tags', 'instagram_id' );
    }

    public function locations(){
        return $this->hasMany('App\locations', 'instagram_id');
    }

    public function comments(){
        return $this->hasMany('App\Comments', 'instagram_id');
    }

    public function likes(){
        return $this->hasMany('App\Likes', 'instagram_id');
    }

    public function images(){
        return $this->hasMany('App\Images', 'instagram_id');
    }

    public function users_in_photo(){
        return $this->hasMany('App\Users_in_photo', 'instagram_id');
    }

    public function captions(){
        return $this->hasMany('App\Captions', 'instagram_id');
    }

    public function users(){
        return $this->hasMany('App\Users', 'instagram_id');
    }
}
