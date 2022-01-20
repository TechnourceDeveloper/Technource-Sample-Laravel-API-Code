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
        Schema::create('users', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id('user_id');
            $table->string('name');
            $table->string('username');
            $table->string('email');
            $table->string('mobile_number', 20)->nullable();
            $table->text('about_me')->nullable();
            $table->string('password');
            $table->date('date_of_birth')->nullable();
            $table->string('profile_image', 100)->nullable();
            $table->string('social_id', 100)->nullable();
            $table->tinyInteger('account_type')->nullable()->comment('1=>email/username, 2=>gmail,3=>facebook');
            $table->enum('user_type',['0','1','2'])->nullable()->comment("0=>admin,1=>customer,2=>specialist");
            $table->enum('is_active',['0','1'])->comment("0=>inactive,1=>active")->default('1');
            $table->enum('is_email_verified',['0','1'])->comment("0=>no,1=>yes")->default('0');
            $table->enum('deactivated_by',['0','1'])->comment("0=>own,1=>admin")->default('0');
            $table->string('otp', 6)->nullable();
            $table->datetime('otp_expire_time')->nullable();
            $table->string('notification_badge', 30)->nullable();
            $table->enum('is_profile_complete',['0','1'])->comment("0=>no,1=>yes")->default('0');
            $table->string('referral_code', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
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
