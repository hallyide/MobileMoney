<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeOperateurModel extends Model
{
    protected $table = 'prefixeOperateur';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['idOperateur', 'prefixe'];
}
