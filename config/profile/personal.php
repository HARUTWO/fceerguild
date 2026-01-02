<?php

return [
    'sections' => [
        'identification' => [
            'label' => 'Identification',
            'model' => App\Models\UserProfile::class,
            'fields' => [
                'first_name' => ['type' => 'text', 'label' => 'First Name', 'required' => true, 'max' => 100],
                'middle_name' => ['type' => 'text', 'label' => 'Middle Name', 'max' => 100],
                'suffix_name' => ['type' => 'text', 'label' => 'Suffix Name', 'max' => 50],
                'lived_name' => ['type' => 'text', 'label' => 'Lived Name', 'max' => 100],
                'sex' => ['type' => 'select', 'label' => 'Sex', 'options' => ['M' => 'Male', 'F' => 'Female', 'O' => 'Other'], 'required' => true],
                'birthday' => ['type' => 'date', 'label' => 'Birthday', 'required' => true],
            ],
        ],
        'contact_details' => [
            'label' => 'Contact Details',
            'model' => App\Models\UserProfile::class,
            'fields' => [
                'phone_number' => ['type' => 'text', 'label' => 'Phone Number', 'max' => 20],
                'email' => ['type' => 'email', 'label' => 'Email', 'max' => 255, 'user_field' => true],
                'facebook_link' => ['type' => 'url', 'label' => 'Facebook Link', 'max' => 255],
            ],
        ],
        'address' => [
            'label' => 'Address',
            'model' => App\Models\Address::class,
            'fields' => [
                'house_number' => ['type' => 'text', 'label' => 'House Number', 'max' => 50],
                'block_number' => ['type' => 'text', 'label' => 'Block Number', 'max' => 50],
                'street' => ['type' => 'text', 'label' => 'Street', 'max' => 255],
                'barangay_id' => ['type' => 'searchable-select', 'label' => 'Barangay', 'options' => 'barangays'],
                'city_id' => ['type' => 'searchable-select', 'label' => 'City', 'options' => 'cities'],
                'province_id' => ['type' => 'searchable-select', 'label' => 'Province', 'options' => 'provinces'],
            ],
        ],
    ],
];