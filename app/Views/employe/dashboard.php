<?php
$pageTitle = 'Tableau de bord employé';
$employe = $employe ?? [];
$departement = $departement ?? null;
$congesAttente = $congesAttente ?? [];
$congesAcceptes = $congesAcceptes ?? [];
$congesRefuses = $congesRefuses ?? [];
$congesRecents = $congesRecents ?? [];
$soldes = $soldes ?? [];
$soldeResume = $soldeResume ?? ['total_attribues' => 0, 'total_pris' => 0, 'total_restant' => 0];
$annee = $annee ?? (int) date('Y');
$nomComplet = trim(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? '')) ?: 'Employé';
$departementNom = $departement['nom'] ?? 'Aucun département';
$totalDemandes = count($congesAttente) + count($congesAcceptes) + count($congesRefuses);
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('employe/dashboard'), 'active' => true],
    ['label' => 'Nouvelle demande', 'icon' => 'bi-plus-circle', 'url' => site_url('employe/conge/demande')],
    ['label' => 'Mes demandes', 'icon' => 'bi-calendar3', 'url' => site_url('employe/conge/historique'), 'badge' => $totalDemandes, 'badgeClass' => 'alert'],
    ['label' => 'Mon profil', 'icon' => 'bi-person', 'url' => site_url('employe/profile')],
];
$sidebarUser = [
    'name' => $nomComplet,
    'role' => $employe['role'] ?? 'EMPLOYE',
    'department' => $departementNom,
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
                <div class="topbar-title">Tableau de bord</div>
                <div class="topbar-breadcrumb">Accueil / <?= esc($nomComplet) ?></div>
            </div>
            <div class="topbar-actions">
                <a href="<?= site_url('employe/conge/demande') ?>" class="btn-forest"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
            </div>
        </div>

        <div class="content">
            <?php if (session()->getFlashdata('success')) : ?>
            <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
            <div class="flash flash-error"><i class="bi bi-exclamation-triangle-fill"></i> <?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="metrics">
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div><div class="metric-val"><?= esc(count($congesAttente)) ?></div><div class="metric-label">En attente</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div><div class="metric-val"><?= esc(count($congesAcceptes)) ?></div><div class="metric-label">Approuvées</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div><div class="metric-val"><?= esc((int) ($soldeResume['total_restant'] ?? 0)) ?></div><div class="metric-label">Jours restants</div><div class="metric-sub">sur <?= esc((int) ($soldeResume['total_attribues'] ?? 0)) ?> cette année</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div><div class="metric-val"><?= esc(count($congesRefuses)) ?></div><div class="metric-label">Refusées</div></div>
            </div>

            <div class="data-card">
                <div class="data-card-head"><h3>Mes soldes de congés — <?= esc($annee) ?></h3></div>
                <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
                <?php if (!empty($soldes)) : foreach ($soldes as $solde) :
                    $attribues = (int) ($solde['jours_attribues'] ?? 0);
                    $pris = (int) ($solde['jours_pris'] ?? 0);
                    $restant = (int) ($solde['restant'] ?? 0);
                    $pourcentage = $attribues > 0 ? min(100, max(0, (int) round(($restant / $attribues) * 100))) : 0;
                    $barreWarn = $pourcentage <= 20 ? ' warn' : '';
                ?>
                    <div class="solde-card" style="margin:0">
                        <div class="solde-header">
                            <span class="solde-type"><?= esc($solde['libelle'] ?? 'Type de congé') ?></span>
                            <span class="solde-nums"><strong><?= esc($restant) ?></strong> / <?= esc($attribues) ?> j</span>
                        </div>
                        <div class="solde-bar"><div class="solde-fill<?= esc($barreWarn) ?>" style="width:<?= esc($pourcentage) ?>%"></div></div>
                        <div class="solde-label"><?= esc($restant) ?> jour(s) restant(s) · <?= esc($pris) ?> pris</div>
                    </div>
                <?php endforeach; else : ?>
                    <div class="solde-card" style="margin:0;grid-column:1/-1"><div class="solde-label">Aucun solde disponible pour cette année.</div></div>
                <?php endif; ?>
                </div>
            </div>

            <div class="data-card">
                <div class="data-card-head">
                    <h3>Mes dernières demandes</h3>
                    <a href="<?= site_url('employe/conge/historique') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
                </div>
                <table class="tbl">
                    <thead><tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if (!empty($congesRecents)) : foreach ($congesRecents as $conge) :
                            $statut = $conge['statut'] ?? 'En attente';
                            $classeStatut = 's-attente';
                            if ($statut === 'Approuvé') {
                                $classeStatut = 's-approuvee';
                            } elseif ($statut === 'Refusé') {
                                $classeStatut = 's-refusee';
                            } elseif ($statut === 'Annulé') {
                                $classeStatut = 's-annulee';
                            }
                            $type = $conge['type_conge_libelle'] ?? $conge['libelle'] ?? 'Congé';
                            $jours = (int) ($conge['nb_jours'] ?? 0);
                            $dateDebut = !empty($conge['date_debut']) ? date('d M Y', strtotime($conge['date_debut'])) : '-';
                            $dateFin = !empty($conge['date_fin']) ? date('d M Y', strtotime($conge['date_fin'])) : '-';
                        ?>
                        <tr>
                            <td><span class="type-badge t-annuel"><?= esc($type) ?></span></td>
                            <td class="td-muted"><?= esc($dateDebut) ?></td>
                            <td class="td-muted"><?= esc($dateFin) ?></td>
                            <td class="td-mono"><?= esc($jours) ?> j</td>
                            <td><span class="statut <?= esc($classeStatut) ?>"><?= esc($statut) ?></span></td>
                            <td>
                                <?php if ($statut === 'En attente') : ?>
                                <span class="td-muted" style="font-size:.75rem">En cours</span>
                                <?php else : ?>
                                <span class="td-muted" style="font-size:.75rem">—</span>
                                <?php endif; ?>
                            </td>
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