<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimerInProjectTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('project_tasks', 'time_tracking'))
        {
            Schema::table(
                'project_tasks', function (Blueprint $table){
                $table->integer('time_tracking')->default(0)->after('order');
            }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'project_tasks', function (Blueprint $table){
            $table->dropColumn('time_tracking');
        }
        );
    }
}
