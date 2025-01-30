<?php
require_once '../../models/fonctionnaire.php';
require_once '../../config/Database.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$fonctionnaire = new Fonctionnaire();
$message = '';
$success = true;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'matricule' => $_POST['matricule'],
                    'marque' => $_POST['marque'],
                    'ville' => $_POST['ville'],
                    'chauffeurs_id' => !empty($_POST['chauffeurs_id']) ? $_POST['chauffeurs_id'] : null,
                    'date_expiration_carte_grise' => $_POST['date_expiration_carte_grise'],
                    'date_expiration_visite' => $_POST['date_expiration_visite'],
                    'date_expiration_assurance' => $_POST['date_expiration_assurance'],
                    'date_expiration_vignette' => $_POST['date_expiration_vignette'],
                    'date_expiration_circulation' => $_POST['date_expiration_circulation'],
                    'date_expiration_extincteur' => $_POST['date_expiration_extincteur'],
                    'date_expiration_tachygraphe' => $_POST['date_expiration_tachygraphe'],
                    'status' => $_POST['status']
                ];
                
                $result = $fonctionnaire->createCar($data, $_FILES);
                if ($result['success']) {
                    $message = $result['message'];
                    $success = true;
                } else {
                    $message = $result['message'];
                    $success = false;
                }
                break;

            case 'edit':
                $data = [
                    'matricule' => $_POST['matricule'],
                    'marque' => $_POST['marque'],
                    'ville' => $_POST['ville'],
                    'chauffeurs_id' => !empty($_POST['chauffeurs_id']) ? $_POST['chauffeurs_id'] : null,
                    'date_expiration_carte_grise' => $_POST['date_expiration_carte_grise'],
                    'date_expiration_visite' => $_POST['date_expiration_visite'],
                    'date_expiration_assurance' => $_POST['date_expiration_assurance'],
                    'date_expiration_vignette' => $_POST['date_expiration_vignette'],
                    'date_expiration_circulation' => $_POST['date_expiration_circulation'],
                    'date_expiration_extincteur' => $_POST['date_expiration_extincteur'],
                    'date_expiration_tachygraphe' => $_POST['date_expiration_tachygraphe'],
                    'status' => $_POST['status']
                ];
                
                $result = $fonctionnaire->updateCar($_POST['old_matricule'], $data, $_FILES);
                if ($result['success']) {
                    $message = $result['message'];
                    $success = true;
                } else {
                    $message = $result['message'];
                    $success = false;
                }
                break;

            case 'delete':
                if (isset($_POST['matricule'])) {
                    $result = $fonctionnaire->deleteCar($_POST['matricule']);
                    if ($result['success']) {
                        $message = $result['message'];
                        $success = true;
                    } else {
                        $message = $result['message'];
                        $success = false;
                    }
                }
                break;
        }
    }
}

