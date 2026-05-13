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