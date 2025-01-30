<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Collisions </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100">
<div class="flex">
 <!-- Sidebar -->
 <div class="w-64 bg-white h-screen shadow-md">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Gestion Administrative</h1>
            </div>
            <nav class="mt-6">
                <ul>
                    <li>
                        <a href="gestion_users.php" class="flex items-center px-6 py-2 text-gray-700 hover:bg-gray-200">
                            <!-- <i class="fas fa-users mr-2"></i> -->
                            <i class="fas fa-users mr-2"></i>
                            Personnel
                        </a>
                    </li>
                    <li>
                        <a href="gestion_cars.php" class="flex items-center px-6 py-2 text-gray-700 hover:bg-gray-200">
                        <i class="fas fa-bus mr-2"></i>
                            Véhicule
                        </a>
                    </li>
                    <li>
                        <a href="accidents.php" class="flex items-center px-6 py-2 text-gray-700 bg-gray-200">
                            <i class="fas fa-car mr-2"></i>
                            Collision
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
</div>
    
</body>
</html>