<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $table = 'comments';

//    protected  $primaryKey = 'id';

    public function users(){
        return $this->belongsTo('App\Users', 'id', 'user_id');
    }
}
