-- Activer le support des clés étrangères dans SQLite
PRAGMA foreign_keys = ON;

-- 1. Table des Départements
CREATE TABLE departements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    description TEXT
);

-- 2. Table des Employés
CREATE TABLE employes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT CHECK(role IN ('ADMIN', 'RH', 'EMPLOYE')) DEFAULT 'EMPLOYE',
    departement_id INTEGER,
    date_embauche DATE,
    actif INTEGER DEFAULT 1, -- 0 pour inactif, 1 pour actif
    FOREIGN KEY (departement_id) REFERENCES departements(id)
);

-- 3. Table des Types de Congé
CREATE TABLE types_conge (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL,
    jours_annuels INTEGER, -- Ex: 30
    deductible INTEGER DEFAULT 1 -- 0 ou 1
);

-- 4. Table des Soldes (La table pivot pour les compteurs)
CREATE TABLE soldes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employe_id INTEGER,
    type_conge_id INTEGER,
    annee INTEGER NOT NULL,
    jours_attribues INTEGER,
    jours_pris INTEGER DEFAULT 0,
    restant INTEGER,
    FOREIGN KEY (employe_id) REFERENCES employes(id),
    FOREIGN KEY (type_conge_id) REFERENCES types_conge(id)
);

-- 5. Table des Congés (Les demandes)
CREATE TABLE conges (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employe_id INTEGER,
    type_conge_id INTEGER,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    nb_jours REAL,
    motif TEXT,
    statut TEXT CHECK(statut IN ('En attente', 'Approuvé', 'Refusé', 'Annulé')) DEFAULT 'En attente',
    commentaire_rh TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    traite_par INTEGER,
    FOREIGN KEY (employe_id) REFERENCES employes(id),
    FOREIGN KEY (type_conge_id) REFERENCES types_conge(id),
    FOREIGN KEY (traite_par) REFERENCES employes(id)
);