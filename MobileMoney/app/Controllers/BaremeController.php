<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class BaremeController extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $bareme = model('BaremeFraisModel')->findAll();
        $types  = model('TypeOperationModel')->findAll();
        $typeMap = array_column($types, 'type', 'id');

        foreach ($bareme as &$b) {
            $b['type_label'] = $typeMap[$b['idtypeOp']] ?? '';
        }
        return $this->respond($bareme);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $model = model('BaremeFraisModel');
        $id = $model->insert([
            'idtypeOp'    => $data['idtypeOp'],
            'montant_min' => $data['montant_min'],
            'montant_max' => $data['montant_max'],
            'prix'        => $data['prix'],
        ]);
        return $this->respondCreated(['id' => $id]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        model('BaremeFraisModel')->update($id, [
            'idtypeOp'    => $data['idtypeOp'],
            'montant_min' => $data['montant_min'],
            'montant_max' => $data['montant_max'],
            'prix'        => $data['prix'],
        ]);
        return $this->respond(['success' => true]);
    }

    public function delete($id = null)
    {
        model('BaremeFraisModel')->delete($id);
        return $this->respondDeleted(['success' => true]);
    }
}