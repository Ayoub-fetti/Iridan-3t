<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/fonctionnaire.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID non fourni']);
    exit;
}

$database = new Database();
$db = $database->connect();
$fonctionnaire = new Fonctionnaire();

$id = intval($_POST['id']);
$result = $fonctionnaire->deletePersonnel($id);

echo json_encode($result);
exit;
