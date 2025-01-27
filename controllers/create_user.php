<?php
require_once '../config/Database.php';
require_once '../models/user.php';

header('Content-Type: application/json');

// Vérifier si la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

// Récupérer les données du formulaire
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'fonctionnaire';

// Validation basique
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Tous les champs sont requis'
    ]);
    exit;
}

try {
    // Créer une instance de la base de données
    $database = new Database();
    $db = $database->connect();
    
    if (!$db) {
        throw new Exception("Erreur de connexion à la base de données");
    }
    
    // Créer une instance de User
    $user = new User($db);
    
    // Créer l'utilisateur
    $result = $user->createUser($username, $email, $password, $role);
    
    // Retourner le résultat
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage()
    ]);
}
