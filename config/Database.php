<?php 
    class Database {
        private $host = 'localhost';   
        private $dbname = 'iridan3t';   
        private $username = 'root';    
        private $password = '';        
        private $conn;

        public function connect() {
            try {
                if ($this->conn !== null) {
                    return $this->conn;
                }

                $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
                return $this->conn;

            } catch (PDOException $e) {
                error_log("Erreur de connexion à la base de données: " . $e->getMessage());
                throw new Exception("Impossible de se connecter à la base de données");
            }
        }

        public function getConnection() {
            return $this->connect();
        }
    }

?>