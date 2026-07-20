<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table = 'prefixeDispo';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['prefixe'];
}
