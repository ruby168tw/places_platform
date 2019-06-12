<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyAttributesInSmscodeVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smscode_verifications', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
            $table->text('captcha')->nullable()->change();
            $table->string('statuscode')->nullable()->change();
            $table->string('Error')->nullable()->change();
            $table->string('msgid')->nullable()->change();
            $table->string('smscode')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smscode_verifications', function (Blueprint $table) {
            //
        });
    }
}
