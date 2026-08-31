<?php
return [
    'api_key' => getenv('API_KEY'),
    'api_endpoint' => getenv('API_ENDPOINT') ?: 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent',
    'allowed_mimes' => ['image/jpeg', 'image/png'],
    'upload_dir' => __DIR__ . '/../uploads/'
];