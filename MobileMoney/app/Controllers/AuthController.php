<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        $data = $this->request->getJSON(true);
        $numero = trim($data['numero'] ?? '');

        if (!$numero) {
            return $this->fail('Numéro requis.');
        }

        // vérification du préfixe autorisé
        $prefixes = model('PrefixeDispoModel')->findColumn('prefixe');
        $prefixOk = false;
        foreach ($prefixes as $p) {
            if (strpos($numero, $p) === 0) { $prefixOk = true; break; }
        }
        if (!$prefixOk) {
            return $this->fail('Préfixe non autorisé.', 403);
        }

        $compte = model('CompteModel')->where('numero', $numero)->first();
        if (!$compte) {
            return $this->fail('Aucun compte associé à ce numéro.', 404);
        }

        session()->set('client_numero', $compte['numero']);
        session()->set('client_id', $compte['id']);

        return $this->respond(['success' => true, 'compte' => $compte]);
    }
}