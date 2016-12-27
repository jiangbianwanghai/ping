<?php

namespace App\Jobs;

use App\Material;
use App\Monitor;
use Illuminate\Support\Facades\DB;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefreshCache implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $uuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $thisWeekKyl = $lastWeekKyl = 0;

        //得到本周的可用率
        $thisWeekNormal = Monitor::where('pid', $this->uuid)->where('status', 1)->where(DB::raw("YEARWEEK(date_format(FROM_UNIXTIME(created_at),'%Y-%m-%d'))"), DB::raw("YEARWEEK(now())"))->count();
        $thisWeekTotal = Monitor::where('pid', $this->uuid)->where(DB::raw("YEARWEEK(date_format(FROM_UNIXTIME(created_at),'%Y-%m-%d'))"), DB::raw("YEARWEEK(now())"))->count();
        if ($thisWeekTotal) {
            $thisWeekKyl = sprintf("%.1f", $thisWeekNormal/$thisWeekTotal*100);
        }

        //得到上周的可用率
        $lastWeekNormal = Monitor::where('pid', $this->uuid)->where('status', 1)->where(DB::raw("YEARWEEK(date_format(FROM_UNIXTIME(created_at),'%Y-%m-%d'))"), DB::raw("YEARWEEK(now())-1"))->count();
        $lastWeekTotal = Monitor::where('pid', $this->uuid)->where(DB::raw("YEARWEEK(date_format(FROM_UNIXTIME(created_at),'%Y-%m-%d'))"), DB::raw("YEARWEEK(now())-1"))->count();
        if ($lastWeekTotal) {
            $lastWeekKyl = sprintf("%.1f", $lastWeekNormal/$lastWeekTotal*100);
        }

        $candle = ['last' => $lastWeekKyl, 'now' => $thisWeekKyl];
        $material = Material::where('uuid', $this->uuid)->first();
        $material->candle = json_encode($candle);
        $material->save();
    }
}
