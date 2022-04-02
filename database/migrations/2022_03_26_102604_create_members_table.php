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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group');
            $table->unsignedBigInteger('user')->nullable();
            $table->integer('balance');
            $table->boolean('admin');
            $table->timestamps();

            $table->unique(['group', 'user']);

            $table->foreign('group')->references('id')->on('groups');
            $table->foreign('user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign('members_group_foreign');
            $table->dropForeign('members_user_foreign');
        });

        Schema::dropIfExists('members');
    }
};
