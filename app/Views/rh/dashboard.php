<?php
$pageTitle = 'Tableau de bord RH';
$rh = $rh ?? [];
$pendingConges = $pendingConges ?? [];
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('rh/dashboard'), 'active' => true],
    ['label' => 'Validation', 'icon' => 'bi-check2-square', 'url' => site_url('rh/conge/approbation')],
];
$sidebarUser = [
    'name' => trim(($rh['prenom'] ?? 'RH') . ' ' . ($rh['nom'] ?? '')) ?: 'Responsable RH',
    'role' => $rh['role'] ?? 'RH',
    'department' => $rh['departement_nom'] ?? 'Ressources Humaines',
    'avatarClass' => 'av-blue',
    'initials' => strtoupper(substr($rh['prenom'] ?? 'R', 0, 1) . substr($rh['nom'] ?? 'H', 0, 1)),
];
?>
<?= $this->include('Layouts/header') ?>
<div class="app-wrap">
    <?= $this->include('Layouts/sidebar') ?>
    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Tableau de bord RH</div>
                <div class="topbar-breadcrumb">Accueil / Validation des congés</div>
            </div>
            <div class="topbar-actions">
                <a href="<?= site_url('rh/conge/approbation') ?>" class="btn-forest"><i class="bi bi-check2-square"></i> Ouvrir les validations</a>
            </div>
        </div>
        <div class="content">
            <div class="metrics">
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div><div class="metric-val"><?= esc($pendingCount ?? 0) ?></div><div class="metric-label">En attente</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div><div class="metric-val"><?= esc($approvedCount ?? 0) ?></div><div class="metric-label">Approuvées</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div><div class="metric-val"><?= esc($rejectedCount ?? 0) ?></div><div class="metric-label">Refusées</div></div>
            </div>

            <div class="data-card">
                <div class="data-card-head"><h3>Demandes à traiter</h3><a href="<?= site_url('rh/conge/approbation') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a></div>
                <table class="tbl">
                    <thead><tr><th>Employé</th><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Motif</th></tr></thead>
                    <tbody>
                    <?php if (!empty($pendingConges)) : foreach ($pendingConges as $conge) : ?>
                        <tr>
                            <td class="td-name"><?= esc(trim(($conge['prenom'] ?? '') . ' ' . ($conge['nom'] ?? ''))) ?></td>
                            <td><span class="type-badge t-annuel"><?= esc($conge['type_conge_libelle'] ?? '-') ?></span></td>
                            <td class="td-muted"><?= !empty($conge['date_debut']) ? esc(date('d/m/Y', strtotime($conge['date_debut']))) : '-' ?></td>
                            <td class="td-muted"><?= !empty($conge['date_fin']) ? esc(date('d/m/Y', strtotime($conge['date_fin']))) : '-' ?></td>
                            <td class="td-mono"><?= esc((int) ($conge['nb_jours'] ?? 0)) ?> j</td>
                            <td class="td-muted"><?= esc($conge['motif'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="6" class="td-muted">Aucune demande en attente.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>
