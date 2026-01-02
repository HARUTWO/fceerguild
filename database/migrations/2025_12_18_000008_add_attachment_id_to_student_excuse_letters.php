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
        Schema::table('student_excuse_letters', function (Blueprint $table) {
            $table->foreignId('attachment_id')
                ->nullable()
                ->after('id')
                ->constrained('attachments')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_excuse_letters', function (Blueprint $table) {
            if (Schema::hasColumn('student_excuse_letters', 'attachment_id')) {
                $table->dropConstrainedForeignId('attachment_id');
            }
        });
    }
};
