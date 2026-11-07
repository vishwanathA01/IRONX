<?php
/** Gemini REST client for the IRONX public/member fitness assistant. */
class AIService {
    private string $apiKey;
    private string $model;
    public function __construct() {
        $this->apiKey = $this->envValue('GEMINI_API_KEY') ?: $this->envValue('AI_API_KEY');
        $this->model = $this->envValue('GEMINI_MODEL') ?: 'gemini-2.5-flash';
    }
    private function envValue(string $key): string {
        $value = getenv($key);
        if ($value !== false && trim($value) !== '') return trim($value);
        $envFile = dirname(__DIR__) . '/.env';
        if (is_readable($envFile)) foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
            [$name, $raw] = explode('=', $line, 2);
            if (trim($name) === $key) return trim(trim($raw), " \t\n\r\0\x0B\"'");
        }
        return '';
    }
    public function isConfigured(): bool { return $this->apiKey !== ''; }
    public function reply(string $message, array $history = []): string {
        if (!$this->isConfigured()) throw new RuntimeException('Gemini is not configured. Add GEMINI_API_KEY to your server environment or local .env file.');
        if (!function_exists('curl_init')) throw new RuntimeException('The PHP cURL extension is required for the AI assistant.');
        $contents = [];
        foreach (array_slice($history, -8) as $turn) {
            if (!is_array($turn)) continue;
            $role = ($turn['role'] ?? '') === 'model' ? 'model' : 'user';
            $text = trim((string)($turn['text'] ?? ''));
            if ($text !== '' && mb_strlen($text) <= 2000) $contents[] = ['role' => $role, 'parts' => [['text' => $text]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];
        $payload = [
            'systemInstruction' => ['parts' => [['text' => 'You are IRONX AI Fitness Coach, a friendly, practical assistant for a gym website serving visitors and members. Help with general exercise technique, workout planning, strength, cardio, mobility, recovery, hydration and balanced nutrition. Ask clarifying questions when important details are missing. Give safe, gradual, realistic suggestions and explain that calorie/BMI estimates are approximate. Never diagnose, prescribe treatment, recommend extreme diets, steroids or unsafe training. For chest pain, fainting, severe breathlessness, injury, pregnancy-related exercise concerns or medical conditions, advise consulting a qualified healthcare professional. Do not claim to be a doctor or human trainer. Keep answers clear, encouraging and concise. Answer in English or Hindi depending on the user.']]],
            'contents' => $contents,
            'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 700],
        ];
        
$url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($this->model) . ':generateContent';

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-goog-api-key: ' . $this->apiKey
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 35
]);

// Execute Gemini API request
$raw = curl_exec($ch);

// Handle connection errors
if ($raw === false) {
    $curlError = curl_error($ch);
    throw new RuntimeException('Could not connect to Gemini: ' . $curlError);
}

// Get HTTP response status
$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Handle Gemini API errors
if ($status < 200 || $status >= 300) {
    error_log('Gemini API HTTP ' . $status . ': ' . substr($raw, 0, 1200));
    throw new RuntimeException('Gemini API request failed with HTTP ' . $status);
}

// Decode response
$data = json_decode($raw, true);

if (!is_array($data)) {
    throw new RuntimeException('Gemini returned invalid JSON.');
}

        $answer = ''; foreach (($data['candidates'][0]['content']['parts'] ?? []) as $part) if (isset($part['text'])) $answer .= $part['text'];
        if (trim($answer) === '') throw new RuntimeException('Gemini returned an empty response.');
        return trim($answer);
    }
}
