<div class="container">
    <div class="blog-header">
        <h1 class="blog-header__title"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h1>
    </div>

    <?php if (empty($posts)): ?>
        <p style="color:#6b7280">No posts found.</p>
    <?php else: ?>
        <ul class="home-post-list">
            <?php foreach ($posts as $post): ?>
                <?php
                $rawDate = $post['date_created'] ?? '';
                if (is_int($rawDate)) {
                    $ts = $rawDate;
                } elseif (!empty($rawDate)) {
                    $ts = strtotime((string) $rawDate);
                } else {
                    $ts = false;
                }
                $dateStr = $ts !== false ? date('j M Y', $ts) : '';
                ?>
                <li>
                    <a href="/blog/<?= htmlspecialchars((string) ($post['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars((string) ($post['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <?php if ($dateStr !== ''): ?>
                        <span class="home-post-list__date"><?= htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if (($totalPages ?? 1) > 1): ?>
        <nav class="pagination" aria-label="Page navigation">
            <span>
                <?php if ($page > 1): ?>
                    <a href="<?= htmlspecialchars($basePath . '?page=' . ($page - 1), ENT_QUOTES, 'UTF-8') ?>">&larr; Previous</a>
                <?php endif; ?>
            </span>
            <span class="pagination__counter"><?= $page ?>/<?= $totalPages ?></span>
            <span>
                <?php if ($page < $totalPages): ?>
                    <a href="<?= htmlspecialchars($basePath . '?page=' . ($page + 1), ENT_QUOTES, 'UTF-8') ?>">Next &rarr;</a>
                <?php endif; ?>
            </span>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
