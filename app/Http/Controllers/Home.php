<?php
namespace App\Http\Controllers;

use Validator;
use SimpleCurl;
use Hash;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Material;
use App\Monitor;
use App\User;
use App\Star;
use Webpatser\Uuid\Uuid;
use App\Jobs\SendMail;

class Home extends Controller
{
    public function post(Request $request) {

        //验证表单
        $rules = [
            'url' => 'required|url|unique:materials|max:255',
            'alias' => 'sometimes|max:100',
            'param' => 'sometimes|max:255',
            'cookie' => 'sometimes|max:2000'
        ];
        $message = [
            'url.required' => '地址不能为空',
            'url.url' => '地址不合法',
            'url.max' => '地址长度不能超过 :max 个字符',
            'url.unique' => '记录已存在',
            'alias.max' => '别名长度不能超过 :max 个字符',
            'param.max' => '地址参数长度不能超过 :max 个字符',
            'cookie.max' => 'Cookie长度不能超过 :max 个字符',
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return response()->json(['errors' => ['validator' => $validator->messages()]]);
        }

        //入库
        $method = $request->input('method');
        $material = new Material;
        $material->uuid = md5(Uuid::generate());
        $material->method = $method;
        $material->url = $request->input('url');
        $material->alias = $request->input('alias');
        $material->param = $request->input('param');
        $material->cookie = $request->input('cookie');
        $material->save();
        return response()->json(['errors' => false, 'id' => $material->id]);
    }

    /**
     * 详情面板
     */
    public function detail($id, Request $request)
    {
        $material = Material::where('uuid', $id)->first();
        if ($material->alias) {
            $title = $material->alias;
        } else {
            $title = '未命名';
        }
        return view('detail', ['title' => $title, 'url' => $material->url, 'uuid' => $id, 'monitor' => 1]);
    }

    /**
     * 输出相应时间图表数据
     */
    public function xysj($id)
    {
        $monitor = Monitor::where('pid', $id)->get()->toArray();
        $res = array();
        if ($monitor) {
            foreach ($monitor as $item) {
                $res[] = array(intval($item['created_at']*1000), intval($item['request_time']));
            }
        }
        return $_GET['callback']. '('. json_encode($res) . ')';
    }

    /**
     * 输出可用率图表数据
     */
    public function kyl($id)
    {
        $monitor = Monitor::where('pid', $id)->get()->toArray();
        $res = array();
        if ($monitor) {
            foreach ($monitor as $item) {
                $res[] = array(intval($item['created_at']*1000), intval($item['status']));
            }
        }
        return $_GET['callback']. '('. json_encode($res) . ')';
    }

    /**
     * 输出状态码图表数据
     */
    public function ztm($id)
    {
        $monitor = Monitor::where('pid', $id)->get()->toArray();
        $res = array();
        if ($monitor) {
            foreach ($monitor as $item) {
                $res[] = array(intval($item['created_at']*1000), intval($item['http_code']));
            }
        }
        return $_GET['callback']. '('. json_encode($res) . ')';
    }

    /**
     * 监控列表面板
     */
    public function board()
    {
        return view('board');
    }

    /**
     * 监控列表api
     */
    public function boardApi($offset = 0)
    {
        $material = Material::offset($offset)->limit(10)->select('uuid', 'url', 'alias', 'snapshot_flag', 'method', 'workding', 'candle')->get();
        if ($material->toArray()) {
            $user = Auth::user();
            if ($user) {
                //获取mid
                $midTmp = [];
                foreach ($material as $value) {
                    $midTmp[] = $value->uuid;
                }
                if ($midTmp) {
                    $star = Star::whereIn('mid', $midTmp)->where('uid', $user->id)->select('mid')->get();
                    //将mid写入临时数据库
                    $starTmp = [];
                    if ($star->toArray()) {
                        foreach ($star as $value) {
                            $starTmp[$value->mid] = 1;
                        }
                        $res = $tmp = [];
                        foreach ($material->toArray() as $value) {
                            $tmp = $value;
                            $star = 0;
                            if (isset($starTmp[$value['uuid']])) {
                                $star = 1;
                            }
                            $tmp['star'] = $star;
                            $res[] = $tmp;
                        }
                        return json_encode($res);
                    }
                }
            }
            return $material->toJson();
        }
    }

