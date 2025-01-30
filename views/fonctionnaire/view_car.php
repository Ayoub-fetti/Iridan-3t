<?php
require_once '../../models/fonctionnaire.php';
require_once '../../config/Database.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Vérifier si le matricule est fourni
if (!isset($_GET['matricule'])) {
    header('Location: gestion_cars.php');
    exit();
}

$fonctionnaire = new Fonctionnaire();
$result = $fonctionnaire->getCarByMatricule($_GET['matricule']);

if (!$result['success']) {
    header('Location: gestion_cars.php');
    exit();
}

$car = $result['data'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Voiture - <?php echo htmlspecialchars($car['matricule']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100">
    <div class="flex">

        <!-- Main Content -->
        <div class="flex-1 p-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Détails du Véhicule</h2>
                <a href="gestion_cars.php" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Informations Générales -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Informations Générales</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Matricule</p>
                            <p class="font-medium"><?php echo htmlspecialchars($car['matricule']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Marque</p>
                            <p class="font-medium"><?php echo htmlspecialchars($car['marque']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Ville</p>
                            <p class="font-medium"><?php echo htmlspecialchars($car['ville']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Chauffeur</p>
                            <p class="font-medium"><?php echo htmlspecialchars($car['chauffeur_nom'] ?? 'Non assigné'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full <?php 
                                echo match($car['status']) {
                                    'en service' => 'bg-green-100 text-green-800',
                                    'en panne' => 'bg-red-100 text-red-800',
                                    'en maintenance' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            ?>">
                                <?php echo htmlspecialchars($car['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Documents et Dates d'Expiration</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'Expiration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $documents = [
                                    ['name' => 'Carte Grise', 'ref' => 'carte_grise', 'date' => 'date_expiration_carte_grise'],
                                    ['name' => 'Visite Technique', 'ref' => 'visite_technique', 'date' => 'date_expiration_visite'],
                                    ['name' => 'Assurance', 'ref' => 'assurance', 'date' => 'date_expiration_assurance'],
                                    ['name' => 'Vignette', 'ref' => 'vignette', 'date' => 'date_expiration_vignette'],
                                    ['name' => 'Feuille de Circulation', 'ref' => 'feuille_circulation', 'date' => 'date_expiration_circulation'],
                                    ['name' => 'Feuille d\'Extincteur', 'ref' => 'feuille_extincteur', 'date' => 'date_expiration_extincteur'],
                                    ['name' => 'Feuille de Tachygraphe', 'ref' => 'feuille_tachygraphe', 'date' => 'date_expiration_tachygraphe']
                                ];

                                foreach ($documents as $doc) {
                                    $expiration_date = new DateTime($car[$doc['date']]);
                                    $today = new DateTime();
                                    $days_remaining = $today->diff($expiration_date)->days;
                                    $is_expired = $expiration_date < $today;

                                    echo '<tr>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($doc['name']) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    if (!empty($car[$doc['ref']])) {
                                        echo '<a href="../../' . htmlspecialchars($car[$doc['ref']]) . '" target="_blank" 
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">';
                                        echo '<i class="fas fa-file-pdf mr-2"></i> Voir le PDF</a>';
                                    } else {
                                        echo '<span class="text-gray-500">Non disponible</span>';
                                    }
                                    echo '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">' . $car[$doc['date']] . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    if ($is_expired) {
                                        echo '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Expiré</span>';
                                    } elseif ($days_remaining <= 30) {
                                        echo '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Expire dans ' . $days_remaining . ' jours</span>';
                                    } else {
                                        echo '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Valide</span>';
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex space-x-4">

                    <button onclick="deleteCar('<?php echo htmlspecialchars($car['matricule']); ?>')" 
                            class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openEditModal(matricule) {
            window.location.href = 'gestion_cars.php?action=edit&matricule=' + encodeURIComponent(matricule);
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
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'gestion_cars.php';
                    
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
    </script>
</body>
</html>