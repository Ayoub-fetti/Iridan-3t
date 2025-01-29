<?php
require_once '../../models/fonctionnaire.php';
require_once '../../config/Database.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

if (!isset($_GET['matricule'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Matricule non fourni']);
    exit();
}

$fonctionnaire = new Fonctionnaire();
$result = $fonctionnaire->getCarByMatricule($_GET['matricule']);

header('Content-Type: application/json');
if ($result['success']) {
    echo json_encode($result['data']);
} else {
    echo json_encode(['error' => $result['message']]);
}
