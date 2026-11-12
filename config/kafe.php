<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Restaurant identity
    |--------------------------------------------------------------------------
    */
    'logo' => env('KAFE_LOGO', '/images/logo.jpg'),
    // 'logo' => env('KAFE_LOGO', '/images/logo-pink.png'),

    'name' => env('KAFE_NAME', 'The Kafe'),
    'tagline' => env('KAFE_TAGLINE', 'Chai · Coffee · Snacks · Meals'),
    'location' => env('KAFE_LOCATION', 'Jakhalabandha, Assam'),
    'instagram' => env('KAFE_INSTAGRAM', 'https://www.instagram.com/the_kafe_jakhalabandha/'),
    'owner_password' => env('KAFE_OWNER_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Party / booking options (extend in config or later via admin)
    |--------------------------------------------------------------------------
    */
    'party_types' => [
        'birthday' => 'Birthday celebration',
        'anniversary' => 'Anniversary',
        'corporate' => 'Corporate / team meet',
        'family' => 'Family gathering',
        'other' => 'Other occasion',
    ],

    'booking_time_slots' => [
        '10:00' => '10:00 AM',
        '12:00' => '12:00 PM',
        '14:00' => '2:00 PM',
        '16:00' => '4:00 PM',
        '18:00' => '6:00 PM',
        '20:00' => '8:00 PM',
    ],

    'currency' => '₹',

];
