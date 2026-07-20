<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurExterneModel extends Model
{
    protected $table = 'operateurExterne';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['nom'];
}
