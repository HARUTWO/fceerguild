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
        Schema::table('fceer_profiles', function (Blueprint $table) {
            $table->boolean('status')->default(1)->after('student_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fceer_profiles', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
