<?php

namespace App\Models;

use CodeIgniter\Model;

class MvmtCompteModel extends Model
{
    protected $table = 'mvmtCompte';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'idCompte',
        'valeur',
        'date',
        'idType',
        'indTypeOp'
    ];
}