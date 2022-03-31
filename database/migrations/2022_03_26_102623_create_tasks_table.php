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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template')->nullable();
            $table->unsignedBigInteger('group');
            $table->unsignedBigInteger('assigned')->nullable();
            $table->string('name');
            $table->string('description');
            $table->integer('value');
            $table->timestamp('completed_date')->nullable();
            $table->timestamps();

            $table->foreign('template')->references('id')->on('template_tasks');
            $table->foreign('group')->references('id')->on('groups');
            $table->foreign('assigned')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_template_foreign');
            $table->dropForeign('tasks_group_foreign');
            $table->dropForeign('tasks_assigned_foreign');
        });

        Schema::dropIfExists('tasks');
    }
};
