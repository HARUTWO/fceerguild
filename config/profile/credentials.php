<?php

return [
    'sections' => [
        'highschool_records' => [
            'label' => 'Highschool Records',
            'model' => App\Models\HighschoolRecord::class,
            'fields' => [
                'highschool_id' => ['type' => 'select', 'label' => 'Highschool', 'options' => 'highschools', 'required' => true],
                'year_started' => ['type' => 'number', 'label' => 'Year Started', 'required' => true],
                'level' => ['type' => 'select', 'label' => 'Level', 'options' => ['junior' => 'Junior', 'senior' => 'Senior'], 'required' => true],
                'year_ended' => ['type' => 'number', 'label' => 'Year Ended', 'required' => true],
            ],
        ],
        'highschool_subject_records' => [
            'label' => 'Highschool Subject Records',
            'model' => App\Models\HighschoolSubjectRecord::class,
            'fields' => [
                'highschool_subject_id' => ['type' => 'select', 'label' => 'Subject', 'options' => 'highschool_subjects', 'required' => true],
                'grade' => ['type' => 'select', 'label' => 'Grade', 'options' => ['fair (70-80)' => 'Fair (70-80)', 'good (80-90)' => 'Good (80-90)', 'great (91-95)' => 'Great (91-95)', 'exceptional (96-100)' => 'Exceptional (96-100)'], 'required' => true],
                'highschool_record_id' => ['type' => 'select', 'label' => 'Highschool Record', 'options' => 'highschool_records', 'required' => true],
            ],
        ],
        'educational_records' => [
            'label' => 'Educational Records',
            'model' => App\Models\EducationalRecord::class,
            'fields' => [
                'degree_program_id' => ['type' => 'select', 'label' => 'Degree Program', 'options' => 'degree_programs', 'required' => true],
                'university_id' => ['type' => 'select', 'label' => 'University', 'options' => 'universities', 'required' => true],
                'year_started' => ['type' => 'number', 'label' => 'Year Started', 'required' => true],
                'year_graduated' => ['type' => 'number', 'label' => 'Year Graduated', 'required' => true],
                'dost_scholarship' => ['type' => 'checkbox', 'label' => 'DOST Scholarship'],
                'latin_honor' => ['type' => 'text', 'label' => 'Latin Honor'],
            ],
        ],
        'professional_credentials' => [
            'label' => 'Professional Credentials',
            'model' => App\Models\ProfessionalCredential::class,
            'fields' => [
                'field_of_work_id' => ['type' => 'select', 'label' => 'Field of Work', 'options' => 'fields_of_work', 'required' => true],
                'prefix_id' => ['type' => 'select', 'label' => 'Prefix', 'options' => 'prefix_titles'],
                'suffix_id' => ['type' => 'select', 'label' => 'Suffix', 'options' => 'suffix_titles'],
                'issued_on' => ['type' => 'date', 'label' => 'Issued On', 'required' => true],
                'notes' => ['type' => 'textarea', 'label' => 'Notes'],
            ],
        ],
    ],
];