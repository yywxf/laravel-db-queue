<?php

namespace Yywxf\Queue\Models;

use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    //
    protected $table = 'jobs';

    // protected $fillable = ['status'];

    public function getAvailableAtAttribute($key)
    {
        return date('Y-m-d H:i:s', $key);
    }

    public function getReservedAtAttribute($key)
    {
        return empty($key) ? '' : date('Y-m-d H:i:s', $key);
    }

}
