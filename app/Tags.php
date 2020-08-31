<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    protected $table = 'Tags';

    public function instagram(){
        return $this->belongsTo('App\Instagram', 'id', 'instagram_id');
    }
}
