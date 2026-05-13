<?php
$pageTitle = 'Mon profil';
$employe = $employe ?? [];
$departement = $departement ?? [];
$soldes = $soldes ?? [];
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('employe/dashboard')],
    ['label' => 'Nouvelle demande', 'icon' => 'bi-plus-circle', 'url' => site_url('employe/conge/demande')],
    ['label' => 'Mes demandes', 'icon' => 'bi-calendar3', 'url' => site_url('employe/conge/historique')],
    ['label' => 'Mon profil', 'icon' => 'bi-person', 'url' => site_url('employe/profile'), 'active' => true],
];
$sidebarUser = [
    'name' => trim(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? '')) ?: 'Employé',
    'role' => $employe['role'] ?? 'EMPLOYE',
    'department' => $departement['nom'] ?? 'Aucun département',
    'avatarClass' => 'av-green',
    'initials' => strtoupper(substr($employe['prenom'] ?? 'E', 0, 1) . substr($employe['nom'] ?? 'M', 0, 1)),
];
?>
<?= $this->include('Layouts/header') ?>
<div class="app-wrap">
    <?= $this->include('Layouts/sidebar') ?>
    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Mon profil</div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('employe/dashboard') ?>">Accueil</a> / Profil</div>
            </div>
        </div>
        <div class="content">
            <div class="form-section">
                <div class="profile-row">
                    <div class="avatar av-green"><?= esc(strtoupper(substr($employe['prenom'] ?? 'E', 0, 1) . substr($employe['nom'] ?? 'M', 0, 1))) ?></div>
                    <div class="profile-info">
                        <div class="pname"><?= esc(trim(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? ''))) ?></div>
                        <div class="pdept"><?= esc($employe['email'] ?? '-') ?> · <?= esc($departement['nom'] ?? '-') ?></div>
                    </div>
                </div>
                <div class="inline-stats">
                    <div class="inline-stat">Rôle: <strong><?= esc($employe['role'] ?? '-') ?></strong></div>
                    <div class="inline-stat">Date d'embauche: <strong><?= !empty($employe['date_embauche']) ? esc(date('d/m/Y', strtotime($employe['date_embauche']))) : '-' ?></strong></div>
                    <div class="inline-stat">Statut: <strong><?= (int) ($employe['actif'] ?? 0) === 1 ? 'Actif' : 'Inactif' ?></strong></div>
                </div>
            </div>

            <div class="data-card">
                <div class="data-card-head"><h3>Soldes disponibles</h3></div>
                <table class="tbl">
                    <thead><tr><th>Type</th><th>Attribués</th><th>Pris</th><th>Restants</th></tr></thead>
                    <tbody>
                    <?php if (!empty($soldes)) : foreach ($soldes as $solde) : ?>
                        <tr>
                            <td><span class="type-badge t-annuel"><?= esc($solde['libelle'] ?? '-') ?></span></td>
                            <td class="td-mono"><?= esc((int) ($solde['jours_attribues'] ?? 0)) ?> j</td>
                            <td class="td-mono"><?= esc((int) ($solde['jours_pris'] ?? 0)) ?> j</td>
                            <td class="td-mono"><?= esc((int) ($solde['restant'] ?? 0)) ?> j</td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="4" class="td-muted">Aucun solde disponible.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>
