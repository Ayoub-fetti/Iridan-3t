<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/fonctionnaire.php';

$response = ['success' => false, 'message' => 'Requête invalide'];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $fonctionnaire = new Fonctionnaire();
    $result = $fonctionnaire->getPersonnelById($id);
    
    if ($result['success']) {
        $response = [
            'success' => true,
            'data' => $result['data']
        ];
    } else {
        $response = [
            'success' => false,
            'message' => $result['message'] ?? 'Impossible de récupérer les informations du personnel'
        ];
    }
}

echo json_encode($response);
