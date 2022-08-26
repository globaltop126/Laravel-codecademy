<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->integer('client')->default(0);
            $table->string('project')->nullable();
            $table->string('subject')->nullable();
            $table->string('value')->nullable();
            $table->integer('type')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->longText('notes')->nullable();
            $table->string('status', 20);
            $table->longText('contract_description')->nullable();
            $table->longText('client_signature')->nullable();
            $table->longText('owner_signature')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
