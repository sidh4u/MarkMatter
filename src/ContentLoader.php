<?php

declare(strict_types=1);

namespace App;

use Spatie\YamlFrontMatter\YamlFrontMatter;

class ContentLoader
{
    private string $contentRoot;

    public function __construct()
    {
        $this->contentRoot = ROOT . '/content';
    }

    /**
     * Load the home page content.
     *
     * @return array{meta: array<string, mixed>, body: string}|null
     */
    public function loadHome(): ?array
    {
        return $this->load($this->contentRoot . '/home/README.md');
    }

    /**
     * Load the blog page content.
     *
     * @return array{meta: array<string, mixed>, body: string}|null
     */
    public function loadBlog(): ?array
    {
        return $this->load($this->contentRoot . '/posts/README.md');
    }

    /**
     * Load a post by slug.
     * Looks for content/posts/{slug}/README.md
     *
     * @return array{meta: array<string, mixed>, body: string}|null
     */
    public function loadPost(string $slug): ?array
    {
        return $this->load($this->contentRoot . '/posts/' . $slug . '/README.md');
    }

    /**
     * Return all posts sorted by date_created descending.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllPosts(): array
    {
        $paths = glob($this->contentRoot . '/posts/*/README.md') ?: [];
        $posts = [];

        foreach ($paths as $path) {
            $result = $this->load($path);
            if ($result !== null) {
                $posts[] = $result['meta'];
            }
        }

        usort($posts, function (array $a, array $b): int {
            $da = $a['date_created'] ?? 0;
            $db = $b['date_created'] ?? 0;
            $ta = is_int($da) ? $da : (strtotime((string) $da) ?: 0);
            $tb = is_int($db) ? $db : (strtotime((string) $db) ?: 0);
            return $tb - $ta;
        });

        return $posts;
    }

    /**
     * Return all posts for a given category (case-insensitive).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPostsByCategory(string $category): array
    {
        return array_values(array_filter(
            $this->getAllPosts(),
            fn(array $p): bool => strtolower((string) ($p['category'] ?? '')) === strtolower($category)
        ));
    }

    /**
     * Return all posts that carry a given tag (case-insensitive).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPostsByTag(string $tag): array
    {
        return array_values(array_filter(
            $this->getAllPosts(),
            function (array $p) use ($tag): bool {
                $raw = $p['tags'] ?? [];
                $tags = is_array($raw)
                    ? $raw
                    : array_map('trim', explode(',', (string) $raw));
                return in_array(strtolower($tag), array_map('strtolower', $tags), true);
            }
        ));
    }

    /**
     * @return array{meta: array<string, mixed>, body: string}|null
     */
    private function load(string $path): ?array
    {
        if (!is_file($path)) {
            return null;
        }

        $raw      = file_get_contents($path);
        $document = YamlFrontMatter::parse($raw);

        return [
            'meta' => $document->matter(),
            'body' => $document->body(),
        ];
    }
}
