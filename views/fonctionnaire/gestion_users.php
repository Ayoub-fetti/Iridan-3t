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
                $data = [
                    'nom_complet' => $_POST['nom_complet'] ?? '',
                    'carte_identite' => $_POST['carte_identite'] ?? '',
                    'date_expiration_carte' => $_POST['date_expiration_carte'] ?? null,
                    'role' => $_POST['role'] ?? '',
                    'situation_familiale' => $_POST['situation_familiale'] ?? '',
                    'ville' => $_POST['ville'] ?? '',
                    'adresse' => $_POST['adresse'] ?? '',
                    'contrat' => $_POST['contrat'] ?? '',
                    'type_contract' => $_POST['type_contract'] ?? '',
                    'date_embauche' => $_POST['date_embauche'] ?? null,
                    //'date_demission' => !empty($_POST['date_demission']) ? $_POST['date_demission'] : null,
                    'permit_conduire' => $_POST['permit_conduire'] ?? '',
                    'date_expiration_permit' => $_POST['date_expiration_permit'] ?? null,
                    'visite_medicale' => $_POST['visite_medicale'] ?? '',
                    'date_expiration_visite' => $_POST['date_expiration_visite'] ?? null,
                    'photo' => $_POST['photo'] ?? ''
                ];
                $result = $fonctionnaire->createPersonnel($data);
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
                    'id' => $_POST['id'] ?? '',
                    'nom_complet' => $_POST['nom_complet'] ?? '',
                    'carte_identite' => $_POST['carte_identite'] ?? '',
                    'date_expiration_carte' => $_POST['date_expiration_carte'] ?? null,
                    'role' => $_POST['role'] ?? '',
                    'situation_familiale' => $_POST['situation_familiale'] ?? '',
                    'ville' => $_POST['ville'] ?? '',
                    'adresse' => $_POST['adresse'] ?? '',
                    'contrat' => $_POST['contrat'] ?? '',
                    'type_contract' => $_POST['type_contract'] ?? '',
                    'date_embauche' => $_POST['date_embauche'] ?? null,
                    'date_demission' => !empty($_POST['date_demission']) ? $_POST['date_demission'] : null,
                    'permit_conduire' => $_POST['permit_conduire'] ?? '',
                    'date_expiration_permit' => $_POST['date_expiration_permit'] ?? null,
                    'visite_medicale' => $_POST['visite_medicale'] ?? '',
                    'date_expiration_visite' => $_POST['date_expiration_visite'] ?? null,
                    'photo' => $_POST['photo'] ?? ''
                ];
                $result = $fonctionnaire->updatePersonnel($data['id'], $data);
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
                    $result = $fonctionnaire->deletePersonnel($_POST['id']);
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
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
        .table-container {
            overflow-x: auto;
        }
        .compact-table {
            table-layout: fixed;
            width: 100%;
        }
        .compact-table th,
        .compact-table td {
            max-width: 150px;
            overflow-wrap: break-word;
            word-wrap: break-word;
            white-space: normal;
            padding: 8px;
            vertical-align: top;
        }
        .compact-table th {
            font-size: 0.65rem;  /* Slightly smaller font size */
            padding: 4px 6px;    /* Slightly reduced padding */
            text-transform: uppercase;
            font-weight: 500;
            color: #6b7280;
            white-space: nowrap; /* Prevent text wrapping */
            overflow: visible;   /* Allow text to be fully visible */
        }
        .photo-cell {
            width: 75px;
        }
        .action-cell {
            width: 80px;
        }
        .role-cell {
            width: 130px;  
        }
        .famille-cell {
            width: 140px;  /* Slightly wider to accommodate the header text */
        }

        .profile-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
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
                        <a href="gestion_users.php" class="flex items-center px-6 py-2 text-gray-700 bg-gray-200">
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
                        <a href="accidents.php" class="flex items-center px-6 py-2 text-gray-700 hover:bg-gray-200">
                        <i class="fas fa-car-crash mr-2"></i>
                            Collision
                        </a>
                    </li>
                    <li>
                        <a href="personnel_embauche.php" class="flex items-center px-6 py-2 text-gray-700">
                            <i class="fas fa-user-clock mr-2"></i>
                            Personnel Embauché
                        </a>
                    </li>
                    <li>
                        <a href="../auth/logout.php" class="flex items-center px-6 py-2 text-gray-700 hover:bg-red-200">
                            <!-- <i class="fas fa-sign-out-alt mr-2"></i> -->
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
                <?php
                // Vérifier les documents qui expirent bientôt ou sont déjà expirés
                $result = $fonctionnaire->getAllPersonnel();
                if ($result['success']) {
                    $today = new DateTime();
                    $expiring_soon = [];
                    $expired = [];
                    
                    foreach ($result['data'] as $person) {
                        $documents = [
                            'carte' => ['date' => $person['date_expiration_carte'], 'nom' => 'Carte d\'identité'],
                            'permit' => ['date' => $person['date_expiration_permit'], 'nom' => 'Permis de conduire'],
                            'visite' => ['date' => $person['date_expiration_visite'], 'nom' => 'Visite médicale']
                        ];

                        foreach ($documents as $type => $info) {
                            if (!empty($info['date'])) {
                                $expiration_date = new DateTime($info['date']);
                                $interval = $today->diff($expiration_date);
                                $days_remaining = $expiration_date >= $today ? $interval->days : -$interval->days;

                                if ($days_remaining <= 5 && $days_remaining >= 0) {
                                    $expiring_soon[] = [
                                        'nom' => $person['nom_complet'],
                                        'document' => $info['nom'],
                                        'jours' => $days_remaining
                                    ];
                                } elseif ($days_remaining < 0) {
                                    $expired[] = [
                                        'nom' => $person['nom_complet'],
                                        'document' => $info['nom'],
                                        'jours' => abs($days_remaining)
                                    ];
                                }
                            }
                        }
                    }
                    
                }
                ?>
                <table class="min-w-full divide-y divide-gray-200 compact-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider photo-cell">Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Nom </th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider role-cell">Rôle</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider famille-cell">S.Familiale</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Ville</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Adresse</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Embauche</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider type-contract-cell">Type contrat</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Démission</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">CIN</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Permis</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Visite</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Documents</th>
                            <th class="px-6 py-3 text-left text-xs font-normal text-gray-500 uppercase tracking-wider action-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $result = $fonctionnaire->getAllPersonnel();
                        if ($result['success']) {
                            foreach ($result['data'] as $person) {
                                ?>
                                <tr data-id="<?php echo htmlspecialchars($person['id'] ?? ''); ?>" 
                                    data-carte-expiration="<?php echo htmlspecialchars($person['date_expiration_carte'] ?? ''); ?>" 
                                    data-permit-expiration="<?php echo htmlspecialchars($person['date_expiration_permit'] ?? ''); ?>" 
                                    data-visite-expiration="<?php echo htmlspecialchars($person['date_expiration_visite'] ?? ''); ?>"
                                    data-demission="<?php echo htmlspecialchars($person['date_demission'] ?? ''); ?>">
                                    

                                    <td class="px-6 py-4 whitespace-nowrap photo-cell">
                                        <?php if (!empty($person['photo'])) : ?>
                                            <img src="../../<?php echo htmlspecialchars($person['photo'] ?? ''); ?>" alt="Photo de profil" class="profile-photo">
                                        <?php else : ?>
                                            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1MCIgaGVpZ2h0PSI1MCIgdmlld0JveD0iMCAwIDUwIDUwIj48Y2lyY2xlIGN4PSIyNSIgY3k9IjE4IiByPSI4IiBmaWxsPSIjZDFkNWRiIi8+PHBhdGggZD0iTTQyLDQ5SDhjLTEuMTA0LDAtMi0wLjg5Ni0yLTJWNDJjMC04LjI4NCw2LjcxNi0xNSwxNS0xNWgxMGM4LjI4NCwwLDE1LDYuNzE2LDE1LDE1djVDNDYsNDguMTA0LDQ1LjEwNCw0OSw0NCw0OXoiIGZpbGw9IiNkMWQ1ZGIiLz48L3N2Zz4=" alt="Photo par défaut" class="profile-photo">
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['nom_complet'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap role-cell"><?php echo htmlspecialchars($person['role'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap famille-cell"><?php echo htmlspecialchars($person['situation_familiale'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['ville'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['adresse'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($person['date_embauche'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap type-contract-cell"><?php echo htmlspecialchars($person['type_contract'] ?? ''); ?></td>
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
                                    <td class="px-6 py-4 whitespace-nowrap action-cell">

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
                        <option value="superviseur">Superviseur</option>
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
                    <label class="block text-sm font-medium text-gray-700">Type de Contrat</label>
                    <select name="type_contract" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="cdi">CDI</option>
                        <option value="cdd">CDD</option>
                    </select>
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
                    <input type="text" name="nom_complet" id="nom_complet" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Carte d'identité (PDF)</label>
                    <input type="file" name="carte_identite_file" id="carte_identite_file" accept=".pdf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration CI</label>
                    <input type="date" name="date_expiration_carte" id="date_expiration_carte" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rôle</label>
                    <select name="role" id="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                    <select name="situation_familiale" id="situation_familiale" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="celibataire">Célibataire</option>
                        <option value="marier">Marié(e)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ville</label>
                    <input type="text" name="ville" id="ville" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Adresse</label>
                    <input type="text" name="adresse" id="adresse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contrat (PDF)</label>
                    <input type="file" name="contrat_file" id="contrat_file" accept=".pdf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type de contrat</label>
                    <select name="type_contract" id="type_contract" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="cdi">CDI</option>
                        <option value="cdd">CDD</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'embauche</label>
                    <input type="date" name="date_embauche" id="date_embauche" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date de démission</label>
                    <input type="date" name="date_demission" id="date_demission" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Permis de conduire (PDF)</label>
                    <input type="file" name="permit_conduire_file" id="permit_conduire_file" accept=".pdf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration permis</label>
                    <input type="date" name="date_expiration_permit" id="date_expiration_permit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Visite médicale (PDF)</label>
                    <input type="file" name="visite_medicale_file" id="visite_medicale_file" accept=".pdf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'expiration visite</label>
                    <input type="date" name="date_expiration_visite" id="date_expiration_visite" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Photo de profil</label>
                    <input type="file" name="photo" id="photo" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="col-span-2 mt-4">
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
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
        //         function checkDocumentExpiration() {
        //     const rows = document.querySelectorAll('tbody tr');
        //     rows.forEach(row => {
        //         const today = new Date();
                
        //         // Get expiration dates from data attributes
        //         const carteExpirationDate = row.getAttribute('data-carte-expiration');
        //         const permitExpirationDate = row.getAttribute('data-permit-expiration');
        //         const visiteExpirationDate = row.getAttribute('data-visite-expiration');
                
        //         // Check if any document is expired
        //         const isExpired = 
        //             (carteExpirationDate && new Date(carteExpirationDate) < today) ||
        //             (permitExpirationDate && new Date(permitExpirationDate) < today) ||
        //             (visiteExpirationDate && new Date(visiteExpirationDate) < today);
                
        //         // Apply red background if expired
        //         if (isExpired) {
        //             row.classList.add('bg-red-100');
        //         }
        //     });
        // }
        function checkDocumentExpiration() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const today = new Date();
        
        // Get expiration dates from data attributes
        const carteExpirationDate = row.getAttribute('data-carte-expiration');
        const permitExpirationDate = row.getAttribute('data-permit-expiration');
        const visiteExpirationDate = row.getAttribute('data-visite-expiration');
        const demissionDate = row.getAttribute('data-demission');
        
        // Check if any document is expired
        const isExpired = 
            (carteExpirationDate && new Date(carteExpirationDate) < today) ||
            (permitExpirationDate && new Date(permitExpirationDate) < today) ||
            (visiteExpirationDate && new Date(visiteExpirationDate) < today);
        
        // Apply red background if expired
        if (isExpired) {
            row.classList.add('bg-red-100');
        }

        // Apply blue background if demission date is set
        if (demissionDate) {
            row.classList.add('bg-orange-300');
        }
    });
}



        // Call the function when the page loads
        document.addEventListener('DOMContentLoaded', checkDocumentExpiration);

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
            // Fetch the latest personnel data from the server
            $.ajax({
                url: '../../controllers/personnel/get_personnel.php', // Create this new endpoint
                type: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const person = response.data;
                        
                        // Remplir le formulaire avec les données récupérées du serveur
                        $('#edit_id').val(person.id);
                        $('#nom_complet').val(person.nom_complet);
                        $('#role').val(person.role);
                        $('#situation_familiale').val(person.situation_familiale);
                        $('#ville').val(person.ville);
                        $('#adresse').val(person.adresse);
                        $('#date_embauche').val(person.date_embauche);
                        $('#type_contract').val(person.type_contract);
                        $('#date_demission').val(person.date_demission);
                        $('#date_expiration_carte').val(person.date_expiration_carte);
                        $('#date_expiration_permit').val(person.date_expiration_permit);
                        $('#date_expiration_visite').val(person.date_expiration_visite);
                        
                        // Afficher le modal
                        $('#editPersonnelModal').show();
                    } else {
                        Swal.fire({
                            title: 'Erreur!',
                            text: response.message || 'Impossible de récupérer les informations du personnel',
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Erreur!',
                        text: 'Une erreur est survenue lors de la récupération des informations',
                        icon: 'error'
                    });
                }
            });
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
                    console.log('Réponse reçue :', response);
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
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX :', xhr.responseText);
                    Swal.fire({
                        title: 'Erreur!',
                        text: 'Une erreur est survenue lors de la communication avec le serveur',
                        icon: 'error'
                    });
                }
            });
        });

        

    </script>
</body>
</html>