<?php
require_once __DIR__ . '/../config/Database.php';

class Fonctionnaire {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
// pour ajouter un personnel
    public function createPersonnel($data) {
        try {
            $query = "INSERT INTO personnel (
                nom_complet, 
                carte_identite, 
                date_expiration_carte, 
                role, 
                situation_familiale, 
                ville, 
                adresse, 
                contrat, 
                date_embauche, 
                date_demission, 
                permit_conduire, 
                date_expiration_permit, 
                visite_medicale, 
                date_expiration_visite,
                photo
            ) VALUES (
                :nom_complet,
                :carte_identite,
                :date_expiration_carte,
                :role,
                :situation_familiale,
                :ville,
                :adresse,
                :contrat,
                :date_embauche,
                :date_demission,
                :permit_conduire,
                :date_expiration_permit,
                :visite_medicale,
                :date_expiration_visite,
                :photo
            )";

            $stmt = $this->conn->prepare($query);

            // Bind les paramètres
            $stmt->bindParam(':nom_complet', $data['nom_complet']);
            $stmt->bindParam(':carte_identite', $data['carte_identite']);
            $stmt->bindParam(':date_expiration_carte', $data['date_expiration_carte']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':situation_familiale', $data['situation_familiale']);
            $stmt->bindParam(':ville', $data['ville']);
            $stmt->bindParam(':adresse', $data['adresse']);
            $stmt->bindParam(':contrat', $data['contrat']);
            $stmt->bindParam(':date_embauche', $data['date_embauche']);
            $stmt->bindParam(':date_demission', $data['date_demission']);
            $stmt->bindParam(':permit_conduire', $data['permit_conduire']);
            $stmt->bindParam(':date_expiration_permit', $data['date_expiration_permit']);
            $stmt->bindParam(':visite_medicale', $data['visite_medicale']);
            $stmt->bindParam(':date_expiration_visite', $data['date_expiration_visite']);
            $stmt->bindParam(':photo', $data['photo']);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Personnel créé avec succès',
                    'id' => $this->conn->lastInsertId()
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la création du personnel'
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour récupérer tous les personnels avec toutes les informations
    public function getAllPersonnel() {
        try {
            $query = "SELECT * FROM personnel ORDER BY date_embauche DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour supprimer un personnel
    public function deletePersonnel($id) {
        try {
            $query = "DELETE FROM personnel WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Personnel supprimé avec succès'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression du personnel'
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour mettre à jour un personnel
    public function updatePersonnel($id, $data) {
        try {
            $setFields = [];
            $params = [':id' => $id];

            // Liste des champs possibles
            $allowedFields = [
                'nom_complet', 'carte_identite', 'date_expiration_carte', 
                'role', 'situation_familiale', 'ville', 'adresse', 
                'contrat', 'date_embauche', 'date_demission', 
                'permit_conduire', 'date_expiration_permit', 
                'visite_medicale', 'date_expiration_visite', 'photo'
            ];

            // Construire la requête dynamiquement
            foreach ($data as $key => $value) {
                if (in_array($key, $allowedFields) && $key !== 'id') {
                    $setFields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if (empty($setFields)) {
                return [
                    'success' => false,
                    'message' => 'Aucun champ à mettre à jour'
                ];
            }

            $query = "UPDATE personnel SET " . implode(', ', $setFields) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            if($stmt->execute($params)) {
                return [
                    'success' => true,
                    'message' => 'Personnel mis à jour avec succès'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du personnel'
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }
}