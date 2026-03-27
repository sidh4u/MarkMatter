# Changelog

All notable changes to MarkMatter will be documented here.

## [1.0.0] - 2026-01-01

### Features

- Flat-file content engine — Markdown posts with YAML front matter, no database
- Homepage, blog listing, single post, category/tag filtered pages, and 404
- SEO built in — `<title>`, meta description, Open Graph tags, canonical URL, RSS 2.0 feed, XML sitemap, `robots.txt`
- Light/dark theme toggle — default via `THEME` config, user preference in `localStorage`, no flash on load
- Post images co-located with content in `content/posts/{slug}/images/`, served via PHP
- Pagination on listing pages — page size via `POSTS_PER_PAGE` in config
- Portfolio image on Home page — float-right with text wrap, configured via `PORTFOLIO_IMAGE`
- Nav with social icons (LinkedIn, Twitter, GitHub) in config-defined order; hamburger on mobile
- Responsive — desktop, tablet, mobile
