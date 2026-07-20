<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table = 'baremeFrais';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'idtypeOp',
        'montant_min',
        'montant_max',
        'prix'
    ];
}