<?php

declare(strict_types=1);

namespace App;

class Config
{
    private static array $data = [];

    public static function load(string $appRoot): void
    {
        self::loadSiteConfig($appRoot . '/content/config.md');

        $envPath = $appRoot . '/.env';

        if (!is_file($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $line = preg_replace('/\s+#.*$/', '', $line);

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            if (
                strlen($value) >= 2
                && (
                    (str_starts_with($value, '"') && str_ends_with($value, '"'))
                    || (str_starts_with($value, "'") && str_ends_with($value, "'"))
                )
            ) {
                $value = substr($value, 1, -1);
            }

            self::$data[$key] = $value;
        }
    }

    private static function loadSiteConfig(string $configPath): void
    {
        if (!is_file($configPath)) {
            return;
        }

        $raw  = file_get_contents($configPath);
        $doc  = \Spatie\YamlFrontMatter\YamlFrontMatter::parse($raw !== false ? $raw : '');
        $meta = $doc->matter();

        foreach ($meta as $key => $value) {
            self::$data[$key] = $value;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$data[$key] ?? $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    /**
     * Returns social links in the order defined in config.md, skipping blank entries.
     *
     * @return array<int, array{name: string, url: string, icon: string}>
     */
    public static function social(): array
    {
        $names = ['linkedin' => 'LinkedIn', 'github' => 'GitHub', 'twitter' => 'Twitter'];
        $links = [];

        foreach (self::$data as $key => $value) {
            if (!str_starts_with($key, 'SOCIAL_') || empty($value)) {
                continue;
            }
            $icon    = strtolower(substr($key, 7));
            $links[] = [
                'name' => $names[$icon] ?? ucfirst($icon),
                'url'  => (string) $value,
                'icon' => $icon,
            ];
        }

        return $links;
    }
}
