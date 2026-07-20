<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeMvmtCompModel extends Model
{
    protected $table = 'typeMvmtComp';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'type'
    ];
}