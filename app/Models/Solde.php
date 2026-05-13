<?php

namespace App\Models;

use CodeIgniter\Model;

class Solde extends Model
{
    protected $table = 'soldes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['employe_id', 'type_conge_id', 'annee', 'jours_attribues', 'jours_pris', 'restant'];
    protected $useTimestamps = false;

    // Règles de validation
    protected $validationRules = [
        'employe_id' => 'required|integer|is_not_unique[employes.id]',
        'type_conge_id' => 'required|integer|is_not_unique[types_conge.id]',
        'annee' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[2100]',
        'jours_attribues' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[365]',
        'jours_pris' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[365]',
        'restant' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[365]',
    ];

    protected $validationMessages = [
        'employe_id' => [
            'required' => 'L\'ID de l\'employé est obligatoire.',
            'integer' => 'L\'ID de l\'employé doit être un nombre entier.',
            'is_not_unique' => 'Cet employé n\'existe pas.',
        ],
        'type_conge_id' => [
            'required' => 'L\'ID du type de congé est obligatoire.',
            'integer' => 'L\'ID du type de congé doit être un nombre entier.',
            'is_not_unique' => 'Ce type de congé n\'existe pas.',
        ],
        'annee' => [
            'required' => 'L\'année est obligatoire.',
            'integer' => 'L\'année doit être un nombre entier.',
            'greater_than_equal_to' => 'L\'année doit être supérieure ou égale à 2000.',
            'less_than_equal_to' => 'L\'année doit être inférieure ou égale à 2100.',
        ],
        'jours_attribues' => [
            'required' => 'Le nombre de jours attribués est obligatoire.',
            'integer' => 'Le nombre de jours doit être un nombre entier.',
            'greater_than_equal_to' => 'Le nombre de jours doit être supérieur ou égal à 0.',
            'less_than_equal_to' => 'Le nombre de jours ne peut pas dépasser 365.',
        ],
        'jours_pris' => [
            'integer' => 'Le nombre de jours pris doit être un nombre entier.',
            'greater_than_equal_to' => 'Le nombre de jours pris doit être supérieur ou égal à 0.',
            'less_than_equal_to' => 'Le nombre de jours pris ne peut pas dépasser 365.',
        ],
        'restant' => [
            'integer' => 'Le nombre de jours restants doit être un nombre entier.',
            'greater_than_equal_to' => 'Le nombre de jours restants doit être supérieur ou égal à 0.',
            'less_than_equal_to' => 'Le nombre de jours restants ne peut pas dépasser 365.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks pour calculer automatiquement le restant
    protected $beforeInsert = ['calculerRestant'];
    protected $beforeUpdate = ['calculerRestant'];

    /**
     * Calcule automatiquement le nombre de jours restants
     */
    protected function calculerRestant(array $data)
    {
        if (isset($data['data']['jours_attribues']) && isset($data['data']['jours_pris'])) {
            $data['data']['restant'] = $data['data']['jours_attribues'] - $data['data']['jours_pris'];
        }
        return $data;
    }

    public function mettreAJourSolde(int $employe_id, int $type_conge_id, int $annee, int $jours_pris)
    {
        // Récupérer le solde existant
        $solde = $this->where(['employe_id' => $employe_id, 'type_conge_id' => $type_conge_id, 'annee' => $annee])->first();

        if ($solde) {
            // Mettre à jour le nombre de jours pris et recalculer le restant
            $jours_pris_total = $solde['jours_pris'] + $jours_pris;
            $restant = max(0, $solde['jours_attribues'] - $jours_pris_total);

            return $this->update($solde['id'], [
                'jours_pris' => $jours_pris_total,
                'restant' => $restant,
            ]);
        }

        return false; // Solde non trouvé
    }

    public function getSoldeTotal(int $employe_id, int $annee): array
    {
        $row = $this->select('COALESCE(SUM(soldes.jours_attribues), 0) as total_attribues, COALESCE(SUM(soldes.jours_pris), 0) as total_pris, COALESCE(SUM(soldes.restant), 0) as total_restant')
            ->where(['soldes.employe_id' => $employe_id, 'soldes.annee' => $annee])
            ->first();

        return $row ?? [
            'total_attribues' => 0,
            'total_pris' => 0,
            'total_restant' => 0,
        ];
    }

    public function getSoldesByEmployeAndAnnee(int $employe_id, int $annee): array
    {
        return $this->select('soldes.*, types_conge.libelle, types_conge.deductible, types_conge.jours_annuels')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id', 'left')
            ->where(['soldes.employe_id' => $employe_id, 'soldes.annee' => $annee])
            ->orderBy('types_conge.libelle', 'ASC')
            ->findAll();
    }
}
