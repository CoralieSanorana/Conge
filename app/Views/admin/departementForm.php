<?php
$pageTitle = ($isEdit ?? false) ? 'Modifier un département' : 'Ajouter un département';
$admin = $admin ?? [];
$departement = $departement ?? [];
$isEdit = $isEdit ?? !empty($departement['id']);
$formAction = $formAction ?? ($isEdit ? site_url('admin/departement/update/' . ($departement['id'] ?? '')) : site_url('admin/departement/submit'));
$submitLabel = $submitLabel ?? ($isEdit ? 'Mettre à jour' : 'Soumettre');
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
                <div class="topbar-title"><?= esc($pageTitle) ?></div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('admin/dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> <a href="<?= site_url('admin/departements') ?>">Départements</a><?= $isEdit ? ' <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Modifier' : '' ?></div>
            </div>
            <div class="topbar-actions">
                <a href="<?= site_url('admin/departements') ?>" class="btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
            </div>
        </div>
        <div class="content">
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <form action="<?= esc($formAction) ?>" method="post">
                <div class="form-section">
                    <h3><i class="bi bi-building" style="color:var(--forest);margin-right:6px"></i><?= $isEdit ? 'Modifier un département' : 'Ajouter un département' ?></h3>
                    <div class="form-grid-2" style="margin-bottom:1rem">
                        <div class="f-group">
                            <label class="f-label">Nom du département</label>
                            <input type="text" name="nom" class="f-input" placeholder="Ex: Informatique" value="<?= esc(old('nom', $departement['nom'] ?? '')) ?>"/>
                        </div>
                        <div class="f-group">
                            <label class="f-label">Description</label>
                            <input type="text" name="description" class="f-input" placeholder="Ex: Équipe technique" value="<?= esc(old('description', $departement['description'] ?? '')) ?>"/>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="btn-forest" type="submit"><i class="bi bi-send"></i> <?= esc($submitLabel) ?></button>
                        <a href="<?= site_url('admin/departements') ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>