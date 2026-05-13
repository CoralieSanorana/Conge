# Conge
- [ok] Lecture du sujet

- [ok] Installation de SQLite3
    - [ok] Extension sqlite3 activée en CLI
    - [ok] PDO Sqlite (pdo_sqlite) utilisé pour le web (SqliteDb.php)

- [ok] Creation de base de donnees
    - [ok] Migrations (2026-05-13-000001_CreateCongesTables.php)
    - [ok] Tables:
        - [ok] departements: id, nom, description
        - [ok] employes: id, nom, prenom, email, password, role, departement_id, date_embauche, actif
        - [ok] types_conge: id, libelle, jours_annuels, deductible
        - [ok] soldes: id, employe_id, type_conge_id, annee, jours_attribues, jours_pris, restant
        - [ok] conges: id, employe_id, type_conge_id, date_debut, date_fin, nb_jours, motif, statut, commentaire_rh, created_at, traite_par

    - [ok] Donnees de test (InitialDataSeeder.php)
        - [ok] 3 departements
        - [ok] 4 employes (admin, rh, 2 employes)
        - [ok] 4 types_conge
        - [ok] 6 soldes initialisés
        - [ok] 6 demandes de conge de test

- [ok] PDO Library
    - [ok] SqliteDb.php (PDO wrapper avec fetchOne, fetchAll, execute)

- [ok] Controllers
    - [ok] EmployeController (login, dashboard, logout, profile, employeForm, submitEmploye)
    - [ok] CongeController (demande, submitDemande, historique, approbation, submitApprobation)
    - [ok] RHController (dashboard)
    - [ok] AdminController (stubs à créer)

- [ok] Routes
    - [ok] GET /login, POST /login
    - [ok] GET /logout
    - [ok] GET /employe/dashboard, /employe/logout, /employe/profile
    - [ok] GET /employe/conge/demande, POST /employe/conge/submit
    - [ok] GET /employe/conge/historique
    - [ok] GET /rh/dashboard
    - [ok] GET /admin/employe/form, POST /admin/employe/submit
    - [ok] Autres routes admin (departement, type-conge)

- [ok] Pages:
    - [ok] login.php
        - [ok] formulaire login() GET/POST
        - [ok] authentification avec password_verify
        - [ok] session management
    - [ok] employe/
        - [ok] dashboard.php (affiche soldes, demandes recentes, metriques)
        - [ok] demande.php (formulaire création - validation solde à ajouter)
        - [ok] historique.php (liste demandes - stub)
        - [ok] profile.php (redirect vers dashboard)
    - [wip] rh/
        - [ok] dashboard.php (stub basique)
        - [ok] conge/approbation (formulaire approbation - stub)
    - [wip] admin/
        - [ok] dashboard.php (à créer)
        - [ok] employeForm.php (formulaire ajout + liste enrichie avec soldes)
        - [wip] departementForm.php (à créer)
        - [wip] typeCongeForm.php (à créer)

- [ok] Validations
    - [ok] demande.php: vérifier solde suffisant avant création
    - [ok] demande.php: vérifier pas de chevauchement de dates
    - [ok] demande.php: vérifier préavis minimum 48h

- [wip] Features avancées
    - [wip] Filtrage/recherche employés admin
    - [wip] Édition/suppression employés admin
    - [wip] Gestion départements (CRUD)
    - [wip] Gestion types congé (CRUD)
    - [wip] Approbation/refus demandes RH
    - [wip] Historique complète avec statuts