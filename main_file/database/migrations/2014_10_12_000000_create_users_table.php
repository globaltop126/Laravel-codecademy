<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('type', 20)->default('user');
            $table->string('avatar')->default('avatars/avatar.png')->nullable();
            $table->integer('created_by')->default(0);
            $table->string('phone', 20)->nullable();
            $table->date('dob')->nullable();
            $table->string('gender', 10)->nullable();
            $table->text('skills')->nullable();
            $table->integer('is_active')->default(0);
            $table->integer('is_invited')->default(0);
            $table->string('lang', 5)->default('en');
            $table->text('facebook')->nullable();
            $table->text('whatsapp')->nullable();
            $table->text('instagram')->nullable();
            $table->text('likedin')->nullable();
            $table->string('mode', 6)->default('light');
            $table->integer('plan')->nullable();
            $table->date('plan_expire_date')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
