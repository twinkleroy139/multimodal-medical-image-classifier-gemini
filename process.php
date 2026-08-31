<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/services/EnvLoader.php';
require_once __DIR__ . '/services/ImageProcessor.php';
require_once __DIR__ . '/services/GeminiClient.php';

// Load local .env file if available
EnvLoader::load(__DIR__ . '/.env');

// Load configurations
$config = require __DIR__ . '/config/app.php';

// Fallback lookup using EnvLoader::get() for environment key resolution
$apiKey = !empty($config['api_key']) ? $config['api_key'] : EnvLoader::get('API_KEY');
$apiEndpoint = !empty($config['api_endpoint']) 
    ? $config['api_endpoint'] 
    : EnvLoader::get('API_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_FILES['medical_image'])) {
    echo json_encode(['success' => false, 'message' => 'No image uploaded.']);
    exit;
}

$modality = $_POST['modality'] ?? 'xray';
$allowedMimes = $config['allowed_mimes'] ?? ['image/jpeg', 'image/png', 'image/webp'];
$uploadDir = $config['upload_dir'] ?? __DIR__ . '/uploads/';

$imageResult = ImageProcessor::validateAndPrepare($_FILES['medical_image'], $allowedMimes, $uploadDir);

if (!$imageResult['success']) {
    echo json_encode($imageResult);
    exit;
}

if (empty($apiKey)) {
    echo json_encode(['success' => false, 'message' => 'API Key is missing in environment configuration.']);
    exit;
}

$analysis = GeminiClient::analyzeScan(
    $imageResult['base64'],
    $imageResult['mime'],
    $modality,
    $apiKey,
    $apiEndpoint
);

echo json_encode($analysis);
exit;