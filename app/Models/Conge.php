<?php

namespace App\Models;

use CodeIgniter\Model;

class Conge extends Model
{
    protected $table = 'conges';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['employe_id', 'type_conge_id', 'date_debut', 'date_fin', 'nb_jours', 'motif', 'statut', 'commentaire_rh', 'created_at', 'traite_par'];
    protected $useTimestamps = false;

    // Règles de validation
    protected $validationRules = [
        'employe_id' => 'required|integer|is_not_unique[employes.id]',
        'type_conge_id' => 'required|integer|is_not_unique[types_conge.id]',
        'date_debut' => 'required|valid_date[Y-m-d]',
        'date_fin' => 'required|valid_date[Y-m-d]',
        'nb_jours' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[365]',
        'motif' => 'permit_empty|string|max_length[500]',
        'statut' => 'required|in_list[En attente,Approuvé,Refusé,Annulé]',
        'commentaire_rh' => 'permit_empty|string|max_length[500]',
        'traite_par' => 'permit_empty|integer|is_not_unique[employes.id]',
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
        'date_debut' => [
            'required' => 'La date de début est obligatoire.',
            'valid_date' => 'La date de début doit être au format YYYY-MM-DD.',
        ],
        'date_fin' => [
            'required' => 'La date de fin est obligatoire.',
            'valid_date' => 'La date de fin doit être au format YYYY-MM-DD.',
        ],
        'nb_jours' => [
            'numeric' => 'Le nombre de jours doit être un nombre.',
            'greater_than' => 'Le nombre de jours doit être supérieur à 0.',
            'less_than_equal_to' => 'Le nombre de jours ne peut pas dépasser 365.',
        ],
        'motif' => [
            'max_length' => 'Le motif ne peut pas dépasser 500 caractères.',
        ],
        'statut' => [
            'required' => 'Le statut est obligatoire.',
            'in_list' => 'Le statut doit être En attente, Approuvé, Refusé ou Annulé.',
        ],
        'commentaire_rh' => [
            'max_length' => 'Le commentaire ne peut pas dépasser 500 caractères.',
        ],
        'traite_par' => [
            'integer' => 'L\'ID du responsable RH doit être un nombre entier.',
            'is_not_unique' => 'Ce responsable n\'existe pas.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['calculerNbJours', 'definirDateCreation'];
    protected $beforeUpdate = ['calculerNbJours'];

    /**
     * Calcule automatiquement le nombre de jours entre date_debut et date_fin
     */
    protected function calculerNbJours(array $data)
    {
        if (isset($data['data']['date_debut']) && isset($data['data']['date_fin'])) {
            $debut = new \DateTime($data['data']['date_debut']);
            $fin = new \DateTime($data['data']['date_fin']);
            
            // Vérifier que la date de fin est après la date de début
            if ($fin < $debut) {
                throw new \Exception('La date de fin doit être après la date de début.');
            }
            
            // Calculer le nombre de jours (intervalle inclusive)
            $interval = $debut->diff($fin);
            $data['data']['nb_jours'] = $interval->days + 1; // +1 pour inclure le premier jour
        }
        return $data;
    }

    /**
     * Définit la date de création avant insertion
     */
    protected function definirDateCreation(array $data)
    {
        if (!isset($data['data']['created_at'])) {
            $data['data']['created_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    public function getCongesAttenteByEmploye(int $employeId)
    {
        return $this->where('employe_id', $employeId)
                    ->where('statut', 'En attente')
                    ->findAll();
    }

    public function getCongesAcceptesByEmploye(int $employeId)
    {
        return $this->where('employe_id', $employeId)
                    ->where('statut', 'Approuvé')
                    ->findAll();
    }

    public function getCongesRefusesByEmploye(int $employeId)
    {
        return $this->where('employe_id', $employeId)
                    ->where('statut', 'Refusé')
                    ->findAll();
    }

    public function getDernieresDemandesByEmploye(int $employeId, int $limit = 5)
    {
        return $this->select('conges.*, types_conge.libelle as type_conge_libelle')
                    ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
                    ->where('conges.employe_id', $employeId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }
}
