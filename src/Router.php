<?php

declare(strict_types=1);

namespace App;

class Router
{
    public static function dispatch(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path = '/' . trim((string) $path, '/');

        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        if ($path === '/') {
            self::renderHome();
            return;
        }

        if ($path === '/blog') {
            self::renderBlog();
            return;
        }

        if (preg_match('#^/blog/([a-z0-9-]+)/images/([a-zA-Z0-9_.-]+)$#', $path, $m)) {
            self::serveImage($m[1], $m[2]);
            return;
        }

        if (preg_match('#^/blog/([a-z0-9-]+)$#', $path, $m)) {
            self::renderPost($m[1]);
            return;
        }

        if (preg_match('#^/category/([a-z0-9-]+)$#', $path, $m)) {
            self::renderTaxonomy('category', $m[1]);
            return;
        }

        if (preg_match('#^/tag/([a-z0-9-]+)$#', $path, $m)) {
            self::renderTaxonomy('tag', $m[1]);
            return;
        }

        if ($path === '/robots.txt') {
            self::renderRobots();
            return;
        }

        if ($path === '/rss.xml') {
            self::renderRss();
            return;
        }

        if ($path === '/sitemap.xml') {
            self::renderSitemap();
            return;
        }

        self::render404();
    }

    private static function renderHome(): void
    {
        $loader  = new ContentLoader();
        $content = $loader->loadHome();

        if ($content === null) {
            self::render404();
            return;
        }

        $body        = (new MarkdownRenderer())->render($content['body']);
        $title       = (string) ($content['meta']['seo_title'] ?? Config::get('SITE_NAME', ''));
        $desc        = (string) ($content['meta']['seo_description'] ?? '');
        $template    = 'home';
        $currentSlug = (string) ($content['meta']['slug'] ?? 'home');

        require ROOT . '/templates/layout.php';
    }

