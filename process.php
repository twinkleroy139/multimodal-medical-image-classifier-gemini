<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/services/EnvLoader.php';
require_once __DIR__ . '/services/ImageProcessor.php';
require_once __DIR__ . '/services/GeminiClient.php';

// Attempt to load local .env file
EnvLoader::load(__DIR__ . '/.env');

// Fetch application configs if available
$config = [];
if (file_exists(__DIR__ . '/config/app.php')) {
    $config = require __DIR__ . '/config/app.php';
}

// Retrieve API credentials using EnvLoader resolution
$apiKey = EnvLoader::get('API_KEY', $config['api_key'] ?? null);
$apiEndpoint = EnvLoader::get(
    'API_ENDPOINT', 
    $config['api_endpoint'] ?? 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent'
);

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
    echo json_encode(['success' => false, 'message' => 'Error: API Key is missing in environment configuration.']);
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