<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/fonctionnaire.php';

// Initialiser la connexion à la base de données
$database = new Database();
$db = $database->connect();
$fonctionnaire = new Fonctionnaire();
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
        /* Ajout du style pour les inputs */
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
                <button onclick="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    <i class="fas fa-plus mr-2"></i>Ajouter un membre
                </button>
            </div>

            <!-- Table du personnel -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom Complet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'embauche</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="personnelTableBody">
                        <!-- Les données seront chargées ici via JavaScript -->
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
                    <label class="block text-sm font-medium text-gray-700">Photo</label>
                    <input type="file" name="photo" accept="image/*" class="mt-1 block w-full">
                </div>

                <div class="col-span-2 mt-4">
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Ajouter le membre
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function openModal() {
            document.getElementById('createPersonnelModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('createPersonnelModal').style.display = 'none';
        }

        // Fermer le modal si on clique en dehors
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                closeModal();
            }
        }

        // Gérer la soumission du formulaire
        $(document).ready(function() {
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
    </script>
</body>
</html>