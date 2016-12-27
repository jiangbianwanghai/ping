<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Monitor;
use App\Material;

use Cache;

class Board extends Controller
{
    public function date($date, Request $request){
        $start = strtotime($date);
        $end = $start + 86400;
        $data = Monitor::where('created_at', '>=', $start)
            ->where('created_at', '<', $end)
            ->get()
            ->toArray();
        $result = array();
        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $result[$hour] = 1;
        }
        $failed = array();
        foreach ($data as $d) {
            if ($d['status'] != 1) {
                $hour = strftime('%H', $d['created_at']);
                $result[$hour] = -1;
                $url = Material::find($d['pid'])->toArray();
                if ($url) {
                    $d['url'] = $url['url'];
                    $d['urlid'] = $url['id'];
                }else {
                    $d['url'] = 'unknown';
                    $d['urlid'] = '1';
                }
                $failed[] = $d;
            }
        }
        $new = array();
        foreach ($result as $key=>$val) {
            $new[] = array(
                'key' => $key,
                'val' =>$val
            );
        }
        return view('date', ['failed' => $failed, 'data' => $new, 'date' => $date, 'act' => 'board']);
    }

    public function data(Request $request){
        /*
         * todo:每次只从数据库查询最近一个小时的实时数据
         * 当天按小时缓存数据，缓存有效期24小时
         * 之前日期按每天来缓存数据，永久缓存，key:20160922result，value:1=green,0=yellow,-1=gray
         * 异步查询
         */
        if (isset($request->month)) {
            if ($request->month == date('Ym')) {
                //如果是当月，需要详细处理
                $result = array();
                //轮询当月每天的结果
                for ($i = 1; $i < date('d'); $i++) {
                    $i = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $key = $request->month . $i;
                    $result[$key] = $this->_getDayResult($key);
                }
                //处理当天
                $period = strtotime('today');
                $data = Monitor::where('created_at', '>=', $period)
                    ->where('status', '0')
                    ->get()
                    ->toArray();
                if (count($data) > 0) {
                    $result[date('Ymd')] = -1;
                }else {
                    $result[date('Ymd')] = 1;
                }
                return response()->json(['errors' => false, 'data' => $result]);
            }else {
                //不是当月，直接查缓存即可
                $days = date('d', strtotime(date('Y-m-01', strtotime($request->month . '01')) . ' +1 month -1 day'));
                $result = array();
                //轮询当月每天的结果
                for ($i = 1; $i <= $days; $i++) {
                    $i = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $key = $request->month . $i;
                    $result[$key] = $this->_getDayResult($key);
                }
                return response()->json(['errors' => false, 'data' => $result]);
            }

        }else {
            return response()->json(['errors' => true, 'message' => 'params error']);
        }

    }

    public function url($urlid, Request $request){
        $url = Material::find($urlid);
        $failed = Monitor::where('pid', $urlid)
            ->where('status', 0)
            ->get()
            ->toArray();
        return view('url', ['failed' => $failed, 'url' => $url, 'act' => 'board']);
    }


    protected function _getDayResult($day){
        $key = $day . 'status';
        if (Cache::has($key)) {
            return Cache::get($key);
        }else {
            $start = strtotime($day);
            $end = $start + 86400;
            $data = Monitor::where('created_at', '>=', $start)
                ->where('created_at', '<', $end)
                ->where('status', '0')
                ->get()
                ->toArray();
            if (count($data) > 0) {
                Cache::forever($key, -1);
                return -1;
            }else {
                Cache::forever($key, 1);
                return 1;
            }
        }
    }
}
