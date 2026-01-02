<?php

return [
    'sections' => [
        'account_information' => [
            'label' => 'Account Information',
            'model' => App\Models\User::class,
            'fields' => [
                'name' => ['type' => 'text', 'label' => 'Name', 'max' => 255],
                'email' => ['type' => 'email', 'label' => 'Email', 'max' => 255],
                'username' => ['type' => 'text', 'label' => 'Username', 'max' => 255],
            ],
        ],
        'profile_picture' => [
            'label' => 'Profile Picture',
            'model' => App\Models\User::class,
            'fields' => [
                'profile_picture' => ['type' => 'file', 'label' => 'Profile Picture', 'accept' => 'image/jpeg,image/png,image/gif', 'max_size' => 10240], // 10MB
            ],
        ],
    ],
    'permissions' => ['update' => 'update'],
];
