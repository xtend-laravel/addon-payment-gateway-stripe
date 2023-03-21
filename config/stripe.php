<?php

return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'capture_method' => env('STRIPE_CAPTURE_METHOD', 'automatic'),
];
