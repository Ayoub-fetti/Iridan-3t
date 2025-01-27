<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log de débogage
error_log("Delete user controller called");
error_log("POST data: " . print_r($_POST, true));

require_once '../config/Database.php';
require_once '../models/user.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log("Initializing database connection");
        $database = new Database();
        $db = $database->connect();
        $user = new User($db);

        // Récupérer et nettoyer l'ID de l'utilisateur
        $userId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        error_log("User ID to delete: " . $userId);

        // Valider l'ID
        if (empty($userId)) {
            throw new Exception('ID utilisateur requis');
        }

        // Supprimer l'utilisateur
        $result = $user->deleteUser($userId);
        error_log("Delete result: " . print_r($result, true));
        
        echo json_encode($result);
    } catch (Exception $e) {
        error_log("Error in delete_user.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ]);
    }
} else {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}