    public function sumary($id)
    {
        $week = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
        $material = Material::where('uuid', $id)->first();
        $c = strtotime($material->created_at);
        $jksc = ceil((time() - $c) / 86400);

        //整个监控周期的可用率
        $total = Monitor::where('pid', $id)->count();
        if (!$total) {
            $arr = [
                'jksc' => '-', //监控时长
                'jksc_a' => '-',
                'kyl' => '-', //一周可用率
                'kyl_all' => '-',
                'min' => '-', //最短响应时间
                'min_time' => '-',
                'max' => '-', //最长响应时间
                'max_time' => '-'
            ];
            return $_GET['callback']. '('. json_encode($arr) . ')';
        }
        $normal_total =Monitor::where('status', 1)->where('pid', $id)->count();
        $kyl_all = sprintf("%.1f", $normal_total/$total*100);

        //一周的可用率
        $total_w = Monitor::where('pid', $id)->where('created_at', '>=', time()-86400*7)->count();
        $normal_total_w =Monitor::where('status', 1)->where('pid', $id)->where('created_at', '>=', time()-86400*7)->count();
        $kyl = sprintf("%.1f", $normal_total_w/$total_w*100);

        //最短响应时间
        $min = Monitor::where('pid', $id)->min('request_time');
        $min_row = Monitor::where('pid', $id)->where('request_time', $min)->first();
        $min_time = strtotime($min_row->created_at);
        $min_time = date("Y-m-d H:i", $min_time).' '.$week[date('w', $min_time)];

        //最长响应时间
        $max = Monitor::where('pid', $id)->max('request_time');
        $max_row = Monitor::where('pid', $id)->where('request_time', $max)->first();
        $max_time = strtotime($max_row->created_at);
        $max_time = date("Y-m-d H:i", $max_time).' '.$week[date('w', $max_time)];

        $arr = [
            'jksc' => $jksc.' 天', //监控时长
            'jksc_a' => date("Y-m-d H:i", $c).' '.$week[date('w',$c)],
            'kyl' => $kyl.'%', //一周可用率
            'kyl_all' => $kyl_all.' %',
            'min' => $min, //最短响应时间
            'min_time' => $min_time,
            'max' => $max, //最长响应时间
            'max_time' => $max_time
        ];
        return $_GET['callback']. '('. json_encode($arr) . ')';
    }

    public function delJob($id)
    {
        $deletedRows = Material::where('uuid', $id)->delete();
        if ($deletedRows) {
            $deletedRows = Monitor::where('pid', $id)->delete();
            $res = ['errors' => 0, 'message' => '删除成功，该任务相关的'.$deletedRows.'条监控记录也随之删除了'];
        } else {
            $res = ['errors' => '删除失败'];
        }
        return json_encode($res);
    }

    /**
     * 监控任务编辑表单
     */
    public function editJob($id)
    {
        $material = Material::where('uuid', $id)->first();
        return view('edit', ['row' => $material]);
    }

    /**
     * 更新监控任务
     */
    public function updateJob(Request $request)
    {
        $res = Material::where('uuid', $request->input('uuid'))->first();
        $res->alias = $request->input('alias');
        $res->method = $request->input('method');
        $res->cookie = $request->input('cookie');
        $res->save();
        $res = ['errors' => 0];
        return json_encode($res);
    }

