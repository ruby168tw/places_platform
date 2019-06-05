<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmscodeVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smscode_verifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phone');
            $table->string('captcha');
            $table->string('statuscode');
            $table->string('Error');
            $table->string('msgid');
            $table->string('smscode');
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
        Schema::dropIfExists('smscode_verifications');
    }
}
