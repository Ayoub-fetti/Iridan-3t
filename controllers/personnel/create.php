<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/fonctionnaire.php';

try {
    $database = new Database();
    $db = $database->connect();
    $fonctionnaire = new Fonctionnaire();

    // Fonction pour gérer l'upload de fichier
    function handleFileUpload($file, $directory) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/' . $directory . '/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '_' . basename($file['name']);
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                return 'uploads/' . $directory . '/' . $fileName;
            }
        }
        return null;
    }

    // Récupérer les données du formulaire
    $data = [
        'nom_complet' => $_POST['nom_complet'],
        'carte_identite' => null, // Sera mis à jour avec le chemin du fichier
        'date_expiration_carte' => $_POST['date_expiration_carte'],
        'role' => $_POST['role'],
        'situation_familiale' => $_POST['situation_familiale'],
        'ville' => $_POST['ville'],
        'adresse' => $_POST['adresse'] ?? null,
        'contrat' => $_POST['contrat'] ?? null,
        'type_contract' => $_POST['type_contract'] ?? null,
        'date_embauche' => $_POST['date_embauche'],
        'date_demission' => $_POST['date_demission'],
        'permit_conduire' => null, // Sera mis à jour avec le chemin du fichier
        'date_expiration_permit' => $_POST['date_expiration_permit'] ?? null,
        'visite_medicale' => null, // Sera mis à jour avec le chemin du fichier
        'date_expiration_visite' => $_POST['date_expiration_visite'] ?? null,
        'photo' => null
    ];

    // Gérer l'upload de la carte d'identité
    if (isset($_FILES['carte_identite_file'])) {
        $data['carte_identite'] = handleFileUpload($_FILES['carte_identite_file'], 'cartes_identite');
    }

    // gerer Contrat
    if (isset($_FILES['contrat_file'])) {
        $data['contrat'] = handleFileUpload($_FILES['contrat_file'], 'contrat');
    }

    // Gérer l'upload du permis de conduire
    if (isset($_FILES['permit_conduire_file'])) {
        $data['permit_conduire'] = handleFileUpload($_FILES['permit_conduire_file'], 'permis');
    }

    // Gérer l'upload de la visite médicale
    if (isset($_FILES['visite_medicale_file'])) {
        $data['visite_medicale'] = handleFileUpload($_FILES['visite_medicale_file'], 'visites_medicales');
    }

    // Gérer l'upload de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $data['photo'] = handleFileUpload($_FILES['photo'], 'photos');
    }

    // Créer le personnel
    $result = $fonctionnaire->createPersonnel($data);

    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
