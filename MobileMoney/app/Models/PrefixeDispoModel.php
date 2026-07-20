<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeDispoModel extends Model
{
    protected $table = 'prefixeDispo';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'prefixe'
    ];
}