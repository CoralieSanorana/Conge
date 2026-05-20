<?php
$pageTitle = 'Gestion des types de congé';
$admin = $admin ?? [];
$typesConge = $typesConge ?? [];
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('admin/dashboard')],
    ['label' => 'Employés', 'icon' => 'bi-people', 'url' => site_url('admin/employes')],
    ['label' => 'Départements', 'icon' => 'bi-building', 'url' => site_url('admin/departements')],
    ['label' => 'Types congé', 'icon' => 'bi-calendar2-week', 'url' => site_url('admin/type-conges'), 'active' => true],
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
                <div class="topbar-title">Gestion des types de congé</div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('admin/dashboard') ?>">Accueil</a> / Types congé</div>
            </div>
            <div class="topbar-actions">
                <a href="<?= site_url('admin/type-conge/form') ?>" class="btn-forest"><i class="bi bi-calendar2-week"></i> Ajouter Type conge</a>
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
                <div class="data-card-head"><h3>Liste des types de congé</h3></div>
                <table class="tbl">
                    <thead><tr><th>Libellé</th><th>Jours annuels</th><th>Déductible</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php if (!empty($typesConge)) : foreach ($typesConge as $typeConge) : ?>
                        <tr>
                            <td class="td-name"><?= esc($typeConge['libelle'] ?? '-') ?></td>
                            <td class="td-muted"><?= esc((int) ($typeConge['jours_annuels'] ?? 0)) ?></td>
                            <td class="td-muted"><?= ((int) ($typeConge['deductible'] ?? 0) === 1) ? 'Oui' : 'Non' ?></td>
                            <td>
                                <div class="action-btns">
                                    <a class="btn-sm btn-edit" href="<?= site_url('admin/type-conge/edit/' . $typeConge['id']) ?>"><i class="bi bi-pencil"></i> Modifier</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="4" class="td-muted">Aucun type de congé trouvé.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>