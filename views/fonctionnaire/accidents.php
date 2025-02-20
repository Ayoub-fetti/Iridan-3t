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

// Récupérer la liste des voitures et des chauffeurs pour le formulaire
$cars = $fonctionnaire->getAllCars()['data'] ?? [];
$chauffeurs = $fonctionnaire->getChauffeurs()['data'] ?? [];
$accidents = $fonctionnaire->getAllAccidents()['data'] ?? [];

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'cars_id' => $_POST['cars_id'],
                    'chauffeurs_id' => $_POST['chauffeurs_id'],
                    'date_declaration_assurance' => $_POST['date_declaration_assurance'],
                    'procedure' => $_POST['procedure'],
                    'status_resolution' => $_POST['status_resolution'],
                    'commentaire' => $_POST['commentaire'],
                    'date_accident' => $_POST['date_accident'],
                    'date_reparation' => $_POST['date_reparation']
                ];  
                
                $result = $fonctionnaire->createAccident($data);
                if ($result['success']) {
                    $message = $result['message'];
                    $success = true;
                    $accidents = $fonctionnaire->getAllAccidents()['data'] ?? [];
                } else {
                    $message = $result['message'];
                    $success = false;
                }
                break;

            case 'update':
                $data = [
                    'cars_id' => $_POST['cars_id'],
                    'chauffeurs_id' => $_POST['chauffeurs_id'],
                    'date_declaration_assurance' => $_POST['date_declaration_assurance'],
                    'procedure' => $_POST['procedure'],
                    'status_resolution' => $_POST['status_resolution'],
                    'commentaire' => $_POST['commentaire'],
                    'date_accident' => $_POST['date_accident'],
                    'date_reparation' => $_POST['date_reparation']
                ];
                
                $result = $fonctionnaire->updateAccident($_POST['accident_id'], $data);
                if ($result['success']) {
                    $message = $result['message'];
                    $success = true;
                    $accidents = $fonctionnaire->getAllAccidents()['data'] ?? [];
                } else {
                    $message = $result['message'];
                    $success = false;
                }
                break;

            case 'delete':
                $result = $fonctionnaire->deleteAccident($_POST['accident_id']);
                if ($result['success']) {
                    $message = $result['message'];
                    $success = true;
                    $accidents = $fonctionnaire->getAllAccidents()['data'] ?? [];
                } else {
                    $message = $result['message'];
                    $success = false;
                }
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Collisions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .modal.active {
            display: block !important;
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 1.5rem;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .grid-1 {
            grid-column: span 2;
        }
        .input-group {
            margin-bottom: 0.75rem;
        }
        .input-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #374151;
        }
        .input-group select,
        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1rem;
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
                        <a href="gestion_cars.php" class="flex items-center px-6 py-2 text-gray-700 hover:bg-gray-200">
                        <i class="fas fa-bus mr-2"></i>
                            Véhicules
                        </a>
                    </li>
                    <li>
                        <a href="accidents.php" class="flex items-center px-6 py-2 bg-gray-200 text-gray-700">
                            <i class="fas fa-car-crash mr-2"></i>
                            Collisions
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

        <!-- Content -->
        <div class="flex-1 p-10">
            <?php if (!empty($message)): ?>
                <div class="mb-4 <?php echo $success ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestion des Collisions</h2>
                <div class="flex items-center gap-4">
                    <input type="text" id="searchInput" placeholder="Rechercher..." class="px-4 py-2 border rounded-md">
                    <button onclick="openModal('addAccidentModal')" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i>Ajouter
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chauffeur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Déclaration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Procédure</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Accident</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Reparation</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($accidents as $accident): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($accident['matricule_vehicule']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($accident['nom_chauffeur']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($accident['date_declaration_assurance']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($accident['procédure']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($accident['status_resolution']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($accident['commentaire']); ?></td>  
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($accident['date_accident']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($accident['date_reparation']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($accident)); ?>)" class="text-blue-600 hover:text-blue-900 mr-2" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteAccident(<?php echo htmlspecialchars($accident['id']); ?>)" class="text-red-600 hover:text-red-900" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajout Accident -->
    <div class="modal" id="addAccidentModal">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-4">Ajouter un Accident</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="grid-2">
                    <div class="input-group">
                        <label for="cars_id">Véhicule</label>
                        <select name="cars_id" id="cars_id" required>
                            <option value="">Sélectionner un véhicule</option>
                            <?php foreach ($cars as $car): ?>
                                <option value="<?php echo htmlspecialchars($car['matricule']); ?>">
                                    <?php echo htmlspecialchars($car['matricule'] . ' - ' . $car['marque']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="chauffeurs_id">Chauffeur</label>
                        <select name="chauffeurs_id" id="chauffeurs_id" required>
                            <option value="">Sélectionner un chauffeur</option>
                            <?php foreach ($chauffeurs as $chauffeur): ?>
                                <option value="<?php echo htmlspecialchars($chauffeur['id']); ?>">
                                    <?php echo htmlspecialchars($chauffeur['nom_complet']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="date_declaration_assurance">Date Déclaration</label>
                        <input type="date" name="date_declaration_assurance" id="date_declaration_assurance" required>
                    </div>

                    <div class="input-group">
                        <label for="procedure">Procédure</label>
                        <select name="procedure" id="procedure" required>
                            <option value="normal">Normal</option>
                            <option value="forfait">Forfait</option>
                            <option value="garage a greyer">Garage à greyer</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="status_resolution">Statut</label>
                        <select name="status_resolution" id="status_resolution" required>
                            <option value="pv">PV</option>
                            <option value="constat">Constat</option>
                            <option value="arrangement">Arrangement</option>
                        </select>
                    </div>

                    <div class="input-group grid-1">
                        <label for="commentaire">Commentaire</label>
                        <textarea name="commentaire" id="commentaire" rows="2"></textarea>
                    </div>

                    <div class="input-group">
                        <label for="date_accident">Date Accident</label>
                        <input type="date" name="date_accident" id="date_accident" required>
                    </div>

                    <div class="input-group">
                        <label for="date_reparation">Date Reparation</label>
                        <input type="date" name="date_reparation" id="date_reparation" required>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" onclick="closeModal('addAccidentModal')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        <i class="fas fa-times mr-2"></i>Fermer
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Modifier Accident -->
    <div class="modal" id="editAccidentModal">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-4">Modifier un Accident</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="accident_id" id="edit_accident_id">
                
                <div class="grid-2">
                    <div class="input-group">
                        <label for="edit_cars_id">Véhicule</label>
                        <select name="cars_id" id="edit_cars_id" required>
                            <option value="">Sélectionner un véhicule</option>
                            <?php foreach ($cars as $car): ?>
                                <option value="<?php echo htmlspecialchars($car['matricule']); ?>">
                                    <?php echo htmlspecialchars($car['matricule'] . ' - ' . $car['marque']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="edit_chauffeurs_id">Chauffeur</label>
                        <select name="chauffeurs_id" id="edit_chauffeurs_id" required>
                            <option value="">Sélectionner un chauffeur</option>
                            <?php foreach ($chauffeurs as $chauffeur): ?>
                                <option value="<?php echo htmlspecialchars($chauffeur['id']); ?>">
                                    <?php echo htmlspecialchars($chauffeur['nom_complet']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="edit_date_declaration_assurance">Date Déclaration</label>
                        <input type="date" name="date_declaration_assurance" id="edit_date_declaration_assurance" required>
                    </div>

                    <div class="input-group">
                        <label for="edit_procedure">Procédure</label>
                        <select name="procedure" id="edit_procedure" required>
                            <option value="normal">Normal</option>
                            <option value="forfait">Forfait</option>
                            <option value="garage a greyer">Garage à greyer</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="edit_status_resolution">Statut</label>
                        <select name="status_resolution" id="edit_status_resolution" required>
                            <option value="pv">PV</option>
                            <option value="constat">Constat</option>
                            <option value="arrangement">Arrangement</option>
                        </select>
                    </div>

                    <div class="input-group grid-1">
                        <label for="edit_commentaire">Commentaire</label>
                        <textarea name="commentaire" id="edit_commentaire" rows="2"></textarea>
                    </div>

                    <div class="input-group">
                        <label for="edit_date_accident">Date Accident</label>
                        <input type="date" name="date_accident" id="edit_date_accident" required>
                    </div>

                    <div class="input-group">
                        <label for="edit_date_reparation">Date Reparation</label>
                        <input type="date" name="date_reparation" id="edit_date_reparation" required>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" onclick="closeModal('editAccidentModal')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        <i class="fas fa-times mr-2"></i>Fermer
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

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
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'block';
            modal.classList.add('active');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'none';
            modal.classList.remove('active');
        }

        // Fonction pour ouvrir le modal de modification
        function openEditModal(accident) {
            document.getElementById('edit_accident_id').value = accident.id;
            document.getElementById('edit_cars_id').value = accident.cars_id;
            document.getElementById('edit_chauffeurs_id').value = accident.chauffeurs_id;
            document.getElementById('edit_date_declaration_assurance').value = accident.date_declaration_assurance;
            document.getElementById('edit_procedure').value = accident.procedure;
            document.getElementById('edit_status_resolution').value = accident.status_resolution;
            document.getElementById('edit_commentaire').value = accident.commentaire || '';
            document.getElementById('edit_date_accident').value = accident.date_accident;
            document.getElementById('edit_date_reparation').value = accident.date_reparation;
            
            openModal('editAccidentModal');
        }

        // Fonction pour supprimer un accident
        function deleteAccident(id) {
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
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="accident_id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Afficher les messages de succès/erreur avec SweetAlert2
        <?php if (!empty($message)): ?>
            Swal.fire({
                icon: '<?php echo $success ? 'success' : 'error'; ?>',
                title: '<?php echo $success ? 'Succès!' : 'Erreur!'; ?>',
                text: '<?php echo addslashes($message); ?>',
                timer: 2000,
                showConfirmButton: false
            });
        <?php endif; ?>
    </script>
</body>
</html>