<?php

return [
    // Centralized field definitions for reference / lookup tables.
    // Add entries here and reference via `config('reference-tables.{key}')` from views.

    'provinces' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
    ],

    'cities' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'province_id',
            'label' => 'Province',
            'type' => 'select',
            'options' => [
                'model' => App\Models\Province::class,
                'label' => 'name',
                'value' => 'id',
                'order_by' => ['name' => 'asc'],
            ],
            'rules' => 'required|exists:provinces,id',
        ],
    ],

    'barangays' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'city_id',
            'label' => 'City',
            'type' => 'select',
            'options' => [
                'model' => App\Models\City::class,
                'label' => 'name',
                'value' => 'id',
                'order_by' => ['name' => 'asc'],
            ],
            'rules' => 'required|exists:cities,id',
        ],
    ],

    'degree_fields' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'abbreviation',
            'label' => 'Abbreviation',
            'type' => 'text',
            'rules' => 'nullable|string|max:50',
        ],
    ],

    'degree_levels' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'abbreviation',
            'label' => 'Abbreviation',
            'type' => 'text',
            'rules' => 'nullable|string|max:50',
        ],
    ],

    'degree_programs' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'abbreviation',
            'label' => 'Abbreviation',
            'type' => 'text',
            'rules' => 'nullable|string|max:50',
        ],
        [
            'key' => 'degree_level_id',
            'label' => 'Degree Level',
            'type' => 'select',
            'options' => [
                'model' => App\Models\DegreeLevel::class,
                'label' => 'name',
                'value' => 'id',
                'order_by' => ['name' => 'asc'],
            ],
            'rules' => 'required|exists:degree_levels,id',
        ],
        [
            'key' => 'degree_type_id',
            'label' => 'Degree Type',
            'type' => 'select',
            'options' => [
                'model' => App\Models\DegreeType::class,
                'label' => 'name',
                'value' => 'id',
                'order_by' => ['name' => 'asc'],
            ],
            'rules' => 'required|exists:degree_types,id',
        ],
        [
            'key' => 'degree_field_id',
            'label' => 'Degree Field',
            'type' => 'select',
            'options' => [
                'model' => App\Models\DegreeField::class,
                'label' => 'name',
                'value' => 'id',
                'order_by' => ['name' => 'asc'],
            ],
            'rules' => 'required|exists:degree_fields,id',
        ],
    ],

    'degree_types' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'abbreviation',
            'label' => 'Abbreviation',
            'type' => 'text',
            'rules' => 'nullable|string|max:50',
        ],
    ],
    
    'universities' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'abbreviation',
            'label' => 'Abbreviation',
            'type' => 'text',
            'rules' => 'nullable|string|max:50',
        ],
    ],
    
    'highschools' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'abbreviation',
            'label' => 'Abbreviation',
            'type' => 'text',
            'rules' => 'nullable|string|max:50',
        ],
    ],

    'highschool_subjects' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
    ],

    'fields_of_work' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'description',
            'label' => 'Description',
            'type' => 'text',
            'rules' => 'nullable|string',
        ],
    ],

    'prefix_titles' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
            'options' => [
                'model' => App\Models\PrefixTitle::class,
                'label' => 'name',
                'value' => 'id',
                'order_by' => ['name' => 'asc'],
            ],
        ],
        [
            'key' => 'abbreviation',
            'label' => 'Abbreviation',
            'type' => 'text',
            'rules' => 'nullable|string|max:50',
        ],
        [
            'key' => 'field_of_work_id',
            'label' => 'Field of Work',
            'type' => 'select',
            'options' => [
                'model' => App\Models\FieldOfWork::class,
                'label' => 'name',
                'value' => 'id',
                'order_by' => ['name' => 'asc'],
            ],
            'rules' => 'nullable|exists:fields_of_work,id',
        ],
    ],

    'suffix_titles' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
            // SuffixTitle model uses the `title` column for the display label.
            // Provide an options descriptor so resolveFromReferenceTable('suffix_titles')
            // yields suffix records keyed by id and labeled by title.
            'options' => [
                'model' => App\Models\SuffixTitle::class,
                'label' => 'title',
                'value' => 'id',
                'order_by' => ['title' => 'asc'],
            ],
        ],
        [
            'key' => 'abbreviation',
            'label' => 'Abbreviation',
            'type' => 'text',
            'rules' => 'nullable|string|max:50',
        ],
        [
            'key' => 'field_of_work_id',
            'label' => 'Field of Work',
            'type' => 'select',
            'options' => [
                'model' => App\Models\FieldOfWork::class,
                'label' => 'name',
                'value' => 'id',
                'order_by' => ['name' => 'asc'],
            ],
            'rules' => 'nullable|exists:fields_of_work,id',
        ],
    ],

    'volunteer_subjects' => [
        [
            'key' => 'code',
            'label' => 'Code',
            'type' => 'text',
            'rules' => 'required|string|max:50',
        ],
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'description',
            'label' => 'Description',
            'type' => 'text',
            'rules' => 'nullable|string',
        ],
    ],

    'committee_positions' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'description',
            'label' => 'Description',
            'type' => 'text',
            'rules' => 'nullable|string',
        ],
    ],

    'committees' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'description',
            'label' => 'Description',
            'type' => 'text',
            'rules' => 'nullable|string',
        ],
    ],

    'classrooms' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'batch_id',
            'label' => 'Batch',
            'type' => 'select',
            'options' => [
                'model' => App\Models\FceerBatch::class,
                'label' => 'name',
                'value' => 'id',
                'order_by' => ['name' => 'asc'],
            ],
            'rules' => 'nullable|exists:fceer_batches,id',
        ],
    ],

    'classroom_positions' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'description',
            'label' => 'Description',
            'type' => 'text',
            'rules' => 'nullable|string',
        ],
    ],

    'review_seasons' => [
        [
            'key' => 'start_month',
            'label' => 'Start Month',
            'type' => 'number',
            'rules' => 'required|integer|min:1|max:12',
        ],
        [
            'key' => 'start_year',
            'label' => 'Start Year',
            'type' => 'number',
            'rules' => 'required|integer|min:2000|max:2100',
        ],
        [
            'key' => 'end_month',
            'label' => 'End Month',
            'type' => 'number',
            'rules' => 'required|integer|min:1|max:12',
        ],
        [
            'key' => 'end_year',
            'label' => 'End Year',
            'type' => 'number',
            'rules' => 'required|integer|min:2000|max:2100',
        ],
        [
            'key' => 'is_active',
            'label' => 'Active',
            'type' => 'checkbox',
            'rules' => 'boolean',
        ],
        [
            'key' => 'set_by_user_id',
            'label' => 'Set By',
            'type' => 'select',
            'options' => [
                'model' => App\Models\User::class,
                'label' => 'name',
                'value' => 'id',
                'order_by' => ['name' => 'asc'],
            ],
            'rules' => 'nullable|exists:users,id',
        ],
    ],

    'fceer_batches' => [
        [
            'key' => 'batch_no',
            'label' => 'Batch No',
            'type' => 'number',
            'rules' => 'required|integer',
        ],
        [
            'key' => 'year',
            'label' => 'Year',
            'type' => 'number',
            'rules' => 'required|integer|min:2000|max:2100',
        ],
        [
            'key' => 'review_season_id',
            'label' => 'Review Season',
            'type' => 'select',
            'options' => [
                'model' => App\Models\ReviewSeason::class,
                'label' => 'id',
                'value' => 'id',
                'order_by' => ['id' => 'desc'],
            ],
            'rules' => 'nullable|exists:review_seasons,id',
        ],
    ],

    'user_attendance_statuses' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'description',
            'label' => 'Description',
            'type' => 'text',
            'rules' => 'nullable|string',
        ],
    ],

    'user_roles' => [
        [
            'key' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'rules' => 'required|string|max:255',
        ],
        [
            'key' => 'description',
            'label' => 'Description',
            'type' => 'text',
            'rules' => 'nullable|string',
        ],
    ],

    // Example placeholder for another reference table:
    // 'categories' => [ ['key'=>'title','label'=>'Title','type'=>'text','rules'=>'required|string|max:255'] ],
];
