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
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign('members_group_foreign');
            $table->dropForeign('members_user_foreign');

            $table->renameColumn('group', 'group_id');
            $table->renameColumn('user', 'user_id');

            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('template_tasks', function (Blueprint $table) {
            $table->dropForeign('template_tasks_group_foreign');

            $table->renameColumn('group', 'group_id');

            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_template_foreign');
            $table->dropForeign('tasks_group_foreign');
            $table->dropForeign('tasks_assigned_foreign');

            $table->renameColumn('template', 'template_id');
            $table->renameColumn('group', 'group_id');
            $table->renameColumn('assigned', 'assigned_id');

            $table->foreign('template_id')->references('id')->on('template_tasks');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('assigned_id')->references('id')->on('members');
        });

        Schema::table('template_rewards', function (Blueprint $table) {
            $table->dropForeign('template_rewards_group_foreign');

            $table->renameColumn('group', 'group_id');

            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::table('rewards', function (Blueprint $table) {
            $table->dropForeign('rewards_template_foreign');
            $table->dropForeign('rewards_group_foreign');
            $table->dropForeign('rewards_claimer_foreign');

            $table->renameColumn('template', 'template_id');
            $table->renameColumn('group', 'group_id');
            $table->renameColumn('claimer', 'claimer_id');

            $table->foreign('template_id')->references('id')->on('template_rewards');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('claimer_id')->references('id')->on('members');
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropForeign('invitations_group_foreign');

            $table->renameColumn('group', 'group_id');

            $table->foreign('group_id')->references('id')->on('groups');
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
            $table->dropForeign('members_group_id_foreign');
            $table->dropForeign('members_user_id_foreign');

            $table->renameColumn('group_id', 'group');
            $table->renameColumn('user_id', 'user');

            $table->foreign('group')->references('id')->on('groups');
            $table->foreign('user')->references('id')->on('users');
        });

        Schema::table('template_tasks', function (Blueprint $table) {
            $table->dropForeign('template_tasks_group_id_foreign');

            $table->renameColumn('group_id', 'group');

            $table->foreign('group')->references('id')->on('groups');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_template_id_foreign');
            $table->dropForeign('tasks_group_id_foreign');
            $table->dropForeign('tasks_assigned_id_foreign');

            $table->renameColumn('template_id', 'template');
            $table->renameColumn('group_id', 'group');
            $table->renameColumn('assigned_id', 'assigned');

            $table->foreign('template')->references('id')->on('template_tasks');
            $table->foreign('group')->references('id')->on('groups');
            $table->foreign('assigned')->references('id')->on('members');
        });

        Schema::table('template_rewards', function (Blueprint $table) {
            $table->dropForeign('template_rewards_group_id_foreign');

            $table->renameColumn('group_id', 'group');

            $table->foreign('group')->references('id')->on('groups');
        });

        Schema::table('rewards', function (Blueprint $table) {
            $table->dropForeign('rewards_template_id_foreign');
            $table->dropForeign('rewards_group_id_foreign');
            $table->dropForeign('rewards_assigned_id_foreign');

            $table->renameColumn('template_id', 'template');
            $table->renameColumn('group_id', 'group');
            $table->renameColumn('assigned_id', 'assigned');

            $table->foreign('template')->references('id')->on('template_rewards');
            $table->foreign('group')->references('id')->on('groups');
            $table->foreign('assigned')->references('id')->on('members');
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropForeign('invitations_group_id_foreign');

            $table->renameColumn('group_id', 'group');

            $table->foreign('group')->references('id')->on('groups');
        });
    }
};
