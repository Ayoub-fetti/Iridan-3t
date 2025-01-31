<?php
require_once 'check_admin.php';
require_once '../../models/user.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$totalUsers = $user->getTotalUsers();
$totalPersonnel = $user->getTotalPersonnel();
$totalVehicules = $user->getTotalVehicules();
$totalAccidents = $user->getTotalAccidents();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Tableau de bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white h-screen shadow-md">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Bienvenue Admin </h1>
            </div>
            <nav class="mt-6">
                <ul>
                    <li>
                        <a href="admin_stat.php" class="flex items-center px-6 py-2 text-gray-700 hover:bg-gray-200 <?php echo basename($_SERVER['PHP_SELF']) === 'admin_stat.php' ? 'bg-gray-200' : ''; ?>">
                            <i class="fas fa-chart-line mr-2"></i>
                            Statistiques
                        </a>
                    </li>
                    <li>
                        <a href="admin_dash.php" class="flex items-center px-6 py-2 text-gray-700 hover:bg-gray-200 <?php echo basename($_SERVER['PHP_SELF']) === 'admin_dash.php' ? 'bg-gray-200' : ''; ?>">
                            <i class="fas fa-user-circle mr-2"></i>
                            Authentification
                        </a>
                    </li>
                    <li>
                        <a href="../auth/logout.php" class="flex items-center px-6 py-2 text-gray-700 hover:bg-red-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Déconnexion
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">


            <h2 class="text-3xl font-bold mb-8">Statistiques Générales</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Carte Utilisateurs -->
                <div class="bg-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-xl font-semibold text-gray-700">Comptes</div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-users text-blue-500"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-800"><?php echo $totalUsers; ?></div>
                    <div class="text-sm text-gray-500 mt-2">Total des utilisateurs de la plateforme</div>
                </div>

                <!-- Carte Personnel -->
                <div class="bg-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-xl font-semibold text-gray-700">Personnels</div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-user-tie text-green-500"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-800"><?php echo $totalPersonnel; ?></div>
                    <div class="text-sm text-gray-500 mt-2">Total  des personnels</div>
                </div>

                <!-- Carte Véhicules -->
                <div class="bg-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-xl font-semibold text-gray-700">Véhicules</div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-car text-yellow-500"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-800"><?php echo $totalVehicules; ?></div>
                    <div class="text-sm text-gray-500 mt-2">Total des véhicules</div>
                </div>

                <!-- Carte Accidents -->
                <div class="bg-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-xl font-semibold text-gray-700">Accidents</div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <i class="fas fa-car-crash text-red-500"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-800"><?php echo $totalAccidents; ?></div>
                    <div class="text-sm text-gray-500 mt-2">Total des accidents</div>
                </div>
            </div>
            <!-- Message de bienvenue -->
                <div class="mt-12 bg-white rounded-lg shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Bienvenue sur la plateforme d'administration IridanParc !</h3>
                    <p class="text-gray-600 leading-relaxed">
                    En tant qu'administrateur, vous avez un rôle essentiel dans la gestion des utilisateurs et le bon fonctionnement du système.
                    Une fois connecté, vous pouvez créer des comptes pour les fonctionnaires de l'administration afin de leur donner accès aux fonctionnalités de la plateforme.
                    Vous avez aussi la possibilité de modifier ou de supprimer des comptes existants selon les besoins. De plus, un tableau de bord détaillé vous permet de consulter des statistiques globales pour suivre l'activité et optimiser la gestion.
                   <br> Cependant, en tant qu'administrateur, vous ne pouvez pas accéder aux fonctionnalités de la plateforme. Toutefois, vous avez la possibilité de créer un compte avec le rôle de fonctionnaire pour bénéficier d'une meilleure expérience et d'un traitement optimisé du système.
                    Votre rôle est clé pour assurer une administration efficace et sécurisée. Bonne gestion !
                    </p>
                </div>
        </div>
    </div>
</body>
</html>