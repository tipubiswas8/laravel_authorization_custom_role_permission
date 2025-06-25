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
        Schema::create('sa_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('sa_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('sa_role_has_sa_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('sa_role_id');
            $table->unsignedBigInteger('sa_permission_id');
            $table->primary(['sa_role_id', 'sa_permission_id']);
        });

        Schema::create('sa_model_has_sa_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('sa_role_id');
            $table->string('model_name'); // like model_type
            $table->unsignedBigInteger('sa_user_id');

            $table->primary(['sa_role_id', 'model_name', 'sa_user_id']);
        });

        Schema::create('sa_model_has_sa_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('sa_permission_id');
            $table->string('model_name');
            $table->unsignedBigInteger('sa_user_id');

            $table->primary(['sa_permission_id', 'model_name', 'sa_user_id']);
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
