<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'projects', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('status')->nullable();
            $table->integer('budget')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('currency', 50)->default('$');
            $table->integer('created_by')->default(0);
            $table->integer('is_active')->default(0);
            $table->text('descriptions')->nullable();
            $table->string('project_progress', 6)->default('false');
            $table->string('progress', 5)->default(0);
            $table->string('task_progress', 6)->default('true');
            $table->text('tags')->nullable();
            $table->integer('estimated_hrs')->default(0);
            $table->timestamps();
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
        Schema::dropIfExists('projects');
    }
}
