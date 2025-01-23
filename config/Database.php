<?php 
    class Database {
        private $host = 'localhost';   
        private $dbname = 'iridan3t';   
        private $username = 'root';    
        private $password = '';        
        private $conn;

        public function connect() {
            try {
                // Cree une nouvelle connexion PDO
                $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $this->conn;  // Retourne la connexion
            } catch (PDOException $e) {
                echo "Erreur de connexion : " . $e->getMessage();
                return null;
            }
        }
    }

?>