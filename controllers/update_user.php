<?php
// Désactiver l'affichage des erreurs pour éviter de corrompre la réponse JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Log de débogage
error_log("Update user controller called");
error_log("POST data: " . print_r($_POST, true));

try {
    require_once '../config/Database.php';
    require_once '../models/user.php';

    // Définir l'en-tête avant toute sortie
    header('Content-Type: application/json');

    // Vérifier la méthode HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

    // Récupérer et nettoyer les données du formulaire
    $userId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    error_log("Données filtrées - ID: $userId, Username: $username, Email: $email, Role: $role");

    // Valider les données
    if (empty($userId) || empty($username) || empty($email) || empty($role)) {
        throw new Exception('Tous les champs sont requis');
    }

    // Initialiser la connexion à la base de données
    $database = new Database();
    $db = $database->connect();
    
    if (!$db) {
        throw new Exception('Erreur de connexion à la base de données');
    }

    $user = new User($db);

    // Mettre à jour l'utilisateur
    $result = $user->updateUser($userId, $username, $email, $role);
    error_log("Résultat de la mise à jour: " . print_r($result, true));

    if (!$result['success']) {
        throw new Exception($result['message']);
    }

    echo json_encode($result);

} catch (Exception $e) {
    error_log("Erreur dans update_user.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
