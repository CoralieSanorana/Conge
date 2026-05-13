<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeConge extends Model
{
    protected $table = 'types_conge';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['libelle', 'jours_annuels', 'deductible'];
    protected $useTimestamps = false;

    // Règles de validation
    protected $validationRules = [
        'libelle' => 'required|string|max_length[255]|is_unique[types_conge.libelle,id,{id}]',
        'jours_annuels' => 'required|integer|greater_than[0]|less_than_equal_to[365]',
        'deductible' => 'permit_empty|in_list[0,1]|integer',
    ];

    protected $validationMessages = [
        'libelle' => [
            'required' => 'Le libellé du type de congé est obligatoire.',
            'max_length' => 'Le libellé ne peut pas dépasser 255 caractères.',
            'is_unique' => 'Ce type de congé existe déjà.',
        ],
        'jours_annuels' => [
            'required' => 'Le nombre de jours annuels est obligatoire.',
            'integer' => 'Le nombre de jours doit être un nombre entier.',
            'greater_than' => 'Le nombre de jours doit être supérieur à 0.',
            'less_than_equal_to' => 'Le nombre de jours ne peut pas dépasser 365.',
        ],
        'deductible' => [
            'in_list' => 'Le statut déductible doit être 0 ou 1.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
