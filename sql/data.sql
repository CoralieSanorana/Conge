-- Donnees de test pour la base de gestion des conges
PRAGMA foreign_keys = ON;

DELETE FROM conges;
DELETE FROM soldes;
DELETE FROM employes;
DELETE FROM types_conge;
DELETE FROM departements;

INSERT INTO departements (id, nom, description) VALUES
(1, 'Informatique', 'Equipe technique et support applicatif'),
(2, 'Ressources Humaines', 'Gestion des collaborateurs et des congés'),
(3, 'Direction', 'Pilotage et coordination generale');

INSERT INTO employes (id, nom, prenom, email, password, role, departement_id, date_embauche, actif) VALUES
(1, 'Rakoto', 'Soa', 'employe@example.mg', '$2y$10$MxWVQfv02h5xeA24sKmRKePNe466QRMruhXdvRb9RVT3e55Wikk3y', 'EMPLOYE', 1, '2024-03-11', 1),
(2, 'Ranaivo', 'Miora', 'rh@techmada.mg', '$2y$10$OobC8nxZb7YBJENxp/dk2Ou5.hJrNNBN288UJbkS84YPAMUZ3cuCC', 'RH', 2, '2022-01-05', 1),
(3, 'Andrianarisoa', 'Jean', 'admin@techmada.mg', '$2y$10$qcdCI9HXvBCVN7qDKnxj4uWz0biMudb1tVIDKlV1l.wSYmhjwRSg.', 'ADMIN', 3, '2021-09-20', 1),
(4, 'Ravelonirina', 'Aina', 'aina.r@techmada.mg', '$2y$10$MxWVQfv02h5xeA24sKmRKePNe466QRMruhXdvRb9RVT3e55Wikk3y', 'EMPLOYE', 1, '2023-08-14', 1);

INSERT INTO types_conge (id, libelle, jours_annuels, deductible) VALUES
(1, 'Congé annuel', 30, 1),
(2, 'Congé maladie', 10, 0),
(3, 'Congé spécial', 5, 1),
(4, 'Sans solde', 0, 0);

INSERT INTO soldes (id, employe_id, type_conge_id, annee, jours_attribues, jours_pris, restant) VALUES
(1, 1, 1, 2026, 30, 12, 18),
(2, 1, 2, 2026, 10, 2, 8),
(3, 1, 3, 2026, 5, 4, 1),
(4, 4, 1, 2026, 30, 6, 24),
(5, 4, 2, 2026, 10, 1, 9),
(6, 2, 1, 2026, 30, 3, 27);

INSERT INTO conges (id, employe_id, type_conge_id, date_debut, date_fin, nb_jours, motif, statut, commentaire_rh, created_at, traite_par) VALUES
(1, 1, 1, '2026-06-16', '2026-06-20', 5, 'Voyage familial', 'En attente', NULL, '2026-05-10 09:30:00', NULL),
(2, 1, 2, '2026-06-02', '2026-06-03', 2, 'Consultation médicale', 'Approuvé', 'Justificatif reçu', '2026-05-05 14:10:00', 2),
(3, 1, 1, '2026-05-12', '2026-05-16', 5, 'Repos annuel', 'Approuvé', 'Demande validée', '2026-05-01 11:45:00', 2),
(4, 1, 3, '2026-07-08', '2026-07-08', 1, 'Événement personnel', 'Refusé', 'Période non disponible', '2026-05-12 08:20:00', 2),
(5, 4, 1, '2026-06-24', '2026-06-26', 3, 'Déplacement personnel', 'En attente', NULL, '2026-05-11 16:00:00', NULL),
(6, 4, 2, '2026-04-14', '2026-04-15', 2, 'Maladie', 'Approuvé', 'OK', '2026-04-12 10:00:00', 2);
