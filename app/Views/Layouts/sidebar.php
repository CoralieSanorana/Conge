<?php
$sidebarTitle = $sidebarTitle ?? 'TechMada RH';
$sidebarSubtitle = $sidebarSubtitle ?? '';
$sidebarSection = $sidebarSection ?? 'Menu';
$sidebarUser = $sidebarUser ?? [];
$logoutUrl = $logoutUrl ?? site_url('logout');
$initials = $sidebarUser['initials'] ?? strtoupper(substr($sidebarUser['name'] ?? 'TR', 0, 2));
$role = strtoupper((string) (session()->get('employe_role') ?? $sidebarUser['role'] ?? 'EMPLOYE'));

if (empty($sidebarItems)) {
    $sidebarItems = match ($role) {
        'ADMIN' => [
            ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('admin/dashboard')],
            ['label' => 'Employés', 'icon' => 'bi-people', 'url' => site_url('admin/employes')],
            ['label' => 'Départements', 'icon' => 'bi-building', 'url' => site_url('admin/departements')],
            ['label' => 'Types congé', 'icon' => 'bi-calendar2-week', 'url' => site_url('admin/type-conges')],
            ['label' => 'Congés', 'icon' => 'bi-calendar2-week', 'url' => site_url('admin/conges')],
        ],
        'RH' => [
            ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('rh/dashboard')],
            ['label' => 'Approbations', 'icon' => 'bi-check2-square', 'url' => site_url('rh/conge/approbation')],
            ['label' => 'Congés', 'icon' => 'bi-calendar3', 'url' => site_url('rh/conges')],
        ],
        default => [
            ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('employe/dashboard')],
            ['label' => 'Calendrier', 'icon' => 'bi-calendar3', 'url' => site_url('employe/conge/calendar')],
            ['label' => 'Nouvelle demande', 'icon' => 'bi-plus-circle', 'url' => site_url('employe/conge/demande')],
            ['label' => 'Mes demandes', 'icon' => 'bi-calendar3', 'url' => site_url('employe/conge/historique')],
            ['label' => 'Mon profil', 'icon' => 'bi-person', 'url' => site_url('employe/profile')],
        ],
    };
}

$currentPath = trim(parse_url(current_url(), PHP_URL_PATH) ?? '', '/');
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
        <?php $itemPath = trim(parse_url($item['url'] ?? '#', PHP_URL_PATH) ?? '', '/'); ?>
        <li>
            <a href="<?= esc($item['url'] ?? '#') ?>" class="<?= $itemPath !== '' && $itemPath === $currentPath ? 'active' : (!empty($item['active']) ? 'active' : '') ?>">
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
