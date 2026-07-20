<?php

namespace App\Models;

use CodeIgniter\Model;

class CaisseOpModel extends Model
{
    protected $table = 'caisseOp';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'gains'
    ];
}