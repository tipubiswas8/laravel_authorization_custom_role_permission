<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sa_roles', static function (Blueprint $table) {
            $table->id(); // role id
            $table->string('name');       // For MyISAM use string('name', 225); // (or 166 for InnoDB with Redundant/Compact row format)
            $table->string('guard_name'); // For MyISAM use string('guard_name', 25);
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('sa_permissions', static function (Blueprint $table) {
            $table->id(); // permission id
            $table->string('name');       // For MyISAM use string('name', 225); // (or 166 for InnoDB with Redundant/Compact row format)
            $table->string('guard_name'); // For MyISAM use string('guard_name', 25);
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('sa_role_has_sa_permissions', static function (Blueprint $table) {
            $table->unsignedBigInteger('sa_role_id');
            $table->unsignedBigInteger('sa_permission_id');

            $table->foreign('sa_role_id')
                ->references('id') // role id
                ->on('sa_roles')
                ->onDelete('cascade');

            $table->foreign('sa_permission_id')
                ->references('id') // permission id
                ->on('sa_permissions')
                ->onDelete('cascade');

            $table->primary(['sa_role_id', 'sa_permission_id'], 'sa_role_has_sa_permissions_sa_role_id_sa_permission_id_primary');
        });

        Schema::create('sa_model_has_sa_roles', static function (Blueprint $table) {
            $table->unsignedBigInteger('sa_role_id');
            $table->string('model_name');
            $table->unsignedBigInteger('sa_user_id');
            $table->index(['sa_user_id', 'model_name'], 'sa_model_has_sa_roles_sa_user_id_model_name_index');

            $table->foreign('sa_role_id')
                ->references('id')
                ->on('sa_roles')
                ->onDelete('cascade');

            $table->primary(
                ['sa_role_id', 'model_name', 'sa_user_id'],
                'sa_model_has_sa_roles_sa_role_id_model_name_primary'
            );
        });

        Schema::create('sa_model_has_sa_permissions', static function (Blueprint $table) {
            $table->unsignedBigInteger('sa_permission_id');
            $table->string('model_name');
            $table->unsignedBigInteger('sa_user_id');
            $table->index(['sa_user_id', 'model_name'], 'sa_model_has_sa_permissions_sa_user_id_model_name_index');

            $table->foreign('sa_permission_id')
                ->references('id')
                ->on('sa_permissions')
                ->onDelete('cascade');

            $table->primary(
                ['sa_permission_id', 'model_name', 'sa_user_id'],
                'sa_model_has_sa_permissions_sa_permission_id_model_name_primary'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sa_roles');
        Schema::dropIfExists('sa_permissions');
        Schema::dropIfExists('sa_role_has_sa_permissions');
        Schema::dropIfExists('sa_model_has_sa_roles');
        Schema::dropIfExists('sa_model_has_sa_permissions');
    }
};
