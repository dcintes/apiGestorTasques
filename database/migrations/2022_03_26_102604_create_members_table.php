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
            $table->unsignedBigInteger('group');
            $table->unsignedBigInteger('user');
            $table->integer('balance');
            $table->boolean('admin');
            $table->timestamps();

            $table->primary(['group', 'user']);

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

        Scheme::table('users',function(Blueprint $table) {
			$table->dropForeign('members_group_foreign');
            $table->dropForeign('members_users_foreign');
		});
        
        Schema::dropIfExists('members');
    }
};
