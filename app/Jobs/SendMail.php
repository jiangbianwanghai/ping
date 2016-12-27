<?php

namespace App\Jobs;

use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $mail;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mail, $type)
    {
        $this->mail = $mail;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->type) {
            case 'auth':
                //发送邮件
                $user = $this->mail['user'];
                $message = $this->mail['message'];
                Mail::send($this->mail['template'], ['token' => $this->mail['token'], 'email' => $this->mail['email']], function($message) use ($user) {
                    $message->from('tixing@domain.com', '猫头鹰状态监控助手')
                    ->to($user['email'], $user['name'])
                    ->subject('您的授权链接');
                });

                break;
            case 'finish':
                //发送邮件
                $user = $this->mail['email'];
                Mail::send($this->mail['template'], ['comment' => $this->mail['comment'], 'repo' => $this->mail['repo'], 'link' => $this->mail['link'], 'result' => $this->mail['result']], function($message) use ($user) {
                    $message->from('tixing@domain.com', '猫头鹰状态监控助手')
                    ->to($user)
                    ->subject('提醒');
                });

            case 'alert':
                $user = $this->mail['email'];
                Mail::send($this->mail['template'], ['text' => $this->mail['text'], 'link' => $this->mail['link']], function($message) use ($user) {
                    $message->from('tixing@domain.com', '猫头鹰状态监控助手')
                    ->to($user)
                    ->subject('提醒');
                });

                break;

            default:
                // code...
                break;
        }
    }
}
