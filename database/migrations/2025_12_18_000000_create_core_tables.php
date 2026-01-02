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
        // Basic reference tables
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('province_id')->constrained('provinces')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('barangays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->onDelete('restrict')->onUpdate('cascade');
            $table->string('name', 255);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fields_of_work', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('prefix_titles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('abbreviation', 50)->nullable();
            $table->foreignId('field_of_work_id')->nullable()->constrained('fields_of_work')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('suffix_titles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('abbreviation', 50)->nullable();
            $table->foreignId('field_of_work_id')->nullable()->constrained('fields_of_work')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('description', 255)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('description', 255)->nullable();
               $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
               $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
               $table->timestamps();
               $table->softDeletes();
        });

        Schema::create('committee_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('committee_id')->constrained('committees')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('committee_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('committee_id')->constrained('committees')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
        });

        // Degree-related
        Schema::create('degree_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->smallInteger('level')->nullable();
            $table->string('abbreviation', 50)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('degree_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('abbreviation', 50)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('degree_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('degree_level_id')->nullable()->constrained('degree_levels')->nullOnDelete()->onUpdate('cascade');
            $table->string('name', 255);
            $table->string('abbreviation', 50)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('degree_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('abbreviation', 50)->nullable();
            $table->foreignId('degree_level_id')->nullable()->constrained('degree_levels')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('degree_type_id')->nullable()->constrained('degree_types')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('degree_field_id')->nullable()->constrained('degree_fields')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Universities and schools
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('abbreviation', 50)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('highschools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('abbreviation', 50)->nullable();
            $table->enum('type', ['public', 'private']);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('highschool_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('subname', 255)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('volunteer_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable();
            $table->string('name', 255);
            $table->string('description', 255)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Review seasons
        Schema::create('review_seasons', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('start_month');
            $table->smallInteger('start_year');
            $table->tinyInteger('end_month');
            $table->smallInteger('end_year');
            $table->boolean('is_active')->default(false);
            $table->foreignId('set_by_user_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Batches and profiles
        Schema::create('fceer_batches', function (Blueprint $table) {
            $table->id();
            $table->integer('batch_no');
            $table->smallInteger('year')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('adviser_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('co_adviser_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('president_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('secretary_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fceer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('volunteer_number', 50)->nullable()->unique();
            $table->string('student_number', 50)->nullable()->unique();
            $table->foreignId('batch_id')->nullable()->constrained('fceer_batches')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('student_group_id')->nullable()->constrained('classrooms')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
        });

        // Address table (references barangay/city/province)
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('house_number', 50)->nullable();
            $table->string('street', 255)->nullable();
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('province_id')->constrained('provinces')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Educational and professional records
        Schema::create('educational_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('degree_program_id')->nullable()->constrained('degree_programs')->nullOnDelete()->onUpdate('cascade');
            $table->smallInteger('year_started')->nullable();
            $table->foreignId('university_id')->nullable()->constrained('universities')->nullOnDelete()->onUpdate('cascade');
            $table->smallInteger('year_graduated')->nullable();
            $table->boolean('dost_scholarship')->default(false);
            $table->string('latin_honor', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('professional_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('field_of_work_id')->nullable()->constrained('fields_of_work')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('prefix_id')->nullable()->constrained('prefix_titles')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('suffix_id')->nullable()->constrained('suffix_titles')->nullOnDelete()->onUpdate('cascade');
            $table->date('issued_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Highschool records
        Schema::create('highschool_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('highschool_id')->constrained('highschools')->onDelete('cascade')->onUpdate('cascade');
            $table->smallInteger('year_started')->nullable();
            $table->string('level', 50)->nullable();
            $table->smallInteger('year_ended')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('highschool_subject_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('highschool_subject_id')->constrained('highschool_subjects')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('grade', ['fair','good','great','exceptional']);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
        });

        // Attendance and excuses
        Schema::create('user_attendance_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->date('date');
            $table->time('time')->nullable();
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->enum('session', ['AM','PM']);
            $table->decimal('absence_value', 4, 2)->default(0);
            $table->foreignId('review_season_id')->nullable()->constrained('review_seasons')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('status_id')->nullable()->constrained('user_attendance_statuses')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('recorded_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for fast lookups
            $table->index('date');
            $table->index('status_id');
            $table->index(['user_id', 'date']);
            $table->index('recorded_by_id');
            $table->index('updated_by_id');

            // Prevent duplicate attendance per user/date/session
            $table->unique(['user_id', 'date', 'session']);
        });

        Schema::create('student_excuse_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->text('reason')->nullable();
            $table->date('date_attendance')->nullable();
            $table->enum('letter_status', ['received','approved','rejected','withdrawn'])->default('received');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->string('letter_link', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attendance_records_letter', function (Blueprint $t) {
            $t->id();
            $t->foreignId('attendance_record_id')->constrained('attendance_records')->cascadeOnDelete();
            $t->foreignId('student_excuse_letter_id')->constrained('student_excuse_letters')->cascadeOnDelete();
            $t->unique(['attendance_record_id','student_excuse_letter_id']);
            $t->timestamps();
        });

        // Profiles
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('suffix_name', 50)->nullable();
            $table->string('lived_name', 100)->nullable();
            $table->string('generational_suffix', 50)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->date('birthday')->nullable();
            $table->string('sex', 10)->nullable();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('student_group_id')->nullable()->constrained('classrooms')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('fceer_batches')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('status_id')->nullable()->constrained('user_attendance_statuses')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subject_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_subject_id')->constrained('volunteer_subjects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_primary')->default(false);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['volunteer_subject_id','user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->after('password')->constrained('user_roles')->nullOnDelete()->onUpdate('cascade');
                $table->index('role_id');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Committee positions and members already created above
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
        // Revert users table alterations
        if (Schema::hasColumn('users', 'role_id') || Schema::hasColumn('users', 'is_active') || Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'role_id')) {
                    $table->dropForeign(['role_id']);
                    $table->dropIndex(['role_id']);
                    $table->dropColumn('role_id');
                }
                if (Schema::hasColumn('users', 'is_active')) {
                    $table->dropColumn('is_active');
                }
                if (Schema::hasColumn('users', 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }

        Schema::dropIfExists('subject_teachers');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('attendance_record_excuse_letter');
        Schema::dropIfExists('student_excuse_letters');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('highschool_subject_records');
        Schema::dropIfExists('highschool_records');
        Schema::dropIfExists('professional_credentials');
        Schema::dropIfExists('educational_records');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('fceer_profiles');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('fceer_batches');
        Schema::dropIfExists('volunteer_subjects');
        Schema::dropIfExists('review_seasons');
        Schema::dropIfExists('highschool_subjects');
        Schema::dropIfExists('highschools');
        Schema::dropIfExists('universities');
        Schema::dropIfExists('degree_programs');
        Schema::dropIfExists('degree_types');
        Schema::dropIfExists('degree_fields');
        Schema::dropIfExists('degree_levels');
        Schema::dropIfExists('committee_members');
        Schema::dropIfExists('committee_positions');
        Schema::dropIfExists('committees');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('suffix_titles');
        Schema::dropIfExists('prefix_titles');
        Schema::dropIfExists('fields_of_work');
        Schema::dropIfExists('barangays');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('provinces');
    }
};
