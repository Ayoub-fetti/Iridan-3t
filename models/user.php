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
}
?>