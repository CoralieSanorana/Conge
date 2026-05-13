<?php
$pageTitle = 'Nouvelle demande de congé';
$employe = $employe ?? [];
$departement = $departement ?? null;
$typesConge = $typesConge ?? [];
$soldes = $soldes ?? [];
$sidebarItems = [
    ['label' => 'Tableau de bord', 'icon' => 'bi-grid-1x2', 'url' => site_url('employe/dashboard')],
    ['label' => 'Nouvelle demande', 'icon' => 'bi-plus-circle', 'url' => site_url('employe/conge/demande'), 'active' => true],
    ['label' => 'Mes demandes', 'icon' => 'bi-calendar3', 'url' => site_url('employe/conge/historique')],
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
                <div class="topbar-title">Nouvelle demande de congé</div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('employe/dashboard') ?>">Accueil</a> / Nouvelle demande</div>
            </div>
        </div>

        <div class="content">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">
                <div>
                    <form action="<?= base_url('employe/conge/submit') ?>" method="post">
                        <div class="form-section">
                            <h3>Détails de la demande</h3>

                            <div class="f-group" style="margin-bottom:1rem">
                                <label class="f-label">Type de congé <span style="color:var(--danger)">*</span></label>
                                <select name="type_conge_id" id="type_conge_id" class="f-select">
                                    <option value="">-- Choisir un type --</option>
                                    <?php foreach ($typesConge as $type) : ?>
                                        <option value="<?= esc($type['id']) ?>"><?= esc($type['libelle'] ?? $type['nom'] ?? 'Type') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-grid-2" style="margin-bottom:1rem">
                                <div class="f-group">
                                    <label class="f-label">Date de début <span style="color:var(--danger)">*</span></label>
                                    <input type="date" name="date_debut" class="f-input"/>
                                </div>
                                <div class="f-group">
                                    <label class="f-label">Date de fin <span style="color:var(--danger)">*</span></label>
                                    <input type="date" name="date_fin" class="f-input"/>
                                </div>
                            </div>

                            <div class="f-group" style="margin-bottom:1rem">
                                <label class="f-label">Motif (optionnel)</label>
                                <textarea name="motif" class="f-textarea" placeholder="Précisez le motif de votre demande si nécessaire..."></textarea>
                                <div class="f-hint">Le motif est visible par le responsable RH.</div>
                            </div>

                            <div class="form-actions">
                                <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button>
                                <a href="<?= site_url('employe/dashboard') ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>

                <div style="display:flex;flex-direction:column;gap:1rem">
                    <div class="data-card" style="margin:0">
                        <div class="data-card-head"><h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels</h3></div>
                        <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
                            <?php if (!empty($soldes)) : foreach ($soldes as $solde) : ?>
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                                    <span style="font-size:.8rem;color:var(--ink)"><?= esc($solde['libelle'] ?? '-') ?></span>
                                    <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= esc((int) ($solde['restant'] ?? 0)) ?> j</span>
                                </div>
                                <?php $attribues = (int) ($solde['jours_attribues'] ?? 0); $restant = (int) ($solde['restant'] ?? 0); $width = $attribues > 0 ? (int) round(($restant / $attribues) * 100) : 0; ?>
                                <div class="solde-bar"><div class="solde-fill" style="width:<?= esc(min(100, max(0, $width))) ?>%"></div></div>
                            </div>
                            <?php endforeach; else : ?>
                            <div class="td-muted">Aucun solde disponible.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flash flash-info" style="margin:0">
                        <i class="bi bi-info-circle-fill"></i>
                        <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre responsable.</span>
                    </div>
                    <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
                        <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem"><i class="bi bi-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des règles</div>
                        <ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
                            <li>Préavis minimum : 48h avant la date de début</li>
                            <li>Pas de chevauchement avec une demande en cours</li>
                            <li>Solde insuffisant = demande refusée automatiquement</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Layouts/footer') ?>