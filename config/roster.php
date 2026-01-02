<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Roster Configuration
    |--------------------------------------------------------------------------
    |
    | This file defines the columns and settings for roster views.
    | Volunteers: System Managers, Administrators, Executives, Instructors
    | Students: Users with student role
    |
    */

    'volunteer_roles' => [
        'System Manager',
        'Administrator', 
        'Executive',
        'Instructor',
    ],

    'student_roles' => [
        'Student',
    ],

    // Initial visible columns (shown by default)
    'default_visible_columns' => [
        'row_number',
        'user_id',
        'full_name',
        'volunteer_number', // or student_number
        'email',
        'role',
    ],

    // Columns shown in archive view (only these are visible)
    'archive_columns' => [
        'row_number',
        'user_id',
        'full_name',
        'volunteer_number', // or student_number for students
        'deleted_at',
        'deleted_by',
    ],

    // Available filters
    'filters' => [
        'role' => [
            'label' => 'Role',
            'type' => 'select',
            'options_source' => 'volunteer_roles',
            'applies_to' => 'volunteers',
        ],
        'batch' => [
            'label' => 'Batch',
            'type' => 'select',
            'relation' => 'fceerProfile.batch_id',
            'model' => \App\Models\FceerBatch::class,
            'display' => 'batch_no',
        ],
        'student_group' => [
            'label' => 'Student Group',
            'type' => 'select',
            'relation' => 'fceerProfile.student_group_id',
            'model' => \App\Models\Classroom::class,
            'display' => 'name',
            'applies_to' => 'students',
        ],
        'sex' => [
            'label' => 'Sex',
            'type' => 'select',
            'relation' => 'profile.sex',
            'options' => ['Male' => 'Male', 'Female' => 'Female'],
        ],
        'is_active' => [
            'label' => 'Account Status',
            'type' => 'boolean',
            'accessor' => 'is_active',
            'true_label' => 'Active',
            'false_label' => 'Inactive',
        ],
        'email_verified' => [
            'label' => 'Email Verified',
            'type' => 'boolean',
            'accessor' => 'email_verified_at',
            'true_label' => 'Verified',
            'false_label' => 'Not Verified',
        ],
        'two_factor_enabled' => [
            'label' => '2FA Enabled',
            'type' => 'boolean',
            'accessor' => 'two_factor_confirmed_at',
            'true_label' => 'Enabled',
            'false_label' => 'Disabled',
        ],
    ],

    // All available columns grouped by section
    'columns' => [
        // Basic Info
        'row_number' => [
            'key' => 'row_number',
            'label' => '#',
            'section' => 'Basic',
            'sortable' => false,
            'searchable' => false,
        ],
        'user_id' => [
            'key' => 'user_id',
            'label' => 'User ID',
            'section' => 'Basic',
            'sortable' => true,
            'searchable' => false,
            'accessor' => 'id',
        ],
        'full_name' => [
            'key' => 'full_name',
            'label' => 'Full Name',
            'section' => 'Basic',
            'sortable' => true,
            'searchable' => true,
        ],
        'email' => [
            'key' => 'email',
            'label' => 'Email',
            'section' => 'Basic',
            'sortable' => true,
            'searchable' => true,
        ],
        'role' => [
            'key' => 'role',
            'label' => 'Role',
            'section' => 'Basic',
            'sortable' => true,
            'searchable' => true,
            'relation' => 'role.name',
        ],

        // FCEER Records
        'volunteer_number' => [
            'key' => 'volunteer_number',
            'label' => 'Volunteer #',
            'section' => 'FCEER Records',
            'sortable' => true,
            'searchable' => true,
            'relation' => 'fceerProfile.volunteer_number',
        ],
        'student_number' => [
            'key' => 'student_number',
            'label' => 'Student #',
            'section' => 'FCEER Records',
            'sortable' => true,
            'searchable' => true,
            'relation' => 'fceerProfile.student_number',
        ],
        'batch' => [
            'key' => 'batch',
            'label' => 'Batch',
            'section' => 'FCEER Records',
            'sortable' => true,
            'searchable' => true,
            'relation' => 'fceerProfile.batch.batch_no',
        ],
        'student_group' => [
            'key' => 'student_group',
            'label' => 'Student Group',
            'section' => 'FCEER Records',
            'sortable' => true,
            'searchable' => true,
            'relation' => 'fceerProfile.studentGroup.name',
        ],
        'fceer_status' => [
            'key' => 'fceer_status',
            'label' => 'FCEER Status',
            'section' => 'FCEER Records',
            'sortable' => true,
            'searchable' => false,
            'relation' => 'fceerProfile.status',
        ],
        'committee_memberships_list' => [
            'key' => 'committee_memberships_list',
            'label' => 'Committee Memberships',
            'section' => 'FCEER Records',
            'sortable' => false,
            'searchable' => false,
            'list_relation' => 'committeeMemberships',
            'list_format' => 'committee-position',
            'applies_to' => 'volunteers',
        ],
        'subject_teachers_list' => [
            'key' => 'subject_teachers_list',
            'label' => 'Subject Assignments',
            'section' => 'FCEER Records',
            'sortable' => false,
            'searchable' => false,
            'list_relation' => 'subjectTeachers',
            'list_format' => 'subject-proficiency',
            'applies_to' => 'volunteers',
        ],
        'classroom_responsibilities_list' => [
            'key' => 'classroom_responsibilities_list',
            'label' => 'Classroom Responsibilities',
            'section' => 'FCEER Records',
            'sortable' => false,
            'searchable' => false,
            'list_relation' => 'classroomResponsibilities',
            'list_format' => 'classroom-position',
        ],

        // Credentials
        'highschool_records_list' => [
            'key' => 'highschool_records_list',
            'label' => 'High School Records',
            'section' => 'Credentials',
            'sortable' => false,
            'searchable' => false,
            'list_relation' => 'highschoolRecords',
            'list_format' => 'highschool',
        ],
        'highschool_subject_records_list' => [
            'key' => 'highschool_subject_records_list',
            'label' => 'High School Subjects',
            'section' => 'Credentials',
            'sortable' => false,
            'searchable' => false,
            'list_relation' => 'highschoolSubjectRecords',
            'list_format' => 'subject-grade',
        ],
        'professional_credentials_list' => [
            'key' => 'professional_credentials_list',
            'label' => 'Professional Credentials',
            'section' => 'Credentials',
            'sortable' => false,
            'searchable' => false,
            'list_relation' => 'professionalCredentials',
            'list_format' => 'field-abbreviation',
            'applies_to' => 'volunteers',
        ],
        'educational_records_list' => [
            'key' => 'educational_records_list',
            'label' => 'Educational Records',
            'section' => 'Credentials',
            'sortable' => false,
            'searchable' => false,
            'list_relation' => 'educationalRecords',
            'list_format' => 'degree-program',
            'applies_to' => 'volunteers',
        ],

        // Personal Records - Identification
        'first_name' => [
            'key' => 'first_name',
            'label' => 'First Name',
            'section' => 'Personal Records',
            'sortable' => true,
            'searchable' => true,
            'relation' => 'profile.first_name',
        ],
        'middle_name' => [
            'key' => 'middle_name',
            'label' => 'Middle Name',
            'section' => 'Personal Records',
            'sortable' => true,
            'searchable' => true,
            'relation' => 'profile.middle_name',
        ],
        'suffix_name' => [
            'key' => 'suffix_name',
            'label' => 'Suffix',
            'section' => 'Personal Records',
            'sortable' => true,
            'searchable' => true,
            'relation' => 'profile.suffix_name',
        ],
        'lived_name' => [
            'key' => 'lived_name',
            'label' => 'Lived Name',
            'section' => 'Personal Records',
            'sortable' => true,
            'searchable' => true,
            'relation' => 'profile.lived_name',
        ],
        'sex' => [
            'key' => 'sex',
            'label' => 'Sex',
            'section' => 'Personal Records',
            'sortable' => true,
            'searchable' => false,
            'relation' => 'profile.sex',
        ],
        'birthday' => [
            'key' => 'birthday',
            'label' => 'Birthday',
            'section' => 'Personal Records',
            'sortable' => true,
            'searchable' => false,
            'relation' => 'profile.birthday',
            'format' => 'date',
        ],
        'phone_number' => [
            'key' => 'phone_number',
            'label' => 'Phone',
            'section' => 'Personal Records',
            'sortable' => false,
            'searchable' => true,
            'relation' => 'profile.phone_number',
        ],
        'facebook_link' => [
            'key' => 'facebook_link',
            'label' => 'Facebook',
            'section' => 'Personal Records',
            'sortable' => false,
            'searchable' => false,
            'relation' => 'profile.facebook_link',
        ],

        // Address (via profile.address)
        'address' => [
            'key' => 'address',
            'label' => 'Full Address',
            'section' => 'Personal Records',
            'sortable' => false,
            'searchable' => true,
            'computed' => true,
        ],

        // Account
        'is_active' => [
            'key' => 'is_active',
            'label' => 'Active',
            'section' => 'Account',
            'sortable' => true,
            'searchable' => false,
            'format' => 'boolean',
        ],
        'email_verified' => [
            'key' => 'email_verified',
            'label' => 'Email Verified',
            'section' => 'Account',
            'sortable' => true,
            'searchable' => false,
            'accessor' => 'email_verified_at',
            'format' => 'boolean',
        ],
        'two_factor_enabled' => [
            'key' => 'two_factor_enabled',
            'label' => '2FA Enabled',
            'section' => 'Account',
            'sortable' => false,
            'searchable' => false,
            'accessor' => 'two_factor_confirmed_at',
            'format' => 'boolean',
        ],
        'created_at' => [
            'key' => 'created_at',
            'label' => 'Joined',
            'section' => 'Account',
            'sortable' => true,
            'searchable' => false,
            'format' => 'date',
        ],

        // Archive-specific columns
        'deleted_at' => [
            'key' => 'deleted_at',
            'label' => 'Deleted At',
            'section' => 'Archive',
            'sortable' => true,
            'searchable' => false,
            'accessor' => 'deleted_at',
            'format' => 'datetime',
        ],
        'deleted_by' => [
            'key' => 'deleted_by',
            'label' => 'Deleted By',
            'section' => 'Archive',
            'sortable' => true,
            'searchable' => false,
            'relation' => 'deletedBy.name',
        ],
    ],

    // Eager load relationships to avoid N+1
    'eager_load' => [
        'profile.address.barangay',
        'profile.address.city',
        'profile.address.province',
        'role',
        'fceerProfile.batch',
        'fceerProfile.studentGroup',
        'deletedBy',
        'committeeMemberships.committee',
        'committeeMemberships.committeePosition',
        'subjectTeachers.volunteerSubject',
        'classroomResponsibilities.classroom',
        'classroomResponsibilities.classroomPosition',
        'highschoolRecords.highschool',
        'highschoolSubjectRecords.subject',
        'professionalCredentials.fieldOfWork',
        'professionalCredentials.prefix',
        'professionalCredentials.suffix',
        'educationalRecords.degreeProgram',
        'educationalRecords.university',
    ],
];
