<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('departements')->insertBatch([
            ['nom' => 'Informatique', 'description' => 'Equipe technique et support applicatif'],
            ['nom' => 'Ressources Humaines', 'description' => 'Gestion des collaborateurs et des congés'],
            ['nom' => 'Direction', 'description' => 'Pilotage et coordination generale'],
        ]);

        $this->db->table('employes')->insertBatch([
            [
                'id' => 1,
                'nom' => 'Rakoto',
                'prenom' => 'Soa',
                'email' => 'employe@techmada.mg',
                'password' => '$2y$10$945GHUP7FV3AGsmxAoqEFOCK3e3b6aGXABxBkR29fYHqJoJ9KNjqG',
                'role' => 'EMPLOYE',
                'departement_id' => 1,
                'date_embauche' => '2024-03-11',
                'actif' => 1,
            ],
            [
                'id' => 2,
                'nom' => 'Ranaivo',
                'prenom' => 'Miora',
                'email' => 'rh@techmada.mg',
                'password' => '$2y$10$ecKBFmBX/BRQL3cPT5WOYuiOmEW7tyCsFquUy14m.rlkQG3DogbdO',
                'role' => 'RH',
                'departement_id' => 2,
                'date_embauche' => '2022-01-05',
                'actif' => 1,
            ],
            [
                'id' => 3,
                'nom' => 'Andrianarisoa',
                'prenom' => 'Jean',
                'email' => 'admin@techmada.mg',
                'password' => '$2y$10$GNSUPQl5y4FSliOfJMmhI.BXscU9mGp7oj7.TzJAjxeTGiNMm634y',
                'role' => 'ADMIN',
                'departement_id' => 3,
                'date_embauche' => '2021-09-20',
                'actif' => 1,
            ],
            [
                'id' => 4,
                'nom' => 'Ravelonirina',
                'prenom' => 'Aina',
                'email' => 'aina.r@techmada.mg',
                'password' => '$2y$10$945GHUP7FV3AGsmxAoqEFOCK3e3b6aGXABxBkR29fYHqJoJ9KNjqG',
                'role' => 'EMPLOYE',
                'departement_id' => 1,
                'date_embauche' => '2023-08-14',
                'actif' => 1,
            ],
        ]);

        $this->db->table('types_conge')->insertBatch([
            ['libelle' => 'Congé annuel', 'jours_annuels' => 30, 'deductible' => 1],
            ['libelle' => 'Congé maladie', 'jours_annuels' => 10, 'deductible' => 0],
            ['libelle' => 'Congé spécial', 'jours_annuels' => 5, 'deductible' => 1],
            ['libelle' => 'Sans solde', 'jours_annuels' => 0, 'deductible' => 0],
        ]);

        $this->db->table('soldes')->insertBatch([
            ['employe_id' => 1, 'type_conge_id' => 1, 'annee' => 2026, 'jours_attribues' => 30, 'jours_pris' => 12, 'restant' => 18],
            ['employe_id' => 1, 'type_conge_id' => 2, 'annee' => 2026, 'jours_attribues' => 10, 'jours_pris' => 2, 'restant' => 8],
            ['employe_id' => 1, 'type_conge_id' => 3, 'annee' => 2026, 'jours_attribues' => 5, 'jours_pris' => 4, 'restant' => 1],
            ['employe_id' => 4, 'type_conge_id' => 1, 'annee' => 2026, 'jours_attribues' => 30, 'jours_pris' => 6, 'restant' => 24],
            ['employe_id' => 4, 'type_conge_id' => 2, 'annee' => 2026, 'jours_attribues' => 10, 'jours_pris' => 1, 'restant' => 9],
            ['employe_id' => 2, 'type_conge_id' => 1, 'annee' => 2026, 'jours_attribues' => 30, 'jours_pris' => 3, 'restant' => 27],
        ]);

        $this->db->table('conges')->insertBatch([
            [
                'employe_id' => 1,
                'type_conge_id' => 1,
                'date_debut' => '2026-06-16',
                'date_fin' => '2026-06-20',
                'nb_jours' => 5,
                'motif' => 'Voyage familial',
                'statut' => 'En attente',
                'commentaire_rh' => null,
                'created_at' => '2026-05-10 09:30:00',
                'traite_par' => null,
            ],
            [
                'employe_id' => 1,
                'type_conge_id' => 2,
                'date_debut' => '2026-06-02',
                'date_fin' => '2026-06-03',
                'nb_jours' => 2,
                'motif' => 'Consultation médicale',
                'statut' => 'Approuvé',
                'commentaire_rh' => 'Justificatif reçu',
                'created_at' => '2026-05-05 14:10:00',
                'traite_par' => 2,
            ],
            [
                'employe_id' => 1,
                'type_conge_id' => 1,
                'date_debut' => '2026-05-12',
                'date_fin' => '2026-05-16',
                'nb_jours' => 5,
                'motif' => 'Repos annuel',
                'statut' => 'Approuvé',
                'commentaire_rh' => 'Demande validée',
                'created_at' => '2026-05-01 11:45:00',
                'traite_par' => 2,
            ],
            [
                'employe_id' => 1,
                'type_conge_id' => 3,
                'date_debut' => '2026-07-08',
                'date_fin' => '2026-07-08',
                'nb_jours' => 1,
                'motif' => 'Événement personnel',
                'statut' => 'Refusé',
                'commentaire_rh' => 'Période non disponible',
                'created_at' => '2026-05-12 08:20:00',
                'traite_par' => 2,
            ],
            [
                'employe_id' => 4,
                'type_conge_id' => 1,
                'date_debut' => '2026-06-24',
                'date_fin' => '2026-06-26',
                'nb_jours' => 3,
                'motif' => 'Déplacement personnel',
                'statut' => 'En attente',
                'commentaire_rh' => null,
                'created_at' => '2026-05-11 16:00:00',
                'traite_par' => null,
            ],
            [
                'employe_id' => 4,
                'type_conge_id' => 2,
                'date_debut' => '2026-04-14',
                'date_fin' => '2026-04-15',
                'nb_jours' => 2,
                'motif' => 'Maladie',
                'statut' => 'Approuvé',
                'commentaire_rh' => 'OK',
                'created_at' => '2026-04-12 10:00:00',
                'traite_par' => 2,
            ],
        ]);
    }
}
