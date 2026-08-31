<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/services/EnvLoader.php';
require_once __DIR__ . '/services/ImageProcessor.php';
require_once __DIR__ . '/services/GeminiClient.php';

EnvLoader::load(__DIR__ . '/.env');
$config = require __DIR__ . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_FILES['medical_image'])) {
    echo json_encode(['success' => false, 'message' => 'No image uploaded.']);
    exit;
}

$modality = $_POST['modality'] ?? 'xray';
$imageResult = ImageProcessor::validateAndPrepare($_FILES['medical_image'], $config['allowed_mimes'], $config['upload_dir']);

if (!$imageResult['success']) {
    echo json_encode($imageResult);
    exit;
}

if (empty($config['api_key'])) {
    echo json_encode(['success' => false, 'message' => 'API Key is missing in .env']);
    exit;
}

$analysis = GeminiClient::analyzeScan(
    $imageResult['base64'],
    $imageResult['mime'],
    $modality,
    $config['api_key'],
    $config['api_endpoint']
);

echo json_encode($analysis);
exit;