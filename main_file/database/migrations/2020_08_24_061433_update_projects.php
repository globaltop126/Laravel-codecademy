<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'projects', function (Blueprint $table){
            $table->string('currency_code', 10)->default('USD')->after('currency');
            $table->string('currency_position', 10)->default('pre')->after('currency_code');
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'projects', function (Blueprint $table){
            $table->dropColumn('currency_code');
            $table->dropColumn('currency_position');
        }
        );
    }
}
