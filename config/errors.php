<?php

return [
    'defaults' => [
        '4xx' => [
            'title' => 'Request Error',
            'detail' => 'Sorry, your request could not be completed.',
            'icon' => 'i-lucide-circle-alert',
            'color' => 'warning',
        ],
        '5xx' => [
            'title' => 'Server Error',
            'detail' => 'Whoops, something went wrong on our end. Please try again.',
            'icon' => 'i-lucide-server-crash',
            'color' => 'error',
        ],
    ],
    'statuses' => [
        401 => [
            'title' => 'Unauthorized',
            'detail' => 'Please sign in to continue.',
            'icon' => 'i-lucide-log-in',
            'color' => 'warning',
        ],
        403 => [
            'title' => 'Forbidden',
            'detail' => 'Sorry, you are unauthorized to access this resource/action.',
            'icon' => 'i-lucide-shield-alert',
            'color' => 'warning',
        ],
        404 => [
            'title' => 'Not Found',
            'detail' => 'Sorry, the resource you are looking for could not be found.',
            'icon' => 'i-lucide-search-x',
            'color' => 'warning',
        ],
        419 => [
            'title' => 'Page Expired',
            'detail' => 'The page expired, please try again.',
            'icon' => 'i-lucide-clock-alert',
            'color' => 'warning',
        ],
        429 => [
            'title' => 'Too Many Requests',
            'detail' => 'You have made too many requests. Please wait and try again.',
            'icon' => 'i-lucide-timer',
            'color' => 'warning',
        ],
        500 => [
            'title' => 'Server Error',
            'detail' => 'Whoops, something went wrong on our end. Please try again.',
            'icon' => 'i-lucide-server-crash',
            'color' => 'error',
        ],
        503 => [
            'title' => 'Service Unavailable',
            'detail' => 'Sorry, we are doing some maintenance. Please check back soon.',
            'icon' => 'i-lucide-construction',
            'color' => 'warning',
        ],
    ],
];
