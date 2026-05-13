<?php
$pageTitle = 'Tableau de bord admin';
$admin = $admin ?? [];
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('admin/dashboard'), 'active' => true],
    ['label' => 'Employés', 'icon' => 'bi-people', 'url' => site_url('admin/employes')],
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
                <div class="topbar-title">Tableau de bord administrateur</div>
                <div class="topbar-breadcrumb">Accueil / Administration</div>
            </div>
            <div class="topbar-actions">
                <a href="<?= site_url('admin/employes') ?>" class="btn-forest"><i class="bi bi-people"></i> Gérer les employés</a>
            </div>
        </div>
        <div class="content">
            <div class="metrics">
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-people"></i></div></div><div class="metric-val"><?= esc($employeeCount ?? 0) ?></div><div class="metric-label">Employés</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-shield-check"></i></div></div><div class="metric-val"><?= esc($adminCount ?? 0) ?></div><div class="metric-label">Administrateurs</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-person-check"></i></div></div><div class="metric-val"><?= esc($rhCount ?? 0) ?></div><div class="metric-label">RH</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div><div class="metric-val"><?= esc($pendingCount ?? 0) ?></div><div class="metric-label">Congés en attente</div></div>
            </div>

            <div class="data-card">
                <div class="data-card-head"><h3>Derniers employés créés</h3><a href="<?= site_url('admin/employes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a></div>
                <table class="tbl">
                    <thead><tr><th>Employé</th><th>Département</th><th>Rôle</th><th>Statut</th></tr></thead>
                    <tbody>
                    <?php if (!empty($recentEmployes)) : foreach ($recentEmployes as $employe) : ?>
                        <tr>
                            <td class="td-name"><?= esc(trim(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? ''))) ?></td>
                            <td class="td-muted"><?= esc($employe['departement_nom'] ?? '-') ?></td>
                            <td><span class="type-badge t-annuel"><?= esc($employe['role'] ?? '-') ?></span></td>
                            <td><span class="statut <?= (int) ($employe['actif'] ?? 0) === 1 ? 's-approuvee' : 's-refusee' ?>"><?= (int) ($employe['actif'] ?? 0) === 1 ? 'Actif' : 'Inactif' ?></span></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="4" class="td-muted">Aucun employé trouvé.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="data-card">
                <div class="data-card-head"><h3>Derniers congés</h3><a href="<?= site_url('admin/conges') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a></div>
                <table class="tbl">
                    <thead><tr><th>Employé</th><th>Type</th><th>Du</th><th>Au</th><th>Statut</th></tr></thead>
                    <tbody>
                    <?php if (!empty($recentConges)) : foreach ($recentConges as $conge) : ?>
                        <tr>
                            <td class="td-name"><?= esc(trim(($conge['prenom'] ?? '') . ' ' . ($conge['nom'] ?? ''))) ?></td>
                            <td><span class="type-badge t-annuel"><?= esc($conge['type_conge_libelle'] ?? '-') ?></span></td>
                            <td class="td-muted"><?= !empty($conge['date_debut']) ? esc(date('d/m/Y', strtotime($conge['date_debut']))) : '-' ?></td>
                            <td class="td-muted"><?= !empty($conge['date_fin']) ? esc(date('d/m/Y', strtotime($conge['date_fin']))) : '-' ?></td>
                            <td><span class="statut <?= strtolower($conge['statut'] ?? '') === 'approuvé' ? 's-approuvee' : (strtolower($conge['statut'] ?? '') === 'refusé' ? 's-refusee' : 's-attente') ?>"><?= esc($conge['statut'] ?? 'En attente') ?></span></td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5" class="td-muted">Aucun congé trouvé.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>
