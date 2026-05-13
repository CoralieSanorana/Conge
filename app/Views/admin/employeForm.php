<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <div class="app-wrap">

    <aside class="sidebar">
        <div class="sidebar-brand">
        <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
        <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
        </div>
        <ul class="sidebar-nav" style="margin-top:1rem">
        <li><a href="#page-dashboard-admin"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
        <li><a href="#page-liste-rh"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
        <li><a href="#page-admin-employes" class="active"><i class="bi bi-people"></i> Employés</a></li>
        <li><a href="#page-admin-employes"><i class="bi bi-building"></i> Départements</a></li>
        <li><a href="#page-admin-employes"><i class="bi bi-tags"></i> Types de congé</a></li>
        </ul>
        <div class="sidebar-user">
        <div class="s-user-row">
            <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem">AD</div>
            <div><div class="user-name">Administrateur</div><div class="user-role">Admin système</div></div>
        </div>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
        <div>
            <div class="topbar-title">Gestion des employés</div>
            <div class="topbar-breadcrumb"><a href="#page-dashboard-admin">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Employés</div>
        </div>
        <div class="topbar-actions">
            <a href="#" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter</a>
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
            <form action="<?= base_url('admin/employe/submit') ?>" method="post">
                <div class="form-section">
                    <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employé</h3>
                    <div class="form-grid-2" style="margin-bottom:1rem">
                    <div class="f-group">
                        <label class="f-label">Prénom</label>
                        <input type="text" name="prenom" class="f-input" placeholder="Jean" value="<?= esc(old('prenom', '')) ?>"/>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Nom</label>
                        <input type="text" name="nom" class="f-input" placeholder="Rakoto" value="<?= esc(old('nom', '')) ?>"/>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Email</label>
                        <input type="email" name="email" class="f-input" placeholder="jean.rakoto@techmada.mg" value="<?= esc(old('email', '')) ?>"/>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Mot de passe initial</label>
                        <input type="password" name="password" class="f-input" placeholder="À communiquer à l'employé"/>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Département</label>
                        <select class="f-select" name="departement_id">
                            <?php foreach ($departements as $dept) : ?>
                                <option value="<?= $dept['id'] ?>" <?= (string) old('departement_id') === (string) $dept['id'] ? 'selected' : '' ?>><?= esc($dept['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Rôle</label>
                        <select class="f-select" name="role">
                        <option value="employe" <?= old('role', 'employe') === 'employe' ? 'selected' : '' ?>>Employé</option>
                        <option value="rh" <?= old('role') === 'rh' ? 'selected' : '' ?>>Responsable RH</option>
                        <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Date d'embauche</label>
                        <input type="date" name="date_embauche" class="f-input" value="<?= esc(old('date_embauche', date('Y-m-d'))) ?>"/>
                    </div>
                    </div>
                    <div class="flash flash-info" style="margin-bottom:1rem">
                    <i class="bi bi-info-circle-fill"></i>
                    <span style="font-size:.82rem">Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span>
                    </div>
                    <div class="form-actions">
                    <button class="btn-forest" type="submit"><i class="bi bi-plus"></i> Créer l'employé</button>
                    <button class="btn-secondary" type="reset">Réinitialiser</button>
                    </div>
                </div>
            </form>
            <!-- Liste employés -->
            <div class="data-card">
                <div class="data-card-head">
                <h3>Tous les employés</h3>
                <div style="display:flex;gap:6px">
                    <input type="text" class="f-input" placeholder="Rechercher..." style="width:200px;padding:6px 10px;font-size:.8rem"/>
                    <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto">
                    <option>Tous les depts</option>
                    <option>IT</option>
                    <option>Finance</option>
                    </select>
                </div>
                </div>
                <table class="tbl">
                <thead>
                    <tr><th>Employé</th><th>Département</th><th>Rôle</th><th>Embauche</th><th>Statut</th><th>Solde annuel</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($employes as $emp) : ?>
                    <tr>
                        <td>
                            <div class="profile-row">
                            <div class="avatar av-green" style="width:32px;height:32px;font-size:.68rem"><?= esc($emp['initiales']) ?></div>
                            <div class="profile-info"><div class="pname"><?= esc($emp['prenom']) ?> <?= esc($emp['nom']) ?></div><div class="pdept"><?= esc($emp['email']) ?></div></div>
                            </div>
                        </td>
                        <td class="td-muted"><?= esc($emp['departement_nom'] ?? '-') ?></td>
                        <td><span class="type-badge" style="background:#f1efe8;color:#444441"><?= esc($emp['role_label']) ?></span></td>
                        <td class="td-muted td-mono" style="font-size:.78rem"><?= esc($emp['date_embauche'] ?? '-') ?></td>
                        <td><span class="statut <?= esc($emp['statut_class']) ?>" style="font-size:.68rem"><?= esc($emp['statut_label']) ?></span></td>
                        <td><span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--forest)"><?= (int) $emp['solde_total_restant'] ?>/<?= (int) $emp['solde_total_attribue'] ?> j</span></td>
                        <td>
                            <div class="action-btns">
                            <button class="btn-sm btn-edit"><i class="bi bi-pencil"></i> Éditer</button>
                            <button class="btn-sm btn-del"><i class="bi bi-slash-circle"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
            </div>

        </div>
        <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
    </div>

    </div>
</body>
</html>