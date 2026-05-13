<?php
$pageTitle = 'Validation des congés';
$rh = $rh ?? [];
$conges = $conges ?? [];
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('rh/dashboard')],
    ['label' => 'Validation', 'icon' => 'bi-check2-square', 'url' => site_url('rh/conge/approbation'), 'active' => true],
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
                <div class="topbar-title">Validation des demandes</div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('rh/dashboard') ?>">Accueil</a> / Approbation</div>
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
                <div class="data-card-head"><h3>Demandes en attente</h3></div>
                <table class="tbl">
                    <thead><tr><th>Employé</th><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Motif</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php if (!empty($conges)) : foreach ($conges as $conge) : ?>
                        <tr>
                            <td class="td-name"><?= esc(trim(($conge['prenom'] ?? '') . ' ' . ($conge['nom'] ?? ''))) ?></td>
                            <td><span class="type-badge t-annuel"><?= esc($conge['type_conge_libelle'] ?? '-') ?></span></td>
                            <td class="td-muted"><?= !empty($conge['date_debut']) ? esc(date('d/m/Y', strtotime($conge['date_debut']))) : '-' ?></td>
                            <td class="td-muted"><?= !empty($conge['date_fin']) ? esc(date('d/m/Y', strtotime($conge['date_fin']))) : '-' ?></td>
                            <td class="td-mono"><?= esc((int) ($conge['nb_jours'] ?? 0)) ?> j</td>
                            <td class="td-muted"><?= esc($conge['motif'] ?? '-') ?></td>
                            <td>
                                <form action="<?= site_url('rh/conge/approbation') ?>" method="post" class="action-btns">
                                    <input type="hidden" name="conge_id" value="<?= esc($conge['id']) ?>">
                                    <button class="btn-sm btn-approve" type="submit" name="action" value="approve"><i class="bi bi-check2"></i> Approuver</button>
                                    <button class="btn-sm btn-refuse" type="submit" name="action" value="refuse"><i class="bi bi-x"></i> Refuser</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="7" class="td-muted">Aucune demande en attente.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>
