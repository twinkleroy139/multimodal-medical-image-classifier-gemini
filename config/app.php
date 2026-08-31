<?php
return [
    'api_key'       => EnvLoader::get('API_KEY', EnvLoader::get('GEMINI_API_KEY')),
    'api_endpoint'  => EnvLoader::get('API_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent'),
    'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
    'upload_dir'    => __DIR__ . '/../uploads/',
];