<article class="container">
    <div class="post-header">
        <h1 class="post-header__title"><?= htmlspecialchars((string) ($meta['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h1>
        <?php
        $rawDate = $meta['date_created'] ?? '';
        if (is_int($rawDate)) {
            $ts = $rawDate;
        } elseif (!empty($rawDate)) {
            $ts = strtotime((string) $rawDate);
        } else {
            $ts = false;
        }
        $dateStr = $ts !== false ? date('j M Y', $ts) : (string) $rawDate;

        $category = (string) ($meta['category'] ?? '');
        $rawTags  = $meta['tags'] ?? [];
        $tagList  = is_array($rawTags)
            ? $rawTags
            : array_map('trim', explode(',', (string) $rawTags));
        ?>
        <?php if ($dateStr !== '' || $category !== '' || !empty($tagList)): ?>
        <div class="post-header__meta">
            <?php if ($dateStr !== ''): ?>
            <span class="post-header__date"><?= htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
            <?php if ($category !== '' || !empty($tagList)): ?>
            <span class="post-header__taxonomy">
                <?php if ($category !== ''): ?>
                <a href="/category/<?= htmlspecialchars(strtolower($category), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($category), ENT_QUOTES, 'UTF-8') ?></a>
                <?php endif; ?>
                <?php if ($category !== '' && !empty($tagList)): ?>→<?php endif; ?>
                <?php foreach ($tagList as $i => $tag): ?>
                    <?php if ($i > 0): ?><span class="post-header__tag-sep">,</span><?php endif; ?>
                    <a href="/tag/<?= htmlspecialchars(strtolower((string) $tag), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8') ?></a>
                <?php endforeach; ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="prose">
        <?= $body ?>
    </div>
</article>
