<?php
/*
 * 执行http请求
 */

namespace App\Jobs;

use App\Material;
use App\Star;
use App\User;
use App\Monitor;
use SimpleCurl;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MakeRequest implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $material;
    protected $simpleCurlConfig;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($material)
    {
        $this->material = $material;

        $this->simpleCurlConfig = [
            'connectTimeout' => 30,
            'dataTimeout' => 60,
            'defaultHeaders' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'zh-CN,zh;q=0.8,en;q=0.6,es;q=0.4,fr;q=0.2,ja;q=0.2,ko;q=0.2,ru;q=0.2,zh-TW;q=0.2,de;q=0.2,ar;q=0.2,it;q=0.2,vi;q=0.2'
            ],
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = array();
        if (!empty($this->material->param)) {
            $this->material->param = explode('&', $this->material->param);
            if ($this->material->param) {
                foreach ($this->material->param as $value) {
                    $value = explode('=', $value);
                    if (is_array($value) && isset($value[0]) && isset($value[1])) {
                        $data[$value[0]] = $value[1];
                    }
                }
            }
        }
        /*
         * 后期可扩展需要的参数：
         * request headers
         * request refers
         * basic auth
         * useragent
         *
         */
        $headers = array(
            'Cookie: ' . $this->material->cookie,
        );
        $this->_get($this->material->url, $this->material->method, $data, $headers);
    }

    protected function _get($url, $type='GET', $data=array(), $headers=array(), $timeout=60){
        $type = strtoupper($type);
        $simpleCurl = SimpleCurl::setConfig($this->simpleCurlConfig);
        switch ($type) {
            case 'GET':
                $request = $simpleCurl->get($url, $data, $headers);
                break;
            case 'POST':
                $request = $simpleCurl->post($url, $data, $headers);
                break;
            default:
                $request = $simpleCurl->get($url, $data, $headers);
                break;
        }
        if (($request->getResponseCode() >= 200) && ($request->getResponseCode() <= 399)) {
            $status = 1;
        }else {
            $status = 0;
        }

        $request_time = $request->getTotalTime() * 1000;

        $monitor = new Monitor;
        $monitor->status = $status;
        $monitor->pid = $this->material->uuid;
        $monitor->request_time = $request_time;
        $monitor->http_code = $request->getResponseCode();
        $monitor->save();

        //刷新可用率
        $queueJob = (new RefreshCache($this->material->uuid))->onQueue('refresh');
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($queueJob);

        //获取关注用户id
        $touser = [];
        $star = Star::where('mid', $this->material->uuid)->select('uid')->get();
        if ($star->toArray()) {
            foreach ($star as $value) {
                $touser[] = $value->uid;
            }
        }

        //拿到关注用户的邮箱&企业微信id等
        $users = User::whereIn('id', $touser)->select('email', 'qwid')->get();
        $emails = $qwid = [];
        if ($users->toArray()) {
            foreach ($users as $user) {
                if ($user->email) {
                    $emails[] = $user->email;
                }
                if ($user->qwid) {
                    $qwid[] = $user->qwid;
                }
            }
        }

        //报警打队列
        if (!Storage::exists($this->material->uuid.'.red') && $status == 0) {

            //发邮件
            if ($emails) {
                foreach ($emails as $key => $email) {
                    $message = ['email' => $email, 'user' => $email];
                    $config = array(
                        'template' => 'emails.alert',
                        'email' => $email,
                        'message' => $message,
                        'user' => $user,
                        'text' => "提醒：\n监控任务（".$this->material->alias."）不可用\n响应时间:".$request_time."/3000",
                        'link' => 'http://status.gongchang.net/detail/'.$this->material->uuid
                    );
                    $queueJob = (new SendMail($config, 'alert'))->onQueue('email');
                    app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($queueJob);
                }
            }
            //发企业微信
            if ($qwid) {
                $queueJob = (new SendMessageByQwapi($this->material, $qwid, $request_time))->onQueue('sendmessagebyqwapi');
                app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($queueJob);
            }

            Storage::put($this->material->uuid.'.red', 1);
        }

        //恢复打队列
        if (Storage::exists($this->material->uuid.'.red') && $status == 1) {
            //发邮件
            if ($emails) {
                foreach ($emails as $key => $email) {
                    $message = ['email' => $email, 'user' => $email];
                    $config = array(
                        'template' => 'emails.alert',
                        'email' => $email,
                        'message' => $message,
                        'user' => $user,
                        'text' => "提醒：\n监控任务（".$this->material->alias."）已经恢复\n响应时间:".$request_time."/3000",
                        'link' => 'http://status.gongchang.net/detail/'.$this->material->uuid
                    );
                    $queueJob = (new SendMail($config, 'alert'))->onQueue('email');
                    app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($queueJob);
                }
            }
            //发企业微信
            if ($qwid) {
                $queueJob = (new SendMessageByQwapi($this->material, $qwid, $request_time, false))->onQueue('sendmessagebyqwapi');
                app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($queueJob);
            }
            Storage::delete($this->material->uuid.'.red'); //恢复后就删除标记文件
        }
    }
}
