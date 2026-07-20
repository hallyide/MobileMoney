<?php

namespace App\Models;

use CodeIgniter\Model;

class TransfertExterneModel extends Model
{
    protected $table = 'transfertExterne';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'idMouvement',
        'idCompteEnvoyeur',
        'idOperateur',
        'numeroDestinataire',
        'montantEnvoye',
        'commission',
        'montantAReverser',
        'date',
    ];
}
