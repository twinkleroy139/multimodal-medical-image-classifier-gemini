<?php
class ImageProcessor {
    public static function validateAndPrepare(array $file, array $allowedMimes, string$uploadDir): array {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error code: ' . $file['error']];
        }

        $fileMime = mime_content_type($file['tmp_name']);
        if (!in_array($fileMime,$allowedMimes)) {
            return ['success' => false, 'message' => 'Invalid file format. Only JPG and PNG are accepted.'];
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid('scan_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);$targetPath = $uploadDir .$fileName;

        if (!move_uploaded_file($file['tmp_name'],$targetPath)) {
            return ['success' => false, 'message' => 'Failed to save uploaded image.'];
        }

        $base64Data = base64_encode(file_get_contents($targetPath));
        @unlink($targetPath);

        return [
            'success' => true,
            'mime' => $fileMime,
            'base64' => $base64Data
        ];
    }
}