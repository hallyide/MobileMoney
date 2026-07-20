<?php

namespace App\Models;

use CodeIgniter\Model;

class FraisMvmtModel extends Model
{
    protected $table = 'FraisMvmt';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'idMvmtCompt',
        'valeur',
        'typeOp',
        'date'
    ];
}