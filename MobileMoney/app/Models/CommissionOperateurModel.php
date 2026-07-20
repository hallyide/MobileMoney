<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionOperateurModel extends Model
{
    protected $table = 'commissionOperateur';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['idOperateur', 'pourcentage'];
}
