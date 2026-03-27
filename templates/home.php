<div class="container">
    <div class="prose home-bio">
        <?php
        $portfolioImage = (string) \App\Config::get('PORTFOLIO_IMAGE', '');
        if ($portfolioImage !== ''):
        ?>
        <img src="<?= htmlspecialchars($portfolioImage, ENT_QUOTES, 'UTF-8') ?>"
             alt="Portfolio photo"
             class="home-avatar">
        <?php endif; ?>
        <?= $body ?>
    </div>
</div>
