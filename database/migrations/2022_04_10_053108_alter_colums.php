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
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 50)->change();
            $table->string('email', 50)->change();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->string('name', 50)->change();
            $table->string('coin', 50)->change();
        });

        Schema::table('template_tasks', function (Blueprint $table) {
            $table->string('name', 50)->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->string('name', 50)->change();
        });

        Schema::table('template_rewards', function (Blueprint $table) {
            $table->string('name', 50)->change();
            $table->string('color', 7)->change();
            $table->string('icon', 10)->change();
        });

        Schema::table('rewards', function (Blueprint $table) {
            $table->string('name', 50)->change();
            $table->string('color', 7)->change();
            $table->string('icon', 10)->change();
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->string('email', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('email', 255)->change();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('coin', 255)->change();
        });

        Schema::table('template_tasks', function (Blueprint $table) {
            $table->string('name', 255)->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->string('name', 255)->change();
        });

        Schema::table('template_rewards', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('color', 255)->change();
            $table->string('icon', 255)->change();
        });

        Schema::table('rewards', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('color', 255)->change();
            $table->string('icon', 255)->change();
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->string('email', 255)->change();
        });
    }
};
