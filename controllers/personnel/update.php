<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/fonctionnaire.php';

header('Content-Type: application/json');

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID non fourni']);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    $fonctionnaire = new Fonctionnaire();

    $id = intval($_POST['id']);
    
    // Si un fichier est fourni
    if (isset($_FILES['photo']) && $_FILES['photo']['size'] > 0) {
        $target_dir = "../../uploads/documents/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $fileType = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . uniqid() . '.' . $fileType;
        
        // Vérifier la taille (max 5MB)
        if ($_FILES["photo"]["size"] > 5000000) {
            echo json_encode(['success' => false, 'message' => 'Le fichier est trop grand (max 5MB)']);
            exit;
        }
        
        // Autoriser les formats PDF et images
        if (!in_array($fileType, ["jpg", "jpeg", "png", "gif", "pdf"])) {
            echo json_encode(['success' => false, 'message' => 'Seuls les fichiers JPG, JPEG, PNG, GIF & PDF sont autorisés']);
            exit;
        }
        
        // Vérifier si c'est une image
        if (in_array($fileType, ["jpg", "jpeg", "png", "gif"])) {
            if (!getimagesize($_FILES["photo"]["tmp_name"])) {
                echo json_encode(['success' => false, 'message' => 'Le fichier image n\'est pas valide']);
                exit;
            }
        }
        
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $_POST['photo'] = str_replace("../../", "", $target_file);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement du fichier']);
            exit;
        }
    } else {
        // Si pas de nouveau fichier, ne pas modifier le champ photo
        unset($_POST['photo']);
    }

    $result = $fonctionnaire->updatePersonnel($id, $_POST);
    
    if (!$result['success']) {
        http_response_code(400);
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
