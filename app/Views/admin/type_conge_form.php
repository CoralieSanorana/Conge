<?php
$pageTitle = ($isEdit ?? false) ? 'Modifier un type de congé' : 'Ajouter un type de congé';
$admin = $admin ?? [];
$typeConge = $typeConge ?? [];
$isEdit = $isEdit ?? !empty($typeConge['id']);
$formAction = $formAction ?? ($isEdit ? site_url('admin/type-conge/update/' . ($typeConge['id'] ?? '')) : site_url('admin/type-conge/submit'));
$submitLabel = $submitLabel ?? ($isEdit ? 'Mettre à jour' : 'Soumettre');
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
                <div class="topbar-title"><?= esc($pageTitle) ?></div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('admin/dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> <a href="<?= site_url('admin/type-conges') ?>">Types congé</a><?= $isEdit ? ' <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Modifier' : '' ?></div>
            </div>
            <div class="topbar-actions">
                <a href="<?= site_url('admin/type-conges') ?>" class="btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
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
                    <h3><i class="bi bi-calendar2-week" style="color:var(--forest);margin-right:6px"></i><?= $isEdit ? 'Modifier un type de congé' : 'Ajouter un type de congé' ?></h3>
                    <div class="form-grid-2" style="margin-bottom:1rem">
                        <div class="f-group">
                            <label class="f-label">Libellé</label>
                            <input type="text" name="libelle" class="f-input" placeholder="Ex: Congé annuel" value="<?= esc(old('libelle', $typeConge['libelle'] ?? '')) ?>"/>
                        </div>
                        <div class="f-group">
                            <label class="f-label">Jours annuels</label>
                            <input type="number" name="jours_annuels" class="f-input" min="0" value="<?= esc(old('jours_annuels', $typeConge['jours_annuels'] ?? '')) ?>"/>
                        </div>
                        <div class="f-group">
                            <label class="f-label">Déductible</label>
                            <select class="f-select" name="deductible">
                                <option value="1" <?= (string) old('deductible', (string) ($typeConge['deductible'] ?? 1)) === '1' ? 'selected' : '' ?>>Oui</option>
                                <option value="0" <?= (string) old('deductible', (string) ($typeConge['deductible'] ?? 1)) === '0' ? 'selected' : '' ?>>Non</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="btn-forest" type="submit"><i class="bi bi-send"></i> <?= esc($submitLabel) ?></button>
                        <a href="<?= site_url('admin/type-conges') ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>