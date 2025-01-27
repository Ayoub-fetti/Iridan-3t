<?php 
class User{
    protected $pdo;
    protected $id;
    protected $username;
    protected $email;
    protected $password;
    protected $role;



    public function __construct($pdo)
    {
        if (!($pdo instanceof PDO)) {
            throw new Exception("Un objet PDO valide est requis");
        }
        $this->pdo = $pdo;
        if (isset($_SESSION['user_id'])) {
            $this->id = $_SESSION['user_id'];
            // Charger les informations de l'utilisateur
            $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id = ?");
            $stmt->execute([$this->id]);
            $user = $stmt->fetch();
            if ($user) {
                $this->username = $user['username'];
                $this->email = $user['email'];
                $this->role = $user['role'];
            }
        }
    }
    // getters 
    public function getId(){ return $this->id;} 
    public function getUsername(){ return $this->username;} 
    public function getEmail(){ return $this->email;} 
    public function getPassword(){ return $this->password;} 
    public function getRole(){ return $this->role;}
    
    // setters

    public function setId($id) { $this->id = $id;}
    public function setUsername($username) { $this->username = $username;}
    public function setEmail($email) { $this->email = $email;}
    public function setPassword($password) { $this->password = $password;}
    public function setRole($role) { $this->role = $role;}

    // public function login
    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = ? AND password = ?");
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Mettre à jour les propriétés de l'objet
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->email = $user['email'];
            $this->role = $user['role'];
            
            return true;
        }
        return false;
    }

    public function createUser($username, $email, $password, $role) {
        try {
            // Hasher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Préparer la requête
            $stmt = $this->pdo->prepare("INSERT INTO utilisateur (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            
            // Exécuter la requête
            $result = $stmt->execute([$username, $email, $hashedPassword, $role]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Utilisateur créé avec succès',
                    'user_id' => $this->pdo->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la création de l\'utilisateur'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    public function getAllUsers() {
        try {
            $stmt = $this->pdo->query("SELECT id, full_name, email, role FROM utilisateur");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateUser($userId, $username, $email, $role) {
        try {
            // Log des données reçues
            error_log("updateUser called with - ID: $userId, Username: $username, Email: $email, Role: $role");

            // Vérifier si l'utilisateur existe
            $checkStmt = $this->pdo->prepare("SELECT id FROM utilisateur WHERE id = ?");
            $checkStmt->execute([$userId]);
            if (!$checkStmt->fetch()) {
                error_log("User not found - ID: $userId");
                return [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ];
            }

            // Vérifier si l'email est déjà utilisé par un autre utilisateur
            $emailStmt = $this->pdo->prepare("SELECT id FROM utilisateur WHERE email = ? AND id != ?");
            $emailStmt->execute([$email, $userId]);
            if ($emailStmt->fetch()) {
                error_log("Email already in use: $email");
                return [
                    'success' => false,
                    'message' => 'Cet email est déjà utilisé par un autre utilisateur'
                ];
            }

            $stmt = $this->pdo->prepare("UPDATE utilisateur SET full_name = ?, email = ?, role = ? WHERE id = ?");
            $result = $stmt->execute([$username, $email, $role, $userId]);
            
            if ($result) {
                $rowCount = $stmt->rowCount();
                error_log("Update successful - Rows affected: $rowCount");
                return [
                    'success' => true,
                    'message' => 'Utilisateur mis à jour avec succès',
                    'rowCount' => $rowCount
                ];
            } else {
                error_log("Update failed - PDO error info: " . print_r($stmt->errorInfo(), true));
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour de l\'utilisateur'
                ];
            }
        } catch (PDOException $e) {
            error_log("PDO Exception in updateUser: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            error_log("General Exception in updateUser: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur inattendue: ' . $e->getMessage()
            ];
        }
    }

    public function deleteUser($userId) {
        try {
            // Vérifier si l'utilisateur existe
            $checkStmt = $this->pdo->prepare("SELECT id FROM utilisateur WHERE id = ?");
            $checkStmt->execute([$userId]);
            if (!$checkStmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ];
            }

            $stmt = $this->pdo->prepare("DELETE FROM utilisateur WHERE id = ?");
            $result = $stmt->execute([$userId]);
            
            if ($result && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Utilisateur supprimé avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la suppression de l\'utilisateur'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erreur SQL lors de la suppression: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'utilisateur'
            ];
        }
    }
}
?>