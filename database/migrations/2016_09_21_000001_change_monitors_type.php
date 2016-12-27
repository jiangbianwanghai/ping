<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMonitorsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitors', function ($table)
        {
            $table->integer('created_at')->default(0)->unsigned()->change();
            $table->integer('updated_at')->default(0)->unsigned()->change();
            $table->dropUnique('monitors_pid_unique');
        });
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
