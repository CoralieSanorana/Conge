<?php
$pageTitle = 'Gestion des employés';
$admin = $admin ?? [];
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('admin/dashboard')],
    ['label' => 'Employés', 'icon' => 'bi-people', 'url' => site_url('admin/employes'), 'active' => true],
    ['label' => 'Congés', 'icon' => 'bi-calendar2-week', 'url' => site_url('admin/conges')],
];
$sidebarUser = [
    'name' => trim(($admin['prenom'] ?? 'Admin') . ' ' . ($admin['nom'] ?? '')) ?: 'Administrateur',
    'role' => 'ADMIN',
    'department' => $admin['departement_nom'] ?? 'Direction',
    'avatarClass' => 'av-amber',
    'initials' => strtoupper(substr($admin['prenom'] ?? 'A', 0, 1) . substr($admin['nom'] ?? 'D', 0, 1)),
];
?>
<?= $this->include('Layouts/header') ?>
<div class="app-wrap">
    <?= $this->include('Layouts/sidebar') ?>
    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Gestion des employés</div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('admin/dashboard') ?>">Accueil</a> / Employés</div>
            </div>
        </div>
        <div class="content">
            <div class="data-card">
                <div class="data-card-head"><h3>Liste des employés</h3></div>
                <table class="tbl">
                    <thead><tr><th>Employé</th><th>Email</th><th>Département</th><th>Rôle</th><th>Statut</th></tr></thead>
                    <tbody>
                    <?php if (!empty($employes)) : foreach ($employes as $employe) : ?>
                        <tr>
                            <td class="td-name"><?= esc(trim(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? ''))) ?></td>
                            <td class="td-muted"><?= esc($employe['email'] ?? '-') ?></td>
                            <td class="td-muted"><?= esc($employe['departement_nom'] ?? '-') ?></td>
                            <td><span class="type-badge t-annuel"><?= esc($employe['role'] ?? '-') ?></span></td>
                            <td><span class="statut <?= (int) ($employe['actif'] ?? 0) === 1 ? 's-approuvee' : 's-refusee' ?>"><?= (int) ($employe['actif'] ?? 0) === 1 ? 'Actif' : 'Inactif' ?></span></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5" class="td-muted">Aucun employé trouvé.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>
