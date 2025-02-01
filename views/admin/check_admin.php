<?php
session_start();
require_once '../../models/user.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['user_id']) || $user->getRole() == 'fonctionnaire') {
    // Rediriger vers la page de connexion
    header('Location: ../auth/login.php');
    exit();
}
?>
