<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group');
            $table->string('email');
            $table->timestamps();

            $table->unique(["group", "email"], 'group_email_unique');

            $table->foreign('group')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Scheme::table('users',function(Blueprint $table) {
            $table->dropForeign('intitations_group_foreign');

            $table->dropUnique('group_email_unique');
		});

        Schema::dropIfExists('invitations');
    }
};
