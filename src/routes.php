<?php

Route::get('dashboard', 'Yywxf\Queue\Http\Controllers\QueueController@index');

Route::middleware(['api'])->prefix('api')->group(function(){
    Route::get('queue/statistics', 'Yywxf\Queue\Http\Controllers\QueueController@statistics');
    Route::get('queue/failed', 'Yywxf\Queue\Http\Controllers\QueueController@failedJobs');
    Route::get('queue/working', 'Yywxf\Queue\Http\Controllers\QueueController@workingJobs');
    Route::get('queue/status', 'Yywxf\Queue\Http\Controllers\QueueController@status');
    Route::post('queue/doQueue', 'Yywxf\Queue\Http\Controllers\QueueController@doQueue');
    Route::post('queue/doJob', 'Yywxf\Queue\Http\Controllers\QueueController@doJob');
});
