<!DOCTYPE html>
<?php $siteTheme = \App\Config::get('THEME', 'light') === 'dark' ? 'dark' : 'light'; ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>try{var _t=localStorage.getItem('theme-v1'),_s='<?= $siteTheme ?>'==='dark';if(_t==='dark'||(!_t&&_s)){document.documentElement.setAttribute('data-theme','dark');}else if(_t==='light'){document.documentElement.removeAttribute('data-theme');}}catch(e){}</script>
    <title><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></title>
<?php if (!empty($desc)): ?>
    <meta name="description" content="<?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?>">
<?php endif; ?>
<?php
    $ogUrl      = rtrim((string) \App\Config::get('SITE_URL', ''), '/')
                  . '/' . ltrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    $ogType     = ($template ?? '') === 'post' ? 'article' : 'website';
    $ogTitle    = $title ?? '';
    $ogSiteName = (string) \App\Config::get('SITE_NAME', '');
?>
    <meta property="og:type"        content="<?= htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:site_name"   content="<?= htmlspecialchars($ogSiteName, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title"       content="<?= htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8') ?>">
<?php if (!empty($desc)): ?>
    <meta property="og:description" content="<?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?>">
<?php endif; ?>
    <meta property="og:url"         content="<?= htmlspecialchars($ogUrl, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="canonical"           href="<?= htmlspecialchars($ogUrl, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="alternate" type="application/rss+xml"
          title="<?= htmlspecialchars($ogSiteName, ENT_QUOTES, 'UTF-8') ?>"
          href="<?= htmlspecialchars(rtrim((string) \App\Config::get('SITE_URL', ''), '/') . '/rss.xml', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>

<?php $currentSlug = $currentSlug ?? ''; ?>
<nav class="site-nav">
    <div class="site-nav__inner">
        <a href="/" class="site-nav__logo"><?= htmlspecialchars((string) \App\Config::get('SITE_NAME', ''), ENT_QUOTES, 'UTF-8') ?></a>

        <div class="site-nav__rhs" id="nav-rhs">
            <div class="site-nav__links">
                <a href="/" <?= $currentSlug === 'home' ? 'class="active"' : '' ?>>Home</a>
                <a href="/blog" <?= $currentSlug === 'blog' ? 'class="active"' : '' ?>>Blog</a>
            </div>

            <div class="site-nav__social">
            <?php foreach (\App\Config::social() as $link): ?>
                <a href="<?= htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8') ?>"
                   class="site-nav__social-link"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="<?= htmlspecialchars($link['name'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($link['icon'] === 'linkedin'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    <?php elseif ($link['icon'] === 'github'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                    <?php elseif ($link['icon'] === 'twitter'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
            <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            </button>
            </div>
        </div>

        <button class="site-nav__hamburger" id="nav-hamburger" aria-label="Toggle menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<main>
    <?php require __DIR__ . '/' . $template . '.php'; ?>
</main>

<footer class="site-footer">
    <div class="site-footer__inner">
        <span>&copy; <?= date('Y') ?> <?= htmlspecialchars((string) \App\Config::get('SITE_NAME', ''), ENT_QUOTES, 'UTF-8') ?></span>
        <span>Powered by &rarr; <a href="https://github.com/sidh4u/MarkMatter" target="_blank" rel="noopener noreferrer">MarkMatter</a></span>
    </div>
</footer>

<script>
(function () {
    // Hamburger menu
    var btn = document.getElementById('nav-hamburger');
    var rhs = document.getElementById('nav-rhs');
    if (btn && rhs) {
        btn.addEventListener('click', function () {
            var open = rhs.classList.toggle('is-open');
            btn.classList.toggle('is-open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Theme toggle
    var toggle = document.getElementById('theme-toggle');
    if (toggle) {
        toggle.addEventListener('click', function () {
            var dark = document.documentElement.getAttribute('data-theme') === 'dark';
            if (dark) {
                document.documentElement.removeAttribute('data-theme');
                try { localStorage.setItem('theme-v1', 'light'); } catch (e) {}
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                try { localStorage.setItem('theme-v1', 'dark'); } catch (e) {}
            }
        });
    }
}());
</script>

</body>
</html>
