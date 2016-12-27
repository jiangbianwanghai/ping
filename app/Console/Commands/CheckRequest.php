<?php
/*
 * 预留：频率限制，在kernel类里面
 * 注册计划任务的时候，可以通过--rates=5或--rates=10
 * 控制将不同频率的任务独立开
 *
 * 说明：这里将需要处理的任务，查询出来，丢给队列
 * 后期如果任务量较大，可以增加队列的worker数量即可
 * 易于扩展
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Material;
use App\Jobs\MakeRequest;

class CheckRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get all requests tasks from db';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*
         * 此处，后期根据--rates参数，来设置不同的调取参数，先忽略这些
         */
        $job = Material::where('workding', 1)->get();
        if ($job) {
            foreach ($job as $request) {
                $queueJob = (new MakeRequest($request))->onQueue('makerequest');
                app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($queueJob);
            }
        }

    }
}
