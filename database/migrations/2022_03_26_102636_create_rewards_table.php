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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template')->nullable();
            $table->unsignedBigInteger('group');
            $table->unsignedBigInteger('claimer')->nullable();
            $table->string('name');
            $table->string('description');
            $table->integer('cost');
            $table->string('color');
            $table->string('icon');
            $table->timestamp('claimed_date')->nullable();
            $table->timestamps();

            $table->foreign('template')->references('id')->on('template_rewards');
            $table->foreign('group')->references('id')->on('groups');
            $table->foreign('claimer')->references('id')->on('members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropForeign('rewards_template_foreign');
            $table->dropForeign('rewards_group_foreign');
            $table->dropForeign('rewards_claimer_foreign');
        });

        Schema::dropIfExists('rewards');
    }
};
