<?php
$pageTitle = 'Gestion des congés';
$admin = $admin ?? [];
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('admin/dashboard')],
    ['label' => 'Employés', 'icon' => 'bi-people', 'url' => site_url('admin/employes')],
    ['label' => 'Congés', 'icon' => 'bi-calendar2-week', 'url' => site_url('admin/conges'), 'active' => true],
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
                <div class="topbar-title">Gestion des congés</div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('admin/dashboard') ?>">Accueil</a> / Congés</div>
            </div>
        </div>
        <div class="content">
            <div class="data-card">
                <div class="data-card-head"><h3>Historique global</h3></div>
                <table class="tbl">
                    <thead><tr><th>Employé</th><th>Type</th><th>Département</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th></tr></thead>
                    <tbody>
                    <?php if (!empty($conges)) : foreach ($conges as $conge) : ?>
                        <tr>
                            <td class="td-name"><?= esc(trim(($conge['prenom'] ?? '') . ' ' . ($conge['nom'] ?? ''))) ?></td>
                            <td><span class="type-badge t-annuel"><?= esc($conge['type_conge_libelle'] ?? '-') ?></span></td>
                            <td class="td-muted"><?= esc($conge['departement_nom'] ?? '-') ?></td>
                            <td class="td-muted"><?= !empty($conge['date_debut']) ? esc(date('d/m/Y', strtotime($conge['date_debut']))) : '-' ?></td>
                            <td class="td-muted"><?= !empty($conge['date_fin']) ? esc(date('d/m/Y', strtotime($conge['date_fin']))) : '-' ?></td>
                            <td class="td-mono"><?= esc((int) ($conge['nb_jours'] ?? 0)) ?> j</td>
                            <td><span class="statut <?= strtolower($conge['statut'] ?? '') === 'approuvé' ? 's-approuvee' : (strtolower($conge['statut'] ?? '') === 'refusé' ? 's-refusee' : 's-attente') ?>"><?= esc($conge['statut'] ?? 'En attente') ?></span></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="7" class="td-muted">Aucune demande trouvée.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>
