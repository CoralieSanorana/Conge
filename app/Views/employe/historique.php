<?php
$pageTitle = 'Historique des demandes';
$employe = $employe ?? [];
$departement = $departement ?? [];
$conges = $conges ?? [];
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('employe/dashboard')],
    ['label' => 'Nouvelle demande', 'icon' => 'bi-plus-circle', 'url' => site_url('employe/conge/demande')],
    ['label' => 'Mes demandes', 'icon' => 'bi-calendar3', 'url' => site_url('employe/conge/historique'), 'active' => true],
    ['label' => 'Mon profil', 'icon' => 'bi-person', 'url' => site_url('employe/profile')],
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
                <div class="topbar-title">Mes demandes</div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('employe/dashboard') ?>">Accueil</a> / Historique</div>
            </div>
        </div>
        <div class="content">
            <div class="data-card">
                <div class="data-card-head"><h3>Demandes enregistrées</h3></div>
                <table class="tbl">
                    <thead><tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Motif</th><th>Statut</th></tr></thead>
                    <tbody>
                    <?php if (!empty($conges)) : foreach ($conges as $conge) : ?>
                        <tr>
                            <td><span class="type-badge t-annuel"><?= esc($conge['type_conge_libelle'] ?? '-') ?></span></td>
                            <td class="td-muted"><?= !empty($conge['date_debut']) ? esc(date('d/m/Y', strtotime($conge['date_debut']))) : '-' ?></td>
                            <td class="td-muted"><?= !empty($conge['date_fin']) ? esc(date('d/m/Y', strtotime($conge['date_fin']))) : '-' ?></td>
                            <td class="td-mono"><?= esc((int) ($conge['nb_jours'] ?? 0)) ?> j</td>
                            <td class="td-muted"><?= esc($conge['motif'] ?? '-') ?></td>
                            <td><span class="statut <?= strtolower($conge['statut'] ?? '') === 'approuvé' ? 's-approuvee' : (strtolower($conge['statut'] ?? '') === 'refusé' ? 's-refusee' : 's-attente') ?>"><?= esc($conge['statut'] ?? 'En attente') ?></span></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="td-muted">Aucune demande trouvée.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>
