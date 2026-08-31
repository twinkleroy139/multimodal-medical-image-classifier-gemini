<?php
class GeminiClient {
    public static function analyzeScan(string $base64Image, string $mimeType, string$modality, string $apiKey, string$apiEndpoint): array {
        $modalityTitle = strtoupper($modality) . ' Scan';

        $prompt = "You are an expert AI clinical diagnostic radiologist assistant analyzing a patient's {$modalityTitle}. Examine this medical image carefully for ALL potential abnormalities including calcifications, kidney/gallbladder stones, mass lesions, tissue abnormalities, joint dislocations, and bone fractures.

Provide your response strictly as a valid JSON object with no markdown formatting blocks, adhering to this exact schema:
{
  \"abnormality_detected\": true,
  \"confidence\": 98.5,
  \"title\": \"Clear concise headline of key diagnostic result\",
  \"patient_summary\": \"2-3 clear sentences in plain English for the patient explaining what was found and what it means.\",
  \"affected_area\": \"Precise anatomical region identified or Normal baseline\",
  \"severity\": \"Urgent Urological Evaluation / High / Moderate / Normal Baseline\",
  \"bounding_boxes\": [
    {
      \"box_2d\": [ymin, xmin, ymax, xmax],
      \"label\": \"Target Abnormality Name\",
      \"type\": \"primary\"
    }
  ],
  \"clinical_findings\": {
    \"pathologies_and_calcifications\": \"Findings on stones, masses, or calcifications\",
    \"skeletal_integrity\": \"Findings on bones, alignment, or fractures\",
    \"soft_tissue_and_organs\": \"Findings on organs, shadows, or soft tissue\"
  },
  \"medications\": \"Recommended medications or None required\",
  \"suggestions\": \"Actionable medical next steps and specialist consultation advice\"
}

Note: Coordinates in box_2d MUST be normalized on a 0 to 1000 scale [ymin, xmin, ymax, xmax]. If no abnormality exists, return an empty array [] for bounding_boxes.";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Image
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $url = rtrim($apiEndpoint, '/') . '?key=' . urlencode($apiKey);

        $maxRetries = 3;
        $attempt = 0;
        $response = false;
        $httpCode = 0;

        while ($attempt < $maxRetries) {$attempt++;
            $ch = curl_init($url);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            
            // Bypass local SSL verification issues in XAMPP
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 &&$response) {
                break;
            }

            if ($attempt <$maxRetries) {
                sleep(2);
            }
        }

        if ($httpCode !== 200 || !$response) {
            $errDetail = !empty($curlError) ? $curlError : "HTTP Code {$httpCode}";
            return ['success' => false, 'message' => "API Connection Failed ({$errDetail})."];
        }

        $apiData = json_decode($response, true);
        $rawText =$apiData['candidates'][0]['content']['parts'][0]['text'] ?? '';

        $cleanJson = preg_replace('/^```json\s*|^```\s*|\s*```$/i', '', trim($rawText));
        $result = json_decode($cleanJson, true);

        if (!is_array($result)) {
            return ['success' => false, 'message' => 'Invalid JSON structure from AI model.'];
        }

        $result['success'] = true;
        return $result;
    }
}