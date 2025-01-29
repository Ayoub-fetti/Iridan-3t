<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/fonctionnaire.php';

header('Content-Type: application/json');

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID non fourni']);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    $fonctionnaire = new Fonctionnaire();

    $id = intval($_GET['id']);
    $result = $fonctionnaire->getPersonnelById($id);

    if (!$result['success']) {
        http_response_code(404);
    }

    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
