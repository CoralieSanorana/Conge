<?php
$sidebarTitle = $sidebarTitle ?? 'TechMada RH';
$sidebarSubtitle = $sidebarSubtitle ?? '';
$sidebarSection = $sidebarSection ?? 'Menu';
$sidebarItems = $sidebarItems ?? [];
$sidebarUser = $sidebarUser ?? [];
$logoutUrl = $logoutUrl ?? site_url('logout');
$initials = $sidebarUser['initials'] ?? strtoupper(substr($sidebarUser['name'] ?? 'TR', 0, 2));
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
        <div class="sidebar-brand-name"><?= esc($sidebarTitle) ?><span><?= esc($sidebarSubtitle) ?></span></div>
    </div>

    <?php if (!empty($sidebarSection)) : ?>
    <div class="sidebar-section"><?= esc($sidebarSection) ?></div>
    <?php endif; ?>

    <ul class="sidebar-nav">
        <?php foreach ($sidebarItems as $item) : ?>
        <li>
            <a href="<?= esc($item['url'] ?? '#') ?>" class="<?= !empty($item['active']) ? 'active' : '' ?>">
                <i class="bi <?= esc($item['icon'] ?? 'bi-dot') ?>"></i>
                <span><?= esc($item['label'] ?? '') ?></span>
                <?php if (!empty($item['badge'])) : ?>
                <span class="nav-badge <?= esc($item['badgeClass'] ?? '') ?>"><?= esc($item['badge']) ?></span>
                <?php endif; ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <div class="sidebar-user">
        <div class="s-user-row">
            <div class="avatar <?= esc($sidebarUser['avatarClass'] ?? 'av-green') ?>"><?= esc($initials) ?></div>
            <div>
                <div class="user-name"><?= esc($sidebarUser['name'] ?? 'Utilisateur') ?></div>
                <div class="user-role"><?= esc($sidebarUser['role'] ?? '') ?><?= !empty($sidebarUser['department']) ? ' · ' . esc($sidebarUser['department']) : '' ?></div>
            </div>
            <a href="<?= esc($logoutUrl) ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</aside>
