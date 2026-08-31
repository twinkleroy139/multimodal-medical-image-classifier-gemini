<?php
class EnvLoader {
    public static function load(string $filePath): bool {
        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (strpos($line, '#') === 0 || empty($line)) continue;
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim(trim($value), "\"' ");
                    if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                        putenv(sprintf('%s=%s', $name, $value));
                        $_ENV[$name] = $value;
                        $_SERVER[$name] = $value;
                    }
                }
            }
            return true;
        }
        return false;
    }

    public static function get(string $key, $default = null) {
        // Try exact key first
        $val = getenv($key);
        if ($val !== false && !empty($val)) return $val;
        if (!empty($_ENV[$key])) return $_ENV[$key];
        if (!empty($_SERVER[$key])) return $_SERVER[$key];

        // Fallbacks for alternative naming conventions
        $aliases = [
            'API_KEY' => ['GEMINI_API_KEY', 'GEMINI_KEY'],
            'GEMINI_API_KEY' => ['API_KEY', 'GEMINI_KEY'],
            'API_ENDPOINT' => ['GEMINI_API_ENDPOINT', 'GEMINI_ENDPOINT'],
            'GEMINI_API_ENDPOINT' => ['API_ENDPOINT', 'GEMINI_ENDPOINT']
        ];

        if (isset($aliases[$key])) {
            foreach ($aliases[$key] as $alias) {
                $val = getenv($alias);
                if ($val !== false && !empty($val)) return $val;
                if (!empty($_ENV[$alias])) return $_ENV[$alias];
                if (!empty($_SERVER[$alias])) return $_SERVER[$alias];
            }
        }

        return $default;
    }
}