<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //创建监控记录表
        if (!Schema::hasTable('monitors')) {
            Schema::create('monitors', function (Blueprint $table) {
                $table->increments('id');
                $table->string('status'); //返回的Http状态
                $table->string('pid')->unique(); //外链，存被监控url的自增id
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('monitors');
    }
}
