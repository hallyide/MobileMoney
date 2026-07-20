<?php

namespace App\Models;

use CodeIgniter\Model;

class MouvementCompteModel extends Model
{
    protected $table = 'mvmtCompte';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'idCompte', 'valeur', 'date', 'idType', 'indTypeOp', 'numeroDestinataire',
    ];

    /** Retourne les mouvements avec des libelles lisibles. */
    public function forAccount(int $compteId, ?int $limit = null): array
    {
        $builder = $this->select('mvmtCompte.*, typeMvmtComp.type AS sens, typeOperation.type AS operation, COALESCE(FraisMvmt.valeur, 0) AS frais')
            ->join('typeMvmtComp', 'typeMvmtComp.id = mvmtCompte.idType')
            ->join('typeOperation', 'typeOperation.id = mvmtCompte.indTypeOp')
            ->join('FraisMvmt', 'FraisMvmt.idMvmtCompt = mvmtCompte.id', 'left')
            ->where('mvmtCompte.idCompte', $compteId)
            ->orderBy('mvmtCompte.id', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }
}
