<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedJobs extends Model
{
    //
    protected $table = 'failed_jobs';

    // public function getPayloadAttribute($key)
    // {
    //     return json_decode($key);
    // }
}
