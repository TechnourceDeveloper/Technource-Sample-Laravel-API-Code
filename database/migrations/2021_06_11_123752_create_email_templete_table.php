<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTempleteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_template', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id('email_templete_id');
            $table->string('title', 100);
            $table->string('parameter', 100)->nullable();
            $table->string('subject', 100);
            $table->text('content');
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
        Schema::dropIfExists('email_templete');
    }
}
