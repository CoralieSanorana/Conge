<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord employé</title>
</head>
<body>
    <?php
    $employe = $employe ?? [];
    $departement = $departement ?? null;
    $congesAttente = $congesAttente ?? [];
    $congesAcceptes = $congesAcceptes ?? [];
    $congesRefuses = $congesRefuses ?? [];
    $congesRecents = $congesRecents ?? [];
    $soldes = $soldes ?? [];
    $soldeResume = $soldeResume ?? ['total_attribues' => 0, 'total_pris' => 0, 'total_restant' => 0];
    $annee = $annee ?? (int) date('Y');
    $successMessage = session()->getFlashdata('success');
    $errorMessage = session()->getFlashdata('error');

    $initiales = strtoupper(substr($employe['prenom'] ?? 'E', 0, 1) . substr($employe['nom'] ?? 'M', 0, 1));
    $nomComplet = trim(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? '')) ?: 'Employé';
    $departementNom = $departement['nom'] ?? 'Aucun département';
    $role = $employe['role'] ?? 'EMPLOYE';
    $totalDemandes = count($congesAttente) + count($congesAcceptes) + count($congesRefuses);
    $joursRestants = (int) ($soldeResume['total_restant'] ?? 0);
    $joursAttribues = (int) ($soldeResume['total_attribues'] ?? 0);
    ?>
    <div class="app-wrap">
    <aside class="sidebar">
        <div class="sidebar-brand">
        <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
        <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
        </div>
        <div class="sidebar-section">Menu</div>
        <ul class="sidebar-nav">
        <li><a href="#page-dashboard-employe" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
        <li><a href="#page-form-conge"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
        <li>
            <a href="#page-mes-conges">
            <i class="bi bi-calendar3"></i> Mes demandes
            <span class="nav-badge alert"><?= esc($totalDemandes) ?></span>
            </a>
        </li>
        <li><a href="#page-profil-employe"><i class="bi bi-person"></i> Mon profil</a></li>
        </ul>
        <div class="sidebar-user">
        <div class="s-user-row">
            <div class="avatar av-green"><?= esc($initiales) ?></div>
            <div>
            <div class="user-name"><?= esc($nomComplet) ?></div>
            <div class="user-role"><?= esc($role) ?> · <?= esc($departementNom) ?></div>
            </div>
            <a href="<?= site_url('employe/logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
        </div>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
        <div>
            <div class="topbar-title">Tableau de bord</div>
            <div class="topbar-breadcrumb">Accueil / <?= esc($nomComplet) ?></div>
        </div>
        <div class="topbar-actions">
            <a href="#page-form-conge" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
            <i class="bi bi-plus-lg"></i> Nouvelle demande
            </a>
        </div>
        </div>

        <div class="content">

        <?php if ($successMessage): ?>
        <div class="flash flash-success">
            <i class="bi bi-check-circle-fill"></i>
            <?= esc($successMessage) ?>
        </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
        <div class="flash flash-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= esc($errorMessage) ?>
        </div>
        <?php endif; ?>

        <div class="metrics">
            <div class="metric">
            <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
            <div class="metric-val"><?= esc(count($congesAttente)) ?></div>
            <div class="metric-label">En attente</div>
            </div>
            <div class="metric">
            <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
            <div class="metric-val"><?= esc(count($congesAcceptes)) ?></div>
            <div class="metric-label">Approuvées</div>
            </div>
            <div class="metric">
            <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
            <div class="metric-val"><?= esc($joursRestants) ?></div>
            <div class="metric-label">Jours restants</div>
            <div class="metric-sub">sur <?= esc($joursAttribues) ?> cette année</div>
            </div>
            <div class="metric">
            <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
            <div class="metric-val"><?= esc(count($congesRefuses)) ?></div>
            <div class="metric-label">Refusée</div>
            </div>
        </div>

        <div class="data-card">
            <div class="data-card-head"><h3>Mes soldes de congés — <?= esc($annee) ?></h3></div>
            <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
            <?php if (!empty($soldes)): ?>
                <?php foreach ($soldes as $solde): ?>
                <?php
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
                <?php endforeach; ?>
            <?php else: ?>
                <div class="solde-card" style="margin:0;grid-column:1/-1">
                    <div class="solde-label">Aucun solde disponible pour cette année.</div>
                </div>
            <?php endif; ?>
            </div>
        </div>

        <div class="data-card">
            <div class="data-card-head">
            <h3>Mes dernières demandes</h3>
            <a href="#page-mes-conges" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
            </div>
            <table class="tbl">
            <thead>
                <tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($congesRecents)): ?>
                    <?php foreach ($congesRecents as $conge): ?>
                    <?php
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
                    <td><span class="statut <?= esc($classeStatut) ?>"><?= esc(strtolower($statut)) ?></span></td>
                    <td>
                        <?php if ($statut === 'En attente'): ?>
                        <a class="btn-sm btn-cancel" href="#page-mes-conges"><i class="bi bi-x"></i> Annuler</a>
                        <?php else: ?>
                        <span class="td-muted" style="font-size:.75rem">—</span>
                        <?php endif; ?>
                    </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="td-muted">Aucune demande trouvée.</td>
                </tr>
                <?php endif; ?>
            </tbody>
            </table>
        </div>

        </div>
        <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc(date('Y')) ?> <span>TechMada RH</span> — Projet CodeIgniter 4</div>
    </div>

    </div>
</body>
</html>