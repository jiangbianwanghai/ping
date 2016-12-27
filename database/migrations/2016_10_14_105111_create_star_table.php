<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //增加关注表
        if (!Schema::hasTable('stars')) {
            Schema::create('stars', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid')->default(0)->unsigned(); //用户id
                $table->string('email', 150)->nullable();
                $table->integer('mid')->default(0)->unsigned(); //监控任务id
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
        //
    }
}
