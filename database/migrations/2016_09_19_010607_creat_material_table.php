<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //创建监控物料表即存放被监控的Url表
        if (!Schema::hasTable('material')) {
            Schema::create('material', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->unique(); //对Url进行加密，用于验证唯一性
                $table->string('url'); //被状态监控的Url
                $table->string('param')->nullable(); //url附加参数
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
        Schema::drop('material');
    }
}
