<?php

namespace App\Jobs;

use SimpleCurl;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendMessageByQwapi implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $material;
    protected $red;
    protected $time;
    protected $touser;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($material, $touser = [], $time = 0, $red = true)
    {
        $this->material = $material;
        $this->red = $red;
        $this->time = $time;
        if ($touser && is_array($touser)) {
            $this->touser = implode('|', $touser);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $config = [
            'connectTimeout' => 30,
            'dataTimeout' => 60,
            'defaultHeaders' => [],
        ];

        //从缓存中获取access_token
        $access_token = '';
        if (Storage::exists('qwapi_access_token')) {
            $exp = Storage::lastModified('qwapi_access_token');
            if ((time() - $exp) < 7200) {
                $access_token = Storage::get('qwapi_access_token');
            }
        }

        //缓存中没有就请求url获取access_token并缓存起来
        if (!$access_token) {
            $simpleCurl = SimpleCurl::setConfig($config);
            $curl = $simpleCurl->get('https://qyapi.weixin.qq.com', [], []);
            $res = json_decode($curl->getResponse(), true);
            //缓存access_token
            if (isset($res['access_token'])) {
                Storage::put('qwapi_access_token', $res['access_token']);
            }
            $access_token = $res['access_token'];
        }

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token='.$access_token;
        if ($this->red) {
            $content = "提醒：\n监控任务（".$this->material->alias."）不可用\n响应时间:".$this->time."/3000\nhttp://status.domain.net/detail/".$this->material->uuid;
        } else {
            $content = "提醒：\n监控任务（".$this->material->alias."）已经恢复\n响应时间:".$this->time."/3000\nhttp://status.domain.net/detail/".$this->material->uuid;
        }
        $data = [
            'touser' => $this->touser,
            'msgtype' => 'text',
            'agentid' => 1000006,
            'text' => [
                'content' => $content
            ]
        ];

        $res = $this->_curl($url, $data);
    }

    /**
     * 由于simpleCurl暂时不支持data转化成json，所以就自己手动写了一个简单方法
     * 我的建议是尽量不要修改引用的第三方库
     */
    protected function _curl($url, $data = [])
    {
        $strPOST = json_encode($data);
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
        curl_setopt($oCurl, CURLOPT_HEADER, 1);
        $sContent = curl_exec($oCurl);
        curl_close($oCurl);
        return $sContent;
    }
}