// Récupérer la liste des chauffeurs pour le formulaire
$chauffeurs = $fonctionnaire->getAllPersonnel()['data'] ?? [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Voitures</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            width: 90%;
            max-width: 800px;
            border-radius: 8px;
            max-height: 90vh;
            overflow-y: auto;
        }
        /* Style pour les inputs */
        input, select {
            border: 1px solid black !important;
        }
    </style>
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
                          
                            <i class="fas fa-users mr-2"></i>
                            Personnel
                        </a>
                    </li>
                    <li>
                        <a href="gestion_cars.php" class="flex items-center px-6 py-2 text-gray-700 bg-gray-200">
                            <!-- <i class="fas fa-car mr-2"></i> -->
                            <i class="fas fa-bus mr-2"></i>
                            Véhicule
                        </a>
                    </li>
                    <li>
                        <a href="accidents.php" class="flex items-center px-6 py-2 text-gray-700 hover:bg-gray-200">
                           
                            <i class="fas fa-car-crash mr-2"></i>
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

        <!-- Main Content -->
        <div class="flex-1 p-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestion des Véhicules</h2>
                <div class="flex items-center gap-4">
                    <input type="text" id="searchInput" placeholder="Rechercher..." 
                           class="px-4 py-2 border rounded-md">
                    <button onclick="openModal()" 
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i>Ajouter un véhicule
                    </button>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="mb-4 p-4 rounded-md <?php echo $success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Table des véhicules -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matricule</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marque</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chauffeur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            $cars = $fonctionnaire->getAllCars();
                            if ($cars['success'] && !empty($cars['data'])) {
                                foreach ($cars['data'] as $car) {
                                    $statusClass = match($car['status']) {
                                        'en service' => 'bg-green-100 text-green-800',
                                        'en panne' => 'bg-red-100 text-red-800',
                                        'en maintenance' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($car['matricule']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($car['marque']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($car['ville']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($car['chauffeur_nom'] ?? 'Non assigné'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($car['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <a href="view_car.php?matricule=<?php echo urlencode($car['matricule']); ?>" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    <i class="fa-solid fa-eye text-lg"></i>
                                                </a>
                                                <button onclick="openEditModal('<?php echo htmlspecialchars($car['matricule']); ?>')" 
                                                        class="text-yellow-600 hover:text-yellow-900">
                                                    <i class="fa-solid fa-pen-to-square text-lg"></i>
                                                </button>
                                                <button onclick="deleteCar('<?php echo htmlspecialchars($car['matricule']); ?>')" 
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fa-solid fa-trash text-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajout Voiture -->
    <div class="modal" id="addCarModal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Ajouter un véhicule</h3>
                <button type="button" class="btn-close text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addCarForm" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
                <input type="hidden" name="action" value="create">
                
                <!-- Informations de base -->
                <div class="col-span-2 grid grid-cols-3 gap-4">
                    <div>
                        <label for="matricule" class="block text-sm font-medium text-gray-700">Matricule</label>
                        <input type="text" id="matricule" name="matricule" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="marque" class="block text-sm font-medium text-gray-700">Marque</label>
                        <input type="text" id="marque" name="marque" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="ville" class="block text-sm font-medium text-gray-700">Ville</label>
                        <input type="text" id="ville" name="ville" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Status et Chauffeur -->
                <div>
                    <label for="chauffeurs_id" class="block text-sm font-medium text-gray-700">Chauffeur</label>
                    <select id="chauffeurs_id" name="chauffeurs_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Sélectionner un chauffeur</option>
                        <?php foreach($chauffeurs as $chauffeur): ?>
                            <option value="<?php echo $chauffeur['id']; ?>">
                                <?php echo htmlspecialchars($chauffeur['nom_complet']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="en service">En service</option>
                        <option value="en panne">En panne</option>
                        <option value="en maintenance">En maintenance</option>
                    </select>
                </div>

                <!-- Documents -->
                <div class="col-span-2">
                    <h4 class="font-medium text-gray-700 mb-2">Documents</h4>
                </div>

                <!-- Carte Grise -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Carte Grise (PDF)</label>
                    <input type="file" name="carte_grise" accept=".pdf" required 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" name="date_expiration_carte_grise" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Visite Technique -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Visite Technique (PDF)</label>
                    <input type="file" name="visite_technique" accept=".pdf" required 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" name="date_expiration_visite" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Assurance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Assurance (PDF)</label>
                    <input type="file" name="assurance" accept=".pdf" required 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" name="date_expiration_assurance" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Vignette -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Vignette (PDF)</label>
                    <input type="file" name="vignette" accept=".pdf" required 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" name="date_expiration_vignette" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Feuille de Circulation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Feuille de Circulation (PDF)</label>
                    <input type="file" name="feuille_circulation" accept=".pdf" required 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" name="date_expiration_circulation" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Feuille d'Extincteur -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Feuille d'Extincteur (PDF)</label>
                    <input type="file" name="feuille_extincteur" accept=".pdf" required 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" name="date_expiration_extincteur" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Feuille de Tachygraphe -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Feuille de Tachygraphe (PDF)</label>
                    <input type="file" name="feuille_tachygraphe" accept=".pdf" required 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" name="date_expiration_tachygraphe" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="col-span-2 flex justify-end mt-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Ajouter le véhicule
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Modification Voiture -->
    <div class="modal" id="editCarModal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Modifier un véhicule</h3>
                <button type="button" class="btn-close text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editCarForm" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="old_matricule" id="edit_old_matricule">
                
                <!-- Informations de base -->
                <div class="col-span-2 grid grid-cols-3 gap-4">
                    <div>
                        <label for="edit_matricule" class="block text-sm font-medium text-gray-700">Matricule</label>
                        <input type="text" id="edit_matricule" name="matricule" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="edit_marque" class="block text-sm font-medium text-gray-700">Marque</label>
                        <input type="text" id="edit_marque" name="marque" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="edit_ville" class="block text-sm font-medium text-gray-700">Ville</label>
                        <input type="text" id="edit_ville" name="ville" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Status et Chauffeur -->
                <div>
                    <label for="edit_status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="edit_status" name="status" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="en service">En service</option>
                        <option value="en panne">En panne</option>
                        <option value="en maintenance">En maintenance</option>
                    </select>
                </div>

                <div>
                    <label for="edit_chauffeurs_id" class="block text-sm font-medium text-gray-700">Chauffeur</label>
                    <select id="edit_chauffeurs_id" name="chauffeurs_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Sélectionner un chauffeur</option>
                        <?php foreach($chauffeurs as $chauffeur): ?>
                            <option value="<?php echo $chauffeur['id']; ?>">
                                <?php echo htmlspecialchars($chauffeur['nom_complet']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Documents -->
                <div class="col-span-2">
                    <h4 class="font-medium text-gray-700 mb-2">Documents</h4>
                </div>

                <!-- Carte Grise -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Carte Grise (PDF)</label>
                    <input type="file" name="carte_grise" accept=".pdf" 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" id="edit_date_expiration_carte_grise" name="date_expiration_carte_grise" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Visite Technique -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Visite Technique (PDF)</label>
                    <input type="file" name="visite_technique" accept=".pdf" 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" id="edit_date_expiration_visite" name="date_expiration_visite" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Assurance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Assurance (PDF)</label>
                    <input type="file" name="assurance" accept=".pdf" 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" id="edit_date_expiration_assurance" name="date_expiration_assurance" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Vignette -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Vignette (PDF)</label>
                    <input type="file" name="vignette" accept=".pdf" 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" id="edit_date_expiration_vignette" name="date_expiration_vignette" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Feuille de Circulation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Feuille de Circulation (PDF)</label>
                    <input type="file" name="feuille_circulation" accept=".pdf" 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" id="edit_date_expiration_circulation" name="date_expiration_circulation" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Feuille d'Extincteur -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Feuille d'Extincteur (PDF)</label>
                    <input type="file" name="feuille_extincteur" accept=".pdf" 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" id="edit_date_expiration_extincteur" name="date_expiration_extincteur" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Tachygraphe -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tachygraphe (PDF)</label>
                    <input type="file" name="tachygraphe" accept=".pdf" 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                    <input type="date" id="edit_date_expiration_tachygraphe" name="date_expiration_tachygraphe" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Bouton de soumission -->
                <div class="col-span-2 flex justify-end mt-4">
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Suppression -->
    <div class="modal" id="deleteCarModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette voiture ?</p>
                <form id="deleteCarForm" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="matricule" id="delete_matricule">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fonction de recherche
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Fonctions pour les modals
        function openModal() {
            document.getElementById('addCarModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addCarModal').style.display = 'none';
        }

        // Gestionnaire pour fermer les modals en cliquant à l'extérieur
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Gestionnaire pour les boutons de fermeture
        document.querySelectorAll('.btn-close').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.modal').style.display = 'none';
            });
        });

        function openEditModal(matricule) {
            // Récupérer les données du véhicule
            $.get('get_car.php', { matricule: matricule }, function(data) {
                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.error
                    });
                    return;
                }
                
                // Remplir le formulaire avec les données
                $('#edit_old_matricule').val(data.matricule);
                $('#edit_matricule').val(data.matricule);
                $('#edit_marque').val(data.marque);
                $('#edit_ville').val(data.ville);
                $('#edit_status').val(data.status);
                $('#edit_chauffeurs_id').val(data.chauffeurs_id);
                
                // Dates d'expiration
                $('#edit_date_expiration_carte_grise').val(data.date_expiration_carte_grise);
                $('#edit_date_expiration_visite').val(data.date_expiration_visite);
                $('#edit_date_expiration_assurance').val(data.date_expiration_assurance);
                $('#edit_date_expiration_vignette').val(data.date_expiration_vignette);
                $('#edit_date_expiration_circulation').val(data.date_expiration_circulation);
                $('#edit_date_expiration_extincteur').val(data.date_expiration_extincteur);
                $('#edit_date_expiration_tachygraphe').val(data.date_expiration_tachygraphe);
                
                // Afficher le modal
                document.getElementById('editCarModal').style.display = 'block';
            });
        }

        function deleteCar(matricule) {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Cette action est irréversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Créer et soumettre le formulaire de suppression
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.style.display = 'none';

                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'delete';

                    const matriculeInput = document.createElement('input');
                    matriculeInput.type = 'hidden';
                    matriculeInput.name = 'matricule';
                    matriculeInput.value = matricule;

                    form.appendChild(actionInput);
                    form.appendChild(matriculeInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        <?php if ($message): ?>
            // Afficher le message après le chargement de la page
            window.onload = function() {
                Swal.fire({
                    icon: '<?php echo $success ? 'success' : 'error' ?>',
                    title: '<?php echo $message ?>',
                    showConfirmButton: true
                });
            };
        <?php endif; ?>
    </script>
</body>
</html>