    private static function renderBlog(): void
    {
        $loader  = new ContentLoader();
        $content = $loader->loadBlog();

        if ($content === null) {
            self::render404();
            return;
        }

        $allPosts    = $loader->getAllPosts();
        $perPage     = max(1, (int) (Config::get('POSTS_PER_PAGE', 10) ?: 10));
        $totalPosts  = count($allPosts);
        $totalPages  = max(1, (int) ceil($totalPosts / $perPage));
        $page        = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));
        $posts       = array_slice($allPosts, ($page - 1) * $perPage, $perPage);
        $body        = (new MarkdownRenderer())->render($content['body']);
        $title       = (string) ($content['meta']['seo_title'] ?? 'Blog');
        $desc        = (string) ($content['meta']['seo_description'] ?? '');
        $template    = 'blog';
        $currentSlug = (string) ($content['meta']['slug'] ?? 'blog');
        $basePath    = '/blog';

        require ROOT . '/templates/layout.php';
    }

    private static function renderPost(string $slug): void
    {
        $loader  = new ContentLoader();
        $content = $loader->loadPost($slug);

        if ($content === null) {
            self::render404();
            return;
        }

        $body        = (new MarkdownRenderer())->render($content['body']);
        $meta        = $content['meta'];
        $title       = (string) ($meta['seo_title'] ?? $meta['title'] ?? '');
        $desc        = (string) ($meta['seo_description'] ?? '');
        $template    = 'post';
        $currentSlug = '';

        require ROOT . '/templates/layout.php';
    }

    private static function renderTaxonomy(string $type, string $slug): void
    {
        $loader   = new ContentLoader();
        $allPosts = $type === 'category'
            ? $loader->getPostsByCategory($slug)
            : $loader->getPostsByTag($slug);

        $perPage     = max(1, (int) (Config::get('POSTS_PER_PAGE', 10) ?: 10));
        $totalPosts  = count($allPosts);
        $totalPages  = max(1, (int) ceil($totalPosts / $perPage));
        $page        = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));
        $posts       = array_slice($allPosts, ($page - 1) * $perPage, $perPage);

        $label       = ucfirst(str_replace('-', ' ', $slug));
        $heading     = $type === 'category' ? 'Category: ' . $label : 'Tag: ' . $label;
        $title       = $heading;
        $desc        = '';
        $template    = 'taxonomy';
        $currentSlug = 'blog';
        $basePath    = '/' . $type . '/' . $slug;

        require ROOT . '/templates/layout.php';
    }

    private static function serveImage(string $slug, string $file): void
    {
        $mimes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
        ];

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (!isset($mimes[$ext])) {
            self::render404();
            return;
        }

        $path = ROOT . '/content/posts/' . $slug . '/images/' . $file;

        if (!is_file($path)) {
            self::render404();
            return;
        }

        header('Content-Type: ' . $mimes[$ext]);
        header('Cache-Control: public, max-age=31536000');
        readfile($path);
        exit;
    }

    private static function renderRobots(): void
    {
        $siteUrl = rtrim((string) Config::get('SITE_URL', ''), '/');

        header('Content-Type: text/plain; charset=UTF-8');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        if ($siteUrl !== '') {
            echo "\nSitemap: " . $siteUrl . "/sitemap.xml\n";
        }
        exit;
    }

    private static function renderRss(): void
    {
        $posts   = (new ContentLoader())->getAllPosts();
        $siteUrl = rtrim((string) Config::get('SITE_URL', ''), '/');
        $name    = htmlspecialchars((string) Config::get('SITE_NAME', ''), ENT_XML1, 'UTF-8');

        header('Content-Type: application/rss+xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        echo '<channel>' . "\n";
        echo '  <title>' . $name . '</title>' . "\n";
        echo '  <link>' . htmlspecialchars($siteUrl, ENT_XML1, 'UTF-8') . '</link>' . "\n";
        echo '  <atom:link href="' . htmlspecialchars($siteUrl . '/rss.xml', ENT_XML1, 'UTF-8') . '" rel="self" type="application/rss+xml"/>' . "\n";
        foreach ($posts as $post) {
            $link    = htmlspecialchars($siteUrl . '/blog/' . ($post['slug'] ?? ''), ENT_XML1, 'UTF-8');
            $rawDate = $post['date_created'] ?? '';
            $ts      = is_int($rawDate) ? $rawDate : (strtotime((string) $rawDate) ?: 0);
            echo '  <item>' . "\n";
            echo '    <title>' . htmlspecialchars((string) ($post['title'] ?? ''), ENT_XML1, 'UTF-8') . '</title>' . "\n";
            echo '    <link>' . $link . '</link>' . "\n";
            echo '    <guid>' . $link . '</guid>' . "\n";
            if (!empty($post['seo_description'])) {
                echo '    <description>' . htmlspecialchars((string) $post['seo_description'], ENT_XML1, 'UTF-8') . '</description>' . "\n";
            }
            if ($ts) {
                echo '    <pubDate>' . date(DATE_RSS, $ts) . '</pubDate>' . "\n";
            }
            echo '  </item>' . "\n";
        }
        echo '</channel>' . "\n</rss>";
        exit;
    }

    private static function renderSitemap(): void
    {
        $posts   = (new ContentLoader())->getAllPosts();
        $siteUrl = rtrim((string) Config::get('SITE_URL', ''), '/');

        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach (['', '/blog'] as $staticPath) {
            echo '  <url><loc>' . htmlspecialchars($siteUrl . $staticPath, ENT_XML1, 'UTF-8') . '</loc></url>' . "\n";
        }

        foreach ($posts as $post) {
            $loc     = htmlspecialchars($siteUrl . '/blog/' . ($post['slug'] ?? ''), ENT_XML1, 'UTF-8');
            $rawDate = $post['date_created'] ?? '';
            $ts      = is_int($rawDate) ? $rawDate : (strtotime((string) $rawDate) ?: 0);
            echo '  <url>' . "\n";
            echo '    <loc>' . $loc . '</loc>' . "\n";
            if ($ts) {
                echo '    <lastmod>' . date('Y-m-d', $ts) . '</lastmod>' . "\n";
            }
            echo '  </url>' . "\n";
        }

        echo '</urlset>';
        exit;
    }

    public static function render404(): void
    {
        http_response_code(404);
        $title       = 'Page Not Found';
        $desc        = '';
        $template    = '404';
        $currentSlug = '';

        require ROOT . '/templates/layout.php';
        exit;
    }
}