    public function snapshot($id)
    {
        $res = Material::where('uuid', $id)->first();

        $config = [
            'connectTimeout' => 30,
            'dataTimeout' => 60,
            'defaultHeaders' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'zh-CN,zh;q=0.8,en;q=0.6,es;q=0.4,fr;q=0.2,ja;q=0.2,ko;q=0.2,ru;q=0.2,zh-TW;q=0.2,de;q=0.2,ar;q=0.2,it;q=0.2,vi;q=0.2'
            ],
        ];
        $simpleCurl = SimpleCurl::setConfig($config);
        $headers = array(
                    'Cookie: ' . $res->cookie,
                );
        $data = [];
        if ($res->method == 'get') {
            $curl = $simpleCurl->get($res->url, $data, $headers);
        }
        if ($res->method == 'post') {
            $curl = $simpleCurl->post($res->url, $data, $headers);
        }

        if ($curl->getResponse()) {
            Storage::put($id.'.cache', $curl->getResponse());
            $res->snapshot_flag = 1;
            $res->save();
            $res = ['errors' => 0];
            return json_encode($res);
        } else {
            $res = ['errors' => '请求异常'];
            return json_encode($res);
        }
    }

    public function working($id, $act)
    {
        $res = Material::where('uuid', $id)->first();
        if ($act == 'play') {
            $res->workding = 1;
        }
        if ($act == 'stop') {
            $res->workding = 0;
        }
        $res->save();
        $res = ['errors' => 0];
        return json_encode($res);
    }

    public function html($id)
    {
        return Storage::get($id.'.cache');
    }

    /**
     * 发送授权链接
     */
    public function postoken(Request $request)
    {
        //表单验证
        $rules = [
            'email' => 'required|email|max:150'
        ];
        $message = [
            'required' => '请填写邮箱地址',
            'email' => '邮箱地址不合法',
            'max' => '长度不能超过 :max 个字符'
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return response()->json(['errors' => ['validator' => $validator->messages()]]);
        }

        $six_digit_random_number = mt_rand(100000, 999999);
        $email = $request->input('email');
        $tmp = explode('@', $email);
        $name = $tmp[0];
        $user = array(
            'email' => $email,
            'name' => $name
        );

        //将随机数写入数据表
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = new User;
            $user->email = $email;
        }
        $user->password = Hash::make($six_digit_random_number);
        $user->save();

        $mail = array(
            'template' => 'emails.token',
            'token' => $six_digit_random_number,
            'email' => $email,
            'message' => $message,
            'user' => $user
        );

        $queueJob = (new SendMail($mail, 'auth'))->onQueue('email');
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($queueJob);
        return json_encode(['errors' => 0]);
    }

    /**
     * 验证授权链接
     */
    public function signin(Request $request, $token)
    {
        $email = $request->get('email');
        if (Auth::attempt(['email' => $email, 'password' => $token])) {
            return redirect('/');
        } else {
            echo '验证码错误，验证失败';
        }
    }

    public function starAdd($id)
    {
        $user = Auth::user();
        if (!$user) {
            return json_encode(['errors' => '请先获取操作授权']);
        }
        $star = new Star;
        $star->uid = $user->id;
        $star->email = $user->email;
        $star->mid = $id;
        $star->save();
        return json_encode(['errors' => 0]);
    }

    public function starRemove($id)
    {
        $user = Auth::user();
        if (!$user) {
            return json_encode(['errors' => '请先获取操作授权']);
        }
        Star::where('mid', $id)->delete();
        return json_encode(['errors' => 0]);
    }

    public function profile()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/auth/email');
        }
        $user = Auth::user();
        return view('profile', ['user' => $user]);
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return json_encode(['errors' => '请先获取操作授权']);
        }
        $user = User::where('email', $user->email)->first();
        $user->qwid = $request->get('qwid');
        $user->save();
        return json_encode(['errors' => 0]);
    }

    public function table($id, $offset)
    {
        $monitor = Monitor::where('pid', $id)->offset($offset)->limit(20)->orderBy('created_at', 'desc')->get();
        if ($monitor->toArray()) {
            return $monitor->toJson();
        }
    }
}
