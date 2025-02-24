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
                type_contract, 
                date_embauche, 
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
                :type_contract,
                :date_embauche,
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
            $stmt->bindParam(':type_contract', $data['type_contract']);
            $stmt->bindParam(':date_embauche', $data['date_embauche']);
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
            error_log("Début de updatePersonnel avec ID: $id");
            error_log("Données reçues: " . print_r($data, true));
            
            $setFields = [];
            $params = [':id' => $id];

            // Liste des champs possibles
            $allowedFields = [
                'nom_complet', 'carte_identite', 'date_expiration_carte', 
                'role', 'situation_familiale', 'ville', 'adresse', 
                'contrat', 'type_contract', 'date_embauche', 'date_demission', 
                'permit_conduire', 'date_expiration_permit', 
                'visite_medicale', 'date_expiration_visite', 'photo'
            ];

            error_log("Champs autorisés: " . implode(', ', $allowedFields));

            // Construire la requête dynamiquement
            foreach ($data as $key => $value) {
                if (in_array($key, $allowedFields) && $key !== 'id') {
                    $setFields[] = "$key = :$key";
                    $params[":$key"] = $value;
                    error_log("Ajout du champ $key avec la valeur: $value");
                } else {
                    error_log("Champ ignoré: $key");
                }
            }

            if (empty($setFields)) {
                error_log("Aucun champ à mettre à jour");
                return [
                    'success' => false,
                    'message' => 'Aucun champ à mettre à jour'
                ];
            }

            $query = "UPDATE personnel SET " . implode(', ', $setFields) . " WHERE id = :id";
            error_log("Requête SQL: " . $query);
            error_log("Paramètres: " . print_r($params, true));

            $stmt = $this->conn->prepare($query);

            if($stmt->execute($params)) {
                error_log("Mise à jour réussie");
                return [
                    'success' => true,
                    'message' => 'Personnel mis à jour avec succès'
                ];
            }

            error_log("Échec de la mise à jour");
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du personnel'
            ];

        } catch(PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour récupérer un personnel par son ID
    public function getPersonnelById($id) {
        try {
            $query = "SELECT * FROM personnel WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $personnel = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($personnel) {
                return [
                    'success' => true,
                    'data' => $personnel
                ];
            }

            return [
                'success' => false,
                'message' => 'Personnel non trouvé'
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour ajouter une voiture
    public function createCar($data, $files) {
        try {
            // Gérer les téléchargements de fichiers
            $uploadedFiles = [];
            $fileFields = [
                'carte_grise' => 'carte_grise',
                'visite_technique' => 'visite_technique',
                'assurance' => 'assurance',
                'vignette' => 'vignette',
                'feuille_circulation' => 'circulation',
                'feuille_extincteur' => 'extincteur',
                'feuille_tachygraphe' => 'tachygraphe'
            ];

            foreach ($fileFields as $field => $folder) {
                if (isset($files[$field]) && $files[$field]['error'] === 0) {
                    $file = $files[$field];
                    $fileName = uniqid() . '_' . basename($file['name']);
                    $targetPath = __DIR__ . '/../uploads/cars/' . $folder . '/' . $fileName;
                    
                    // Vérifier si c'est un PDF
                    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if ($fileType != "pdf") {
                        throw new Exception("Le fichier $field doit être un PDF");
                    }

                    // Déplacer le fichier
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $uploadedFiles[$field] = 'uploads/cars/' . $folder . '/' . $fileName;
                    } else {
                        throw new Exception("Erreur lors du téléchargement du fichier $field");
                    }
                } else {
                    throw new Exception("Le fichier $field est requis");
                }
            }

            // Mettre à jour les chemins des fichiers dans $data
            foreach ($fileFields as $field => $folder) {
                $data[$field] = $uploadedFiles[$field];
            }

            $query = "INSERT INTO cars (
                matricule,
                marque,
                ville,
                chauffeurs_id,
                carte_grise,
                date_expiration_carte_grise,
                visite_technique,
                date_expiration_visite,
                assurance,
                date_expiration_assurance,
                vignette,
                date_expiration_vignette,
                feuille_circulation,
                date_expiration_circulation,
                feuille_extincteur,
                date_expiration_extincteur,
                feuille_tachygraphe,
                date_expiration_tachygraphe,
                status
            ) VALUES (
                :matricule,
                :marque,
                :ville,
                :chauffeurs_id,
                :carte_grise,
                :date_expiration_carte_grise,
                :visite_technique,
                :date_expiration_visite,
                :assurance,
                :date_expiration_assurance,
                :vignette,
                :date_expiration_vignette,
                :feuille_circulation,
                :date_expiration_circulation,
                :feuille_extincteur,
                :date_expiration_extincteur,
                :feuille_tachygraphe,
                :date_expiration_tachygraphe,
                :status
            )";

            $stmt = $this->conn->prepare($query);

            // Bind les paramètres
            $stmt->bindParam(':matricule', $data['matricule']);
            $stmt->bindParam(':marque', $data['marque']);
            $stmt->bindParam(':ville', $data['ville']);
            $stmt->bindParam(':chauffeurs_id', $data['chauffeurs_id']);
            $stmt->bindParam(':carte_grise', $data['carte_grise']);
            $stmt->bindParam(':date_expiration_carte_grise', $data['date_expiration_carte_grise']);
            $stmt->bindParam(':visite_technique', $data['visite_technique']);
            $stmt->bindParam(':date_expiration_visite', $data['date_expiration_visite']);
            $stmt->bindParam(':assurance', $data['assurance']);
            $stmt->bindParam(':date_expiration_assurance', $data['date_expiration_assurance']);
            $stmt->bindParam(':vignette', $data['vignette']);
            $stmt->bindParam(':date_expiration_vignette', $data['date_expiration_vignette']);
            $stmt->bindParam(':feuille_circulation', $data['feuille_circulation']);
            $stmt->bindParam(':date_expiration_circulation', $data['date_expiration_circulation']);
            $stmt->bindParam(':feuille_extincteur', $data['feuille_extincteur']);
            $stmt->bindParam(':date_expiration_extincteur', $data['date_expiration_extincteur']);
            $stmt->bindParam(':feuille_tachygraphe', $data['feuille_tachygraphe']);
            $stmt->bindParam(':date_expiration_tachygraphe', $data['date_expiration_tachygraphe']);
            $stmt->bindParam(':status', $data['status']);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Voiture ajoutée avec succès'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de la voiture'
            ];

        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour récupérer toutes les voitures
    public function getAllCars() {
        try {
            $query = "SELECT c.*, p.nom_complet as chauffeur_nom 
                     FROM cars c 
                     LEFT JOIN personnel p ON c.chauffeurs_id = p.id 
                     ORDER BY c.matricule";
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

    // Pour récupérer une voiture spécifique
    public function getCarByMatricule($matricule) {
        try {
            $query = "SELECT c.*, p.nom_complet as chauffeur_nom 
                     FROM cars c 
                     LEFT JOIN personnel p ON c.chauffeurs_id = p.id 
                     WHERE c.matricule = :matricule";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':matricule', $matricule);
            $stmt->execute();

            $car = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($car) {
                return [
                    'success' => true,
                    'data' => $car
                ];
            }

            return [
                'success' => false,
                'message' => 'Voiture non trouvée'
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour modifier une voiture
    public function updateCar($oldMatricule, $data, $files = null) {
        try {
            $fileFields = [
                'carte_grise' => 'carte_grise',
                'visite_technique' => 'visite_technique',
                'assurance' => 'assurance',
                'vignette' => 'vignette',
                'feuille_circulation' => 'circulation',
                'feuille_extincteur' => 'extincteur',
                'feuille_tachygraphe' => 'tachygraphe'
            ];

            // Gérer les nouveaux fichiers s'ils sont fournis
            if ($files) {
                foreach ($fileFields as $field => $folder) {
                    if (isset($files[$field]) && $files[$field]['error'] === 0) {
                        $file = $files[$field];
                        $fileName = uniqid() . '_' . basename($file['name']);
                        $targetPath = __DIR__ . '/../uploads/cars/' . $folder . '/' . $fileName;
                        
                        // Vérifier si c'est un PDF
                        $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        if ($fileType != "pdf") {
                            throw new Exception("Le fichier $field doit être un PDF");
                        }

                        // Supprimer l'ancien fichier
                        $oldFile = $this->getCarByMatricule($oldMatricule);
                        if ($oldFile['success'] && !empty($oldFile['data'][$field])) {
                            $oldPath = __DIR__ . '/../' . $oldFile['data'][$field];
                            if (file_exists($oldPath)) {
                                unlink($oldPath);
                            }
                        }

                        // Déplacer le nouveau fichier
                        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                            $data[$field] = 'uploads/cars/' . $folder . '/' . $fileName;
                        } else {
                            throw new Exception("Erreur lors du téléchargement du fichier $field");
                        }
                    }
                }
            }

            $query = "UPDATE cars SET 
                matricule = :new_matricule,
                marque = :marque,
                ville = :ville,
                chauffeurs_id = :chauffeurs_id,";

            // Ajouter les champs de fichiers seulement s'ils sont présents dans $data
            foreach ($fileFields as $field => $folder) {
                if (isset($data[$field])) {
                    $query .= " $field = :$field,";
                }
            }

            $query .= "
                date_expiration_carte_grise = :date_expiration_carte_grise,
                date_expiration_visite = :date_expiration_visite,
                date_expiration_assurance = :date_expiration_assurance,
                date_expiration_vignette = :date_expiration_vignette,
                date_expiration_circulation = :date_expiration_circulation,
                date_expiration_extincteur = :date_expiration_extincteur,
                date_expiration_tachygraphe = :date_expiration_tachygraphe,
                status = :status
                WHERE matricule = :old_matricule";

            $stmt = $this->conn->prepare($query);

            // Bind les paramètres
            $stmt->bindParam(':old_matricule', $oldMatricule);
            $stmt->bindParam(':new_matricule', $data['matricule']);
            $stmt->bindParam(':marque', $data['marque']);
            $stmt->bindParam(':ville', $data['ville']);
            $stmt->bindParam(':chauffeurs_id', $data['chauffeurs_id']);
            $stmt->bindParam(':date_expiration_carte_grise', $data['date_expiration_carte_grise']);
            $stmt->bindParam(':date_expiration_visite', $data['date_expiration_visite']);
            $stmt->bindParam(':date_expiration_assurance', $data['date_expiration_assurance']);
            $stmt->bindParam(':date_expiration_vignette', $data['date_expiration_vignette']);
            $stmt->bindParam(':date_expiration_circulation', $data['date_expiration_circulation']);
            $stmt->bindParam(':date_expiration_extincteur', $data['date_expiration_extincteur']);
            $stmt->bindParam(':date_expiration_tachygraphe', $data['date_expiration_tachygraphe']);
            $stmt->bindParam(':status', $data['status']);

            // Bind les paramètres de fichiers s'ils sont présents
            foreach ($fileFields as $field => $folder) {
                if (isset($data[$field])) {
                    $stmt->bindParam(":$field", $data[$field]);
                }
            }

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Voiture mise à jour avec succès'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la voiture'
            ];

        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour supprimer une voiture
    public function deleteCar($matricule) {
        try {
            $query = "DELETE FROM cars WHERE matricule = :matricule";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':matricule', $matricule);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Voiture supprimée avec succès'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression de la voiture'
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour ajouter un accident
    public function createAccident($data) {
        try {
            $query = "INSERT INTO accidents (
                cars_id,
                chauffeurs_id,
                date_declaration_assurance,
                `procédure`,
                status_resolution,
                commentaire,
                date_accident,
                date_reparation,
                suivie

            ) VALUES (
                :cars_id,
                :chauffeurs_id,
                :date_declaration_assurance,
                :procedure,
                :status_resolution,
                :commentaire,
                :date_accident,
                :date_reparation,
                :suivie
            )";

            $stmt = $this->conn->prepare($query);

            // Bind les paramètres
            $stmt->bindParam(':cars_id', $data['cars_id']);
            $stmt->bindParam(':chauffeurs_id', $data['chauffeurs_id']);
            $stmt->bindParam(':date_declaration_assurance', $data['date_declaration_assurance']);
            $stmt->bindParam(':procedure', $data['procedure']);
            $stmt->bindParam(':status_resolution', $data['status_resolution']);
            $stmt->bindParam(':commentaire', $data['commentaire']);
            $stmt->bindParam(':date_accident', $data['date_accident']);
            $stmt->bindParam(':date_reparation', $data['date_reparation']);
            $stmt->bindParam(':suivie', $data['suivie']);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Accident enregistré avec succès',
                    'id' => $this->conn->lastInsertId()
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de l\'accident'
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour récupérer tous les accidents
    public function getAllAccidents() {
        try {
            $query = "SELECT a.*, c.matricule as matricule_vehicule, p.nom_complet as nom_chauffeur 
                     FROM accidents a 
                     LEFT JOIN cars c ON a.cars_id = c.matricule 
                     LEFT JOIN personnel p ON a.chauffeurs_id = p.id 
                     ORDER BY date_declaration_assurance DESC";

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

    // Pour supprimer un accident
    public function deleteAccident($id) {
        try {
            $query = "DELETE FROM accidents WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Accident supprimé avec succès'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'accident'
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour mettre à jour un accident
    public function updateAccident($id, $data) {
        try {
            $query = "UPDATE accidents SET 
                cars_id = :cars_id,
                chauffeurs_id = :chauffeurs_id,
                date_declaration_assurance = :date_declaration_assurance,
                -- `procédure` = :procédure,
                `procédure` = :procedure,
                status_resolution = :status_resolution,
                commentaire = :commentaire,
                date_accident  = :date_accident,
                date_reparation = :date_reparation,
                suivie = :suivie
                WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            // Bind les paramètres
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':cars_id', $data['cars_id']);
            $stmt->bindParam(':chauffeurs_id', $data['chauffeurs_id']);
            $stmt->bindParam(':date_declaration_assurance', $data['date_declaration_assurance']);
            $stmt->bindParam(':procedure', $data['procedure']);
            $stmt->bindParam(':status_resolution', $data['status_resolution']);
            $stmt->bindParam(':commentaire', $data['commentaire']);
            $stmt->bindParam(':date_accident', $data['date_accident']);
            $stmt->bindParam(':date_reparation', $data['date_reparation']);
            $stmt->bindParam(':suivie', $data['suivie']);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Accident mis à jour avec succès'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'accident'
            ];

        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    // Pour récupérer uniquement les chauffeurs avec les informations essentielles
    public function getChauffeurs() {
        try {
            $query = "SELECT id, nom_complet FROM personnel WHERE role = 'chauffeurs' ORDER BY nom_complet ASC";
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

    public function getPersonnelByEmbaucheDate($date) {
        try {
            $query = "SELECT * FROM personnel WHERE date_embauche <= :date AND date_demission IS NOT NULL ORDER BY date_embauche DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':date', $date);
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
}