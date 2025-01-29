<?php
require_once __DIR__ . '/../../models/fonctionnaire.php';
require_once __DIR__ . '/../../config/Database.php';
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
                $result = $fonctionnaire->createUser(
                    $_POST['full_name'],
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['role']
                );
                if ($result['success']) {
                    $message = $result['message'];
                    $success = true;
                } else {
                    $message = $result['message'];
                    $success = false;
                }
                break;

            case 'edit':
                $result = $fonctionnaire->updateUser(
                    $_POST['id'],
                    $_POST['full_name'],
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['role']
                );
                if ($result['success']) {
                    $message = $result['message'];
                    $success = true;
                } else {
                    $message = $result['message'];
                    $success = false;
                }
                break;

            case 'delete':
                if (isset($_POST['id'])) {
                    $result = $fonctionnaire->deleteUser($_POST['id']);
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

// Initialiser la connexion à la base de données
$database = new Database();
$db = $database->connect();
?>

<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Gestion du Personnel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
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
        /* Ajout du style pour le conteneur du tableau */
        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-top: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
        }
        /* Style pour rendre le tableau plus compact */
        .compact-table td, .compact-table th {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem;
        }
        /* Style pour les en-têtes fixes */
        .table-container thead th {
            position: sticky;
            top: 0;
            background-color: #f9fafb;
            z-index: 1;
        }
        /* Style pour la photo de profil */
        .profile-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
        }
        /* Style pour la cellule de la photo */
        .photo-cell {
            width: 70px;
            text-align: center;
        }
        /* Ajout des styles pour les inputs */
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
                <h1 class="text-2xl font-bold">Gestion RH</h1>
            </div>
            <nav class="mt-6">
                <ul>
                    <li>
                        <a href="gestion_users.php" class="flex items-center px-6 py-2 text-gray-700 bg-gray-200">
                            <i class="fas fa-users mr-2"></i>
                            Personnel
                        </a>
                    </li>
                    <li>
                        <a href="gestion_cars.php" class="flex items-center px-6 py-2 text-gray-700 hover:bg-gray-200">
                            <i class="fas fa-users mr-2"></i>
                             Véhicule
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
                <h2 class="text-2xl font-bold">Gestion du Personnel</h2>
                <div class="flex items-center gap-4">
                    <input type="text" id="searchInput" placeholder="Rechercher..." class="px-4 py-2 border rounded-md">
                    <button onclick="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i>Ajouter un membre
                    </button>
                </div>
            </div>

            <!-- Table du personnel -->
            <div class="table-container">
                <table class="min-w-full divide-y divide-gray-200 compact-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider photo-cell">Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom Complet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Situation Familiale</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adresse</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'embauche</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de démission</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date exp. CI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date exp. Permis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date exp. Visite</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $result = $fonctionnaire->getAllPersonnel();
                        if ($result['success']) {
                            foreach ($result['data'] as $person) {
                                ?>
                                <tr data-id="<?php echo htmlspecialchars($person['id'] ?? ''); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap photo-cell">
                                        <?php if (!empty($person['photo'])) : ?>
                                            <img src="../../<?php echo htmlspecialchars($person['photo'] ?? ''); ?>" alt="Photo de profil" class="profile-photo">
                                        <?php else : ?>
                                            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1MCIgaGVpZ2h0PSI1MCIgdmlld0JveD0iMCAwIDUwIDUwIj48Y2lyY2xlIGN4PSIyNSIgY3k9IjE4IiByPSI4IiBmaWxsPSIjZDFkNWRiIi8+PHBhdGggZD0iTTQyLDQ5SDhjLTEuMTA0LDAtMi0wLjg5Ni0yLTJWNDJjMC04LjI4NCw2LjcxNi0xNSwxNS0xNWgxMGM4LjI4NCwwLDE1LDYuNzE2LDE1LDE1djVDNDYsNDguMTA0LDQ1LjEwNCw0OSw0NCw0OXoiIGZpbGw9IiNkMWQ1ZGIiLz48L3N2Zz4=" alt="Photo par défaut" class="profile-photo">
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['nom_complet'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['role'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['situation_familiale'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['ville'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['adresse'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['date_embauche'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['date_demission'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['date_expiration_carte'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['date_expiration_permit'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['date_expiration_visite'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (!empty($person['carte_identite'])) : ?>
                                            <a href="../../<?php echo htmlspecialchars($person['carte_identite'] ?? ''); ?>" target="_blank" class="text-blue-600 hover:text-blue-900 mr-2" title="Carte d'identité">
                                                <i class="fas fa-id-card"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($person['contrat'])) : ?>
                                            <a href="../../<?php echo htmlspecialchars($person['contrat'] ?? ''); ?>" target="_blank" class="text-blue-600 hover:text-blue-900 mr-2" title="Contrat">
                                                <i class="fas fa-file-contract"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($person['permit_conduire'])) : ?>
                                            <a href="../../<?php echo htmlspecialchars($person['permit_conduire'] ?? ''); ?>" target="_blank" class="text-blue-600 hover:text-blue-900 mr-2" title="Permis de conduire">
                                                <i class="fas fa-id-card"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($person['visite_medicale'])) : ?>
                                            <a href="../../<?php echo htmlspecialchars($person['visite_medicale'] ?? ''); ?>" target="_blank" class="text-blue-600 hover:text-blue-900" title="Visite médicale">
                                                <i class="fas fa-file-medical"></i>
                                            </a>
                                        <?php endif; ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <a href="#" class="text-green-600 hover:text-green-900 mr-2" onclick="openEditModal(<?php echo htmlspecialchars($person['id']); ?>)" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="deletePersonnel(<?php echo htmlspecialchars($person['id'] ?? ''); ?>)" class="text-red-600 hover:text-red-900" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="13" class="px-6 py-4 text-center text-red-500">Erreur: ' . htmlspecialchars($result['message']) . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout -->
    <div id="createPersonnelModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Ajouter un membre du personnel</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="createPersonnelForm" class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nom Complet</label>
                    <input type="text" name="nom_complet" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Carte d'identité (PDF)</label>
                    <input type="file" name="carte_identite_file" accept=".pdf" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration CI</label>
                    <input type="date" name="date_expiration_carte" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Rôle</label>
                    <select name="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="fonctionnaire">Fonctionnaire</option>
                        <option value="chauffeurs">Chauffeur</option>
                        <option value="chef de zone">Chef de zone</option>
                        <option value="chef de site">Chef de site</option>
                        <option value="menage">Ménage</option>
                        <option value="securite">Sécurité</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Situation Familiale</label>
                    <select name="situation_familiale" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="celibataire">Célibataire</option>
                        <option value="marier">Marié(e)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Ville</label>
                    <input type="text" name="ville" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Adresse</label>
                    <input type="text" name="adresse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contrat (PDF)</label>
                    <input type="file" name="contrat_file" accept=".pdf" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'embauche</label>
                    <input type="date" name="date_embauche" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date de démission</label>
                    <input type="date" name="date_demission" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="col-span-2">
                    <h4 class="font-medium text-gray-700 mb-2">Informations supplémentaires</h4>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Permis de conduire (PDF)</label>
                    <input type="file" name="permit_conduire_file" accept=".pdf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration permis</label>
                    <input type="date" name="date_expiration_permit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Visite médicale (PDF)</label>
                    <input type="file" name="visite_medicale_file" accept=".pdf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration visite</label>
                    <input type="date" name="date_expiration_visite" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Photo de profil</label>
                    <input type="file" name="photo" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="col-span-2 mt-4">
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de modification -->
    <div id="editPersonnelModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Modifier un membre du personnel</h3>
                <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editPersonnelForm" class="grid grid-cols-2 gap-4">
                <input type="hidden" name="id" id="edit_id">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nom Complet</label>
                    <input type="text" name="nom_complet" id="edit_nom_complet" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">CIN</label>
                    <input type="text" name="cin" id="edit_cin" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Photo</label>
                    <input type="file" name="photo" id="edit_photo" accept="image/*,.pdf" class="mt-1 block w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Diplôme</label>
                    <input type="file" name="diplome" id="edit_diplome" accept=".pdf" class="mt-1 block w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">CV</label>
                    <input type="file" name="cv" id="edit_cv" accept=".pdf" class="mt-1 block w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contrat</label>
                    <input type="file" name="contrat" id="edit_contrat" accept=".pdf" class="mt-1 block w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Visite médicale</label>
                    <input type="file" name="visite_medicale" id="edit_visite_medicale" accept=".pdf" class="mt-1 block w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date de naissance</label>
                    <input type="date" name="date_naissance" id="edit_date_naissance" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lieu de naissance</label>
                    <input type="text" name="lieu_naissance" id="edit_lieu_naissance" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Situation familiale</label>
                    <select name="situation_familiale" id="edit_situation_familiale" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="Célibataire">Célibataire</option>
                        <option value="Marié(e)">Marié(e)</option>
                        <option value="Divorcé(e)">Divorcé(e)</option>
                        <option value="Veuf(ve)">Veuf(ve)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre d'enfants</label>
                    <input type="number" name="nombre_enfants" id="edit_nombre_enfants" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date de recrutement</label>
                    <input type="date" name="date_recrutement" id="edit_date_recrutement" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'affectation</label>
                    <input type="date" name="date_affectation" id="edit_date_affectation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration visite</label>
                    <input type="date" name="date_expiration_visite" id="edit_date_expiration_visite" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="col-span-2">
                    <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openModal() {
            document.getElementById('createPersonnelModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('createPersonnelModal').style.display = 'none';
        }

        // Fermer le modal si on clique en dehors
        window.onclick = function(event) {
            if (event.target == document.getElementById('createPersonnelModal')) {
                closeModal();
            }
        }

        // Fonction de recherche
        $(document).ready(function() {
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            $('#createPersonnelForm').on('submit', function(e) {
                e.preventDefault();
                
                let formData = new FormData(this);
                
                $.ajax({
                    url: '../../controllers/personnel/create.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.success) {
                            alert('Personnel ajouté avec succès!');
                            closeModal();
                            location.reload(); // Recharger la page pour voir les changements
                        } else {
                            alert('Erreur: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Une erreur est survenue lors de la communication avec le serveur');
                    }
                });
            });
        });

        function deletePersonnel(id) {
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
                    $.ajax({
                        url: '../../controllers/personnel/delete.php',
                        type: 'POST',
                        data: { id: id },
                        success: function(response) {
                            if (response.success) {
                                // Supprimer la ligne du tableau
                                $(`tr[data-id="${id}"]`).fadeOut(400, function() {
                                    $(this).remove();
                                });
                                Swal.fire(
                                    'Supprimé!',
                                    'L\'utilisateur a été supprimé avec succès.',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Erreur!',
                                    response.message || 'Une erreur est survenue lors de la suppression.',
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Erreur!',
                                'Une erreur est survenue lors de la communication avec le serveur.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Fonction pour ouvrir le modal de modification
        window.openEditModal = function(id) {
            // Récupérer les données de la ligne
            const row = $(`tr[data-id="${id}"]`);
            
            // Remplir le formulaire avec les données existantes
            $('#edit_id').val(id);
            $('#edit_nom_complet').val(row.find('td:eq(1)').text().trim());
            $('#edit_cin').val(row.find('td:eq(2)').text().trim());
            $('#edit_date_naissance').val(row.find('td:eq(3)').text().trim());
            $('#edit_lieu_naissance').val(row.find('td:eq(4)').text().trim());
            $('#edit_situation_familiale').val(row.find('td:eq(5)').text().trim());
            $('#edit_nombre_enfants').val(row.find('td:eq(6)').text().trim());
            $('#edit_date_recrutement').val(row.find('td:eq(7)').text().trim());
            $('#edit_date_affectation').val(row.find('td:eq(8)').text().trim());
            $('#edit_date_expiration_visite').val(row.find('td:eq(9)').text().trim());
            
            // Afficher le modal
            $('#editPersonnelModal').show();
        }

        // Fonction pour fermer le modal de modification
        window.closeEditModal = function() {
            $('#editPersonnelModal').hide();
        }

        // Gérer la soumission du formulaire de modification
        $('#editPersonnelForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            $.ajax({
                url: '../../controllers/personnel/update.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Succès!',
                            text: 'Personnel mis à jour avec succès',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Erreur!',
                            text: response.message || 'Une erreur est survenue',
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Erreur!',
                        text: 'Une erreur est survenue',
                        icon: 'error'
                    });
                }
            });
        });
    </script>
</body>
</html>