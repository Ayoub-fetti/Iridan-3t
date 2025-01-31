<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/fonctionnaire.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->connect();
    $fonctionnaire = new Fonctionnaire();

    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('ID non spécifié');
    }

    $id = intval($_POST['id']);
    
    // Debug: Afficher les données reçues
    error_log("Données POST reçues : " . print_r($_POST, true));
    error_log("Fichiers reçus : " . print_r($_FILES, true));
    
    // Liste des fichiers à traiter avec leur nom de champ dans la base de données
    $fileFields = [
        'photo' => 'photo',
        'carte_identite_file' => 'carte_identite',
        'contrat_file' => 'contrat',
        'permit_conduire_file' => 'permit_conduire',
        'visite_medicale_file' => 'visite_medicale'
    ];
    
    $data = $_POST;
    unset($data['action']); // Retirer le champ action s'il existe
    
    // Gérer les champs de date vides
    $dateFields = [
        'date_expiration_carte',
        'date_embauche',
        'date_demission',
        'date_expiration_permit',
        'date_expiration_visite'
    ];
    
    foreach ($dateFields as $dateField) {
        if (empty($data[$dateField])) {
            unset($data[$dateField]); // Supprimer le champ s'il est vide pour garder la valeur existante
        }
    }
    
    // Traitement des fichiers
    foreach ($fileFields as $uploadField => $dbField) {
        if (isset($_FILES[$uploadField]) && $_FILES[$uploadField]['size'] > 0) {
            $target_dir = "../../uploads/documents/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $fileType = strtolower(pathinfo($_FILES[$uploadField]["name"], PATHINFO_EXTENSION));
            $target_file = $target_dir . uniqid() . '.' . $fileType;
            
            error_log("Traitement du fichier $uploadField vers $target_file");
            
            // Vérifier la taille (max 5MB)
            if ($_FILES[$uploadField]["size"] > 5000000) {
                throw new Exception("Le fichier $uploadField est trop grand (max 5MB)");
            }
            
            // Pour la photo, autoriser les formats PDF et images
            if ($uploadField === 'photo') {
                if (!in_array($fileType, ["jpg", "jpeg", "png", "gif", "pdf"])) {
                    throw new Exception('Seuls les fichiers JPG, JPEG, PNG, GIF & PDF sont autorisés pour la photo');
                }
                
                // Vérifier si c'est une image
                if (in_array($fileType, ["jpg", "jpeg", "png", "gif"])) {
                    if (!getimagesize($_FILES[$uploadField]["tmp_name"])) {
                        throw new Exception('Le fichier image n\'est pas valide');
                    }
                }
            } else {
                // Pour les autres documents, uniquement PDF
                if ($fileType !== "pdf") {
                    throw new Exception("Seuls les fichiers PDF sont autorisés pour $uploadField");
                }
            }
            
            if (move_uploaded_file($_FILES[$uploadField]["tmp_name"], $target_file)) {
                $data[$dbField] = str_replace("../../", "", $target_file);
                error_log("Fichier déplacé avec succès. Chemin enregistré : " . $data[$dbField]);
            } else {
                throw new Exception("Erreur lors du téléchargement du fichier $uploadField");
            }
        }
    }

    error_log("Données à mettre à jour : " . print_r($data, true));
    $result = $fonctionnaire->updatePersonnel($id, $data);
    error_log("Résultat de la mise à jour : " . print_r($result, true));
    
    if (!$result['success']) {
        http_response_code(400);
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    error_log("Erreur : " . $e->getMessage());
    error_log("Trace : " . $e->getTraceAsString());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
