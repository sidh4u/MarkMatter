# MarkMatter

A lightweight, file-based PHP content engine for building SEO-optimized websites using markdown and front matter. No framework, no database, no build step.

## Architecture

```
MarkMatter/
├── public/                        # nginx/PHP document root
│   ├── index.php                  # Single entry point
│   ├── assets/css/main.css        # All styles — colour tokens, layout, prose, responsive
│   └── assets/images/             # Static images (e.g. avatar.svg)
├── src/                           # PHP classes (namespace: App\)
│   ├── Config.php                  # Loads content/config.md + optional .env
│   ├── ContentLoader.php          # Reads Markdown files from content/
│   ├── MarkdownRenderer.php       # Converts Markdown → HTML
│   └── Router.php                 # URL dispatch and page renderers
├── content/                       # All site content (Markdown + YAML front matter)
│   ├── config.md                   # Site config: SITE_NAME, SITE_URL, social links
│   ├── home/README.md             # Homepage bio
│   └── posts/
│       ├── README.md              # Blog page heading + intro text
│       ├── hello-world/README.md  # Sample post — basic Markdown syntax
│       └── code-and-images/       # Sample post — code, trees, and images
│           ├── README.md
│           └── images/            # Post images served via /blog/{slug}/images/{file}
├── templates/                     # PHP templates
│   ├── layout.php                 # Master layout: head, nav, footer (all inlined)
│   ├── home.php                   # Homepage: bio from content/home/README.md
│   ├── blog.php                   # Blog list: body from content/posts/README.md + all posts
│   ├── post.php                   # Single post: title, date, category/tags, prose body
│   └── taxonomy.php               # Filtered post list for /category/{slug} and /tag/{slug}
└── composer.json                  # league/commonmark + spatie/yaml-front-matter
```

## Routes

| URL                    | Renderer                     | Template        |
| ---------------------- | ---------------------------- | --------------- |
| `/`                    | `Router::renderHome()`       | `home.php`      |
| `/blog`                | `Router::renderBlog()`       | `blog.php`      |
| `/blog/{slug}`         | `Router::renderPost()`       | `post.php`      |
| `/category/{slug}`     | `Router::renderTaxonomy()`   | `taxonomy.php`  |
| `/tag/{slug}`          | `Router::renderTaxonomy()`   | `taxonomy.php`  |
| `/blog/{slug}/images/{file}` | `Router::serveImage()` | raw image bytes |
| `/robots.txt`          | `Router::renderRobots()`     | inline text     |
| `/rss.xml`             | `Router::renderRss()`        | inline XML      |
| `/sitemap.xml`         | `Router::renderSitemap()`    | inline XML      |
| anything else          | `Router::render404()`        | `404.php`       |

## Adding a new post

Every content file is split into two sections by `---`:

- **YAML front matter** — populates the HTML `<head>`: browser tab title (`seo_title`), meta description (`seo_description`), and Open Graph tags (`og:title`, `og:description`, `og:url`, `og:type`, `og:site_name`). Post attributes (slug, date, category, tags) are used by the router and rendered in the post header.
- **Markdown body** — everything after the closing `---`; rendered to HTML and displayed as the visible page content.

Steps:

1. Create `content/posts/{slug}/README.md`
2. Add front matter: `title`, `slug`, `date_created`, `seo_title`, `seo_description`
3. Optionally add `category` (string) and `tags` (YAML list, e.g. `[php, markdown]`)
4. Write the post body in Markdown below the `---` closing delimiter
5. It will appear automatically on `/blog` and at `/blog/{slug}`; category and tags become linked from the post header

## Configuration

Edit `content/config.md`:

