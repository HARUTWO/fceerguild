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
        Schema::create('classroom_responsibilities', function (Blueprint $table) {
            $table->id();

            // Link to classroom
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();

            // The user who holds the responsibility
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Position reference
            $table->foreignId('classroom_position_id')->constrained('classroom_positions')->cascadeOnDelete();

            $table->text('note')->nullable();

            // created_by / updated_by tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classroom_responsibilities');
    }
};
