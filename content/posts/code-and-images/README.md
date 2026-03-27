---
title: "Code, Trees & Images"
slug: code-and-images
date_created: 2026-01-07
category: tutorial
tags: [markdown, php, images]
seo_title: "Code, Trees & Images"
seo_description: "How to add code blocks, file tree diagrams, and images to a Markdown post."
---

This post covers the more visual side of Markdown — embedding code, drawing file trees, and including images.

---
## Inline code

Reference a function like `Router::dispatch()` or a filename like `composer.json` inline within a sentence.

---
## Code blocks

Wrap blocks in triple backticks with an optional language hint for syntax highlighting.

Bash:

```bash
composer install
php -S localhost:8080 -t public/
```

PHP:

```php
<?php
declare(strict_types=1);

echo "Hello, World!";
```

---
## File tree

A plain `text` code block works well for directory structures. Here is the full layout of this project:

```text
MarkMatter/
├── public/
│   ├── index.php
│   ├── assets/css/main.css
│   └── assets/images/avatar.svg
├── src/
│   ├── Config.php
│   ├── ContentLoader.php
│   ├── MarkdownRenderer.php
│   └── Router.php
├── content/
│   ├── config.md
│   ├── home/README.md
│   └── posts/
│       ├── README.md
│       ├── hello-world/README.md
│       └── code-and-images/
│           ├── README.md
│           └── images/
├── templates/
│   ├── layout.php
│   ├── home.php
│   ├── blog.php
│   ├── post.php
│   ├── taxonomy.php
│   └── 404.php
└── composer.json
```

---
## Images

Images live in the `images/` folder inside the post directory:

```text
content/posts/{slug}/images/{filename}
```

Reference them in Markdown using the URL pattern `/blog/{slug}/images/{filename}`:

```markdown
![Alt text](/blog/my-post/images/photo.jpg)
```

### How a post file becomes a page

The diagram below shows how the two sections of every Markdown post map to the final HTML page — the YAML front matter populates the `<head>`, and the Markdown body becomes the visible `<body>` content.

![Diagram: Markdown post to HTML page](/blog/code-and-images/images/md-to-html.svg)

The diagram adapts to light or dark mode preference.

---
## Links

[Visit the blog](/) or check out the [source on GitHub](https://github.com/sidh4u/MarkMatter).
