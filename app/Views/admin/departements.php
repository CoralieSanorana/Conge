<?php
$pageTitle = 'Gestion des départements';
$admin = $admin ?? [];
$departements = $departements ?? [];
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('admin/dashboard')],
    ['label' => 'Employés', 'icon' => 'bi-people', 'url' => site_url('admin/employes')],
    ['label' => 'Départements', 'icon' => 'bi-building', 'url' => site_url('admin/departements'), 'active' => true],
    ['label' => 'Types congé', 'icon' => 'bi-calendar2-week', 'url' => site_url('admin/type-conges')],
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
                <div class="topbar-title">Gestion des départements</div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('admin/dashboard') ?>">Accueil</a> / Départements</div>
            </div>
            <div class="topbar-actions">
                <a href="<?= site_url('admin/departement/form') ?>" class="btn-forest"><i class="bi bi-building"></i> Ajouter Departement</a>
            </div>
        </div>
        <div class="content">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <div class="data-card">
                <div class="data-card-head"><h3>Liste des départements</h3></div>
                <table class="tbl">
                    <thead><tr><th>Nom</th><th>Description</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php if (!empty($departements)) : foreach ($departements as $departement) : ?>
                        <tr>
                            <td class="td-name"><?= esc($departement['nom'] ?? '-') ?></td>
                            <td class="td-muted"><?= esc($departement['description'] ?? '-') ?></td>
                            <td>
                                <div class="action-btns">
                                    <a class="btn-sm btn-edit" href="<?= site_url('admin/departement/edit/' . $departement['id']) ?>"><i class="bi bi-pencil"></i> Modifier</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="3" class="td-muted">Aucun département trouvé.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>