<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class HistoriqueController extends ResourceController
{
    protected $format = 'json';

    public function index($numero = null)
    {
        $compte = model('CompteModel')->where('numero', $numero)->first();
        if (!$compte) return $this->failNotFound('Compte introuvable.');

        $db = \Config\Database::connect();
        $rows = $db->table('mvmtCompte m')
            ->select('m.id, m.valeur, m.date, m.idType, m.indTypeOp, t.type as type_op, tm.type as type_mvmt')
            ->join('typeOperation t', 't.id = m.indTypeOp')
            ->join('typeMvmtComp tm', 'tm.id = m.idType')
            ->where('m.idCompte', $compte['id'])
            ->orderBy('m.id', 'DESC')
            ->get()->getResultArray();

        return $this->respond($rows);
    }
}