<?php

namespace App\Models;

use CodeIgniter\Model;

class Employe extends Model
{
    protected $table = 'employes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['nom', 'prenom', 'email', 'password', 'role', 'departement_id', 'date_embauche', 'actif'];
    protected $useTimestamps = false;

    // Règles de validation
    protected $validationRules = [
        'nom' => 'required|string|max_length[255]',
        'prenom' => 'required|string|max_length[255]',
        'email' => 'required|valid_email|is_unique[employes.email,id,{id}]|max_length[255]',
        'password' => 'required|string|min_length[6]|max_length[255]',
        'role' => 'required|in_list[ADMIN,RH,EMPLOYE]',
        'departement_id' => 'permit_empty|integer|is_not_unique[departements.id]',
        'date_embauche' => 'permit_empty|valid_date[Y-m-d]',
        'actif' => 'permit_empty|in_list[0,1]|integer',
    ];

    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom de l\'employé est obligatoire.',
            'max_length' => 'Le nom ne peut pas dépasser 255 caractères.',
        ],
        'prenom' => [
            'required' => 'Le prénom de l\'employé est obligatoire.',
            'max_length' => 'Le prénom ne peut pas dépasser 255 caractères.',
        ],
        'email' => [
            'required' => 'L\'email est obligatoire.',
            'valid_email' => 'L\'email n\'est pas valide.',
            'is_unique' => 'Cet email est déjà utilisé.',
            'max_length' => 'L\'email ne peut pas dépasser 255 caractères.',
        ],
        'password' => [
            'required' => 'Le mot de passe est obligatoire.',
            'min_length' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'max_length' => 'Le mot de passe ne peut pas dépasser 255 caractères.',
        ],
        'role' => [
            'required' => 'Le rôle est obligatoire.',
            'in_list' => 'Le rôle doit être ADMIN, RH ou EMPLOYE.',
        ],
        'departement_id' => [
            'integer' => 'L\'ID du département doit être un nombre entier.',
            'is_not_unique' => 'Ce département n\'existe pas.',
        ],
        'date_embauche' => [
            'valid_date' => 'La date d\'embauche doit être au format YYYY-MM-DD.',
        ],
        'actif' => [
            'in_list' => 'Le statut actif doit être 0 ou 1.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks pour hasher le mot de passe avant insertion/mise à jour
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash le mot de passe avant insertion ou mise à jour
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    protected function passwordValide(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
