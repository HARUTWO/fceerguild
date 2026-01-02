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
        Schema::table('highschool_records', function (Blueprint $table) {
            // Drop the incorrectly created foreign key constraints
            $table->dropForeign('highschool_records_created_by');
            $table->dropForeign('highschool_records_updated_by');
            $table->dropForeign('highschool_records_deleted_by');

            // Recreate the foreign key constraints correctly
            // created_by_id is NOT NULL, so use restrict on delete
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            // updated_by_id and deleted_by_id are nullable, so use nullOnDelete
            $table->foreign('updated_by_id')->references('id')->on('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreign('deleted_by_id')->references('id')->on('users')->nullOnDelete()->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('highschool_records', function (Blueprint $table) {
            // Drop the corrected foreign key constraints
            $table->dropForeign(['created_by_id']);
            $table->dropForeign(['updated_by_id']);
            $table->dropForeign(['deleted_by_id']);

            // Recreate the incorrect constraints (for rollback)
            $table->foreign('id')->references('id')->on('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreign('id')->references('id')->on('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreign('id')->references('id')->on('users')->nullOnDelete()->onUpdate('cascade');
        });
    }
};
