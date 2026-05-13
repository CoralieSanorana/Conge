<?php

namespace App\Models;

use CodeIgniter\Model;

class Departement extends Model
{
    protected $table = 'departements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['nom', 'description'];
    protected $useTimestamps = false;

    // Règles de validation
    protected $validationRules = [
        'nom' => 'required|string|max_length[255]|is_unique[departements.nom,id,{id}]',
        'description' => 'permit_empty|string|max_length[500]',
    ];

    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom du département est obligatoire.',
            'is_unique' => 'Ce nom de département existe déjà.',
            'max_length' => 'Le nom ne peut pas dépasser 255 caractères.',
        ],
        'description' => [
            'max_length' => 'La description ne peut pas dépasser 500 caractères.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];
}
