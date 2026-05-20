<?php
$pageTitle = 'Formulaire';
$employe = $employe ?? [];
$departement = $departement ?? null;
$typesConge = $typesConge ?? [];
$soldes = $soldes ?? [];
$departements = $departements ?? [];
$isEdit = $isEdit ?? !empty($employe['id']);
$formAction = $formAction ?? ($isEdit ? site_url('admin/employe/update/' . ($employe['id'] ?? '')) : site_url('admin/employe/submit'));
$submitLabel = $submitLabel ?? ($isEdit ? 'Mettre à jour l\'employé' : 'Créer l\'employé');
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('admin/dashboard')],
    ['label' => 'Employés', 'icon' => 'bi-people', 'url' => site_url('admin/employes')],
    ['label' => 'Congés', 'icon' => 'bi-calendar2-week', 'url' => site_url('admin/conges')],
];
$sidebarUser = [
    'name' => 'Administrateur',
    'role' => 'ADMIN',
    'department' => 'Direction',
    'avatarClass' => 'av-amber',
    'initials' => strtoupper(substr($employe['prenom'] ?? 'E', 0, 1) . substr($employe['nom'] ?? 'M', 0, 1)),
];
?>
<?= $this->include('Layouts/header') ?>
<div class="app-wrap">
    <?= $this->include('Layouts/sidebar') ?>

    <div class="main">
        <div class="topbar">
        <div>
            <div class="topbar-title"><?= $isEdit ? 'Modifier un employé' : 'Gestion des employés' ?></div>
            <div class="topbar-breadcrumb"><a href="<?= site_url('admin/dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> <a href="<?= site_url('admin/employes') ?>">Employés</a><?= $isEdit ? ' <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Modifier' : '' ?></div>
        </div>
        <div class="topbar-actions">
            <a href="<?= site_url('admin/employe/form') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter Employé</a>
        </div>
        </div>

        <div class="content">
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="flash flash-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="flash flash-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <!-- Formulaire ajout -->
            <form action="<?= esc($formAction) ?>" method="post">
                <div class="form-section">
                    <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i><?= $isEdit ? 'Modifier un employé' : 'Ajouter un employé' ?></h3>
                    <div class="form-grid-2" style="margin-bottom:1rem">
                    <div class="f-group">
                        <label class="f-label">Prénom</label>
                        <input type="text" name="prenom" class="f-input" placeholder="Jean" value="<?= esc(old('prenom', $employe['prenom'] ?? '')) ?>"/>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Nom</label>
                        <input type="text" name="nom" class="f-input" placeholder="Rakoto" value="<?= esc(old('nom', $employe['nom'] ?? '')) ?>"/>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Email</label>
                        <input type="email" name="email" class="f-input" placeholder="jean.rakoto@techmada.mg" value="<?= esc(old('email', $employe['email'] ?? '')) ?>"/>
                    </div>
                    <div class="f-group">
                        <label class="f-label"><?= $isEdit ? 'Nouveau mot de passe' : 'Mot de passe initial' ?></label>
                        <input type="password" name="password" class="f-input" placeholder="<?= $isEdit ? 'Laisser vide pour conserver le mot de passe actuel' : 'À communiquer à l\'employé' ?>"/>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Département</label>
                        <select class="f-select" name="departement_id">
                            <?php foreach ($departements as $dept) : ?>
                                <option value="<?= $dept['id'] ?>" <?= (string) old('departement_id', $employe['departement_id'] ?? '') === (string) $dept['id'] ? 'selected' : '' ?>><?= esc($dept['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Rôle</label>
                        <select class="f-select" name="role">
                        <option value="employe" <?= old('role', strtolower($employe['role'] ?? 'employe')) === 'employe' ? 'selected' : '' ?>>Employé</option>
                        <option value="rh" <?= old('role', strtolower($employe['role'] ?? 'employe')) === 'rh' ? 'selected' : '' ?>>Responsable RH</option>
                        <option value="admin" <?= old('role', strtolower($employe['role'] ?? 'employe')) === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Date d'embauche</label>
                        <input type="date" name="date_embauche" class="f-input" value="<?= esc(old('date_embauche', $employe['date_embauche'] ?? date('Y-m-d'))) ?>"/>
                    </div>
                    </div>
                    <div class="flash flash-info" style="margin-bottom:1rem">
                    <i class="bi bi-info-circle-fill"></i>
                    <span style="font-size:.82rem">Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span>
                    </div>
                    <div class="form-actions">
                    <button class="btn-forest" type="submit"><i class="bi bi-plus"></i> <?= esc($submitLabel) ?></button>
                    <button class="btn-secondary" type="reset">Réinitialiser</button>
                    </div>
                </div>
            </form>

        </div>
        <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
    </div>

    </div>
</body>
</html>