```yaml
---
# App
# SITE_URL: full URL of your site, used for canonical links, OG tags, sitemap, and RSS feed.
# SITE_NAME: displayed as the nav logo and in browser tab titles.
SITE_URL: http://localhost:8080
SITE_NAME: Your Name

# Portfolio image shown at the top-right of the Home page (text wraps around it).
# Use a path like /assets/images/avatar.svg or an absolute URL.
# Leave blank to hide the portfolio image.
PORTFOLIO_IMAGE: /assets/images/avatar.svg

# Default color theme for first-time visitors.
# Accepted values: light | dark
# Once a visitor clicks the toggle their choice is saved in the browser and
# takes priority over this setting on all future visits.
THEME: light

# Number of posts shown per page on /blog, /category/*, and /tag/* pages.
POSTS_PER_PAGE: 10

# Social links — icons appear in the nav in the order listed here.
# Leave a value blank or remove the line entirely to hide that icon.
SOCIAL_LINKEDIN: https://linkedin.com/in/yourhandle
SOCIAL_TWITTER: https://twitter.com/yourhandle
SOCIAL_GITHUB: https://github.com/yourhandle
---
```

Use a `.env` file in the project root to override any config key locally (e.g. `SITE_URL=http://localhost:8080`).

## Running locally

```bash
composer install
php -S localhost:8080 -t public/
```

See `README.md` for the nginx + php-fpm option.

## Key conventions

- **Theme** — `data-theme="dark"` on `<html>` drives dark mode via CSS custom properties; default set by `THEME` key in `content/config.md`; user preference stored in `localStorage` key `theme-v1` and takes priority; an inline `<script>` at the top of `<head>` applies the theme before CSS loads to prevent flash; toggle button lives inside `.site-nav__social` (always visible, shares the same `gap` as social icons); on mobile `.site-nav__social` gets `margin-left: auto` to stay right-aligned when the links dropdown is hidden
- **No PostIndex** — `ContentLoader::getAllPosts()` does a simple glob of `content/posts/*/README.md`; `content/posts/README.md` (the blog index file) is not matched by this glob
- **Date handling** — `spatie/yaml-front-matter` via `symfony/yaml` parses YAML dates (e.g. `2025-01-01`) as Unix timestamps (int); always check `is_int($rawDate)` before calling `strtotime()`
- **`$currentSlug`** — set in each Router renderer and used in `layout.php` to apply `.active` class to nav links; taxonomy pages set it to `'blog'` so the Blog nav link stays active
- **No partials** — head, nav, and footer are inlined directly into `templates/layout.php`
- **SEO head** — every page emits: `<title>`, `<meta name="description">`, five OG tags (`og:title`, `og:description`, `og:url`, `og:type`, `og:site_name`), `<link rel="canonical">`, and `<link rel="alternate">` (RSS); `og:type` is `article` for post pages, `website` for all others; no `og:image` (no image field in front matter)
- **XML routes** — `renderRss()` and `renderSitemap()` set the correct `Content-Type` header, echo XML directly, and call `exit`; they do not use `layout.php`; both work with the PHP built-in server and nginx unchanged
- **Post images** — stored at `content/posts/{slug}/images/{file}`, served by `Router::serveImage()` at `/blog/{slug}/images/{file}`; slug is validated by the route regex `[a-z0-9-]+`, filename by `[a-zA-Z0-9_.-]+`; only whitelisted MIME types (jpg, png, gif, webp, svg) are served; images are never exposed directly from `public/`
- **Tags** — stored as a YAML list in front matter (e.g. `tags: [php, markdown]`); `ContentLoader::getPostsByTag()` normalizes both sides to lowercase for matching; tag/category URL slugs are also lowercased
- **Pagination** — `renderBlog()` and `renderTaxonomy()` read `$_GET['page']`, slice `getAllPosts()` / filtered posts by `POSTS_PER_PAGE`, and pass `$page`, `$totalPages`, `$basePath` to the template; URL pattern is `{basePath}?page=n`; pagination nav only renders when `$totalPages > 1`
- **Portfolio image** — `PORTFOLIO_IMAGE` config key; rendered as a float-right `<img class="home-avatar">` at the top of `.home-bio` in `home.php`; blank value hides it; image itself is a static file in `public/assets/images/`
