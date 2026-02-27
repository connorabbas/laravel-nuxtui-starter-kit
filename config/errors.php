<?php

return [
    'defaults' => [
        '4xx' => [
            'detail' => 'Sorry, your request could not be completed.',
            'icon' => 'i-lucide-circle-alert',
            'color' => 'warning',
        ],
        '5xx' => [
            'detail' => 'Whoops, something went wrong on our end. Please try again.',
            'icon' => 'i-lucide-server-crash',
            'color' => 'error',
        ],
    ],
    'statuses' => [
        401 => [
            'detail' => 'Please sign in to continue.',
            'icon' => 'i-lucide-log-in',
            'color' => 'warning',
        ],
        403 => [
            'detail' => 'Sorry, you are unauthorized to access this resource/action.',
            'icon' => 'i-lucide-shield-alert',
            'color' => 'warning',
        ],
        404 => [
            'detail' => 'Sorry, the resource you are looking for could not be found.',
            'icon' => 'i-lucide-search-x',
            'color' => 'warning',
        ],
        419 => [
            'detail' => 'The page expired, please try again.',
            'icon' => 'i-lucide-clock-alert',
            'color' => 'warning',
        ],
        429 => [
            'detail' => 'You have made too many requests. Please wait and try again.',
            'icon' => 'i-lucide-timer',
            'color' => 'warning',
        ],
        500 => [
            'detail' => 'Whoops, something went wrong on our end. Please try again.',
            'icon' => 'i-lucide-server-crash',
            'color' => 'error',
        ],
        503 => [
            'detail' => 'Sorry, we are doing some maintenance. Please check back soon.',
            'icon' => 'i-lucide-construction',
            'color' => 'warning',
        ],
    ],
];
