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
        Schema::create('template_rewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group');
            $table->string('name');
            $table->string('description');
            $table->integer('cost');
            $table->string('color');
            $table->string('icon');
            $table->timestamps();

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
			$table->dropForeign('template_tasks_group_foreign');
		});

        Schema::dropIfExists('template_rewards');
    }
};
