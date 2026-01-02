<?php

return [
    'sections' => [
        'committee_memberships' => [
            'label' => 'Committee Memberships',
            'model' => App\Models\CommitteeMembership::class,
            'fields' => [
                'committee_id' => ['type' => 'select', 'label' => 'Committee', 'options' => 'committees', 'required' => true],
                'committee_position_id' => ['type' => 'select', 'label' => 'Position', 'options' => 'committee_positions', 'required' => true],
            ],
        ],
        'subject_teachers' => [
            'label' => 'Subject Assignments',
            'model' => App\Models\SubjectTeacher::class,
            'fields' => [
                'volunteer_subject_id' => ['type' => 'select', 'label' => 'Volunteer Subject', 'options' => 'volunteer_subjects', 'required' => true],
                'subject_proficiency' => ['type' => 'select', 'label' => 'Proficiency', 'options' => ['Beginner' => 'Beginner', 'Competent' => 'Competent', 'Proficient' => 'Proficient'], 'required' => true],
            ],
        ],
        'classroom_responsibilities' => [
            'label' => 'Classroom Responsibilities',
            'model' => App\Models\ClassroomResponsibility::class,
            'fields' => [
                'classroom_id' => ['type' => 'select', 'label' => 'Classroom', 'options' => 'classrooms', 'required' => true],
                'classroom_position_id' => ['type' => 'select', 'label' => 'Position', 'options' => 'classroom_positions', 'required' => true],
                'note' => ['type' => 'textarea', 'label' => 'Note'],
            ],
        ],
        'fceer_profile_section' => [
            'label' => 'FCEER Profile',
            'model' => App\Models\FceerProfile::class,
            'fields' => [
                'fceer_id' => ['type' => 'text', 'label' => 'FCEER ID'],
                'volunteer_number' => ['type' => 'text', 'label' => 'Volunteer Number'],
                'student_number' => ['type' => 'text', 'label' => 'Student Number'],
                'batch_id' => ['type' => 'select', 'label' => 'Batch', 'options' => 'fceer_batches'],
                'student_group_id' => ['type' => 'select', 'label' => 'Student Group', 'options' => 'classrooms'],
                'status' => ['type' => 'select', 'label' => 'Status', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
            ],
        ],
    ],
];