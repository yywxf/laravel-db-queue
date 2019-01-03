<?php

namespace Yywxf\Queue\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FailedJobs;
use App\Models\Jobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class QueueController extends Controller
{
    //
    public function index()
    {
        return view('queue::dashboard.index');
    }

    public function statistics()
    {
        // 任务总数
        $job_count = Jobs::query()->count();
        // 等待执行任务总数，延迟执行任务
        $wait_count = Jobs::query()->where('available_at', '>', time())->count();
        $work_count = Jobs::query()->where('available_at', '<=', time())->count();
        $job_nums = Jobs::query()->selectRaw('queue,count(*) as count')->where('available_at', '<=', time())->groupBy('queue')->pluck('count', 'queue');
        // dump($job_nums);
        // 失败任务总数
        $failed_count = FailedJobs::query()->count();

        return [
            'job_count'    => $job_count,
            'wait_count'   => $wait_count,
            // 'work_count' => $job_count-$wait_count,
            'work_count'   => $work_count,
            'failed_count' => $failed_count,
            'total'        => $job_count + $failed_count,
            'job_nums'     => $job_nums,
        ];
    }

    public function status()
    {
        exec('sudo /usr/bin/supervisorctl status 2>&1', $out, $status);
        $queues = [];
        if ($status === 0) {
            foreach ($out as $item) {
                $arr = preg_split('/[ ]+/', $item, 3);
                if ($arr[1] === 'RUNNING') {
                    $time = ltrim(explode('uptime', $arr[2])[1]);
                } else if ($arr[1] === 'STOPPED') {
                    $time = date('Y-m-d H:i:s', strtotime($arr[2]));
                }
                $queues[] = [
                    'queue'  => $arr[0],
                    'status' => $arr[1],
                    'time'   => $time,
                ];
            }
        } else {
            \Log::error($out);
            return false;
        }

        return $queues;
    }

    // start stop restart
    public function doQueue(Request $request)
    {
        $queue = $request->input('queue', 'all');
        $action = $request->action;
        if (!in_array($action, ['start', 'stop', 'restart'])) {
            return ['code' => 1, 'msg' => '参数错误'];
        }
        exec('sudo /usr/bin/supervisorctl ' . $action . ' ' . $queue . ' 2>&1', $out, $status);
        if ($status === 0) {
            return ['code' => 0];
        } else {
            \Log::error($out);
            return [
                'code' => 1,
                'msg'  => $out,
            ];
        }
    }

    // retry forget flush
    public function doJob(Request $request)
    {
        $queue = $request->input('queue', 'all');
        $action = $request->action;
        \Log::info($queue);
        if (!in_array($action, ['retry', 'forget', 'flush'])) {
            return ['code' => 1, 'msg' => '参数错误'];
        }
        $status = Artisan::call('queue:' . $action, ['id' => $queue]);
        $msg = Artisan::output();
        return [
            'code' => $status,
            'msg'  => $msg,
        ];
    }

    public function failedJobs(Request $request)
    {
        if (isset($request->id)) {
            return FailedJobs::query()->find($request->id);
        } else {
            return FailedJobs::query()->paginate(10);
        }
    }

    public function workingJobs(Request $request)
    {
        return Jobs::query()->where('available_at', '<=', time())->paginate(10);
    }

}
