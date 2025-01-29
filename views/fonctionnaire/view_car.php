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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Détails de la Voiture</h2>
            <a href="gestion_cars.php" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title">Informations Générales</h5>
                        <table class="table">
                            <tr>
                                <th>Matricule:</th>
                                <td><?php echo htmlspecialchars($car['matricule']); ?></td>
                            </tr>
                            <tr>
                                <th>Marque:</th>
                                <td><?php echo htmlspecialchars($car['marque']); ?></td>
                            </tr>
                            <tr>
                                <th>Ville:</th>
                                <td><?php echo htmlspecialchars($car['ville']); ?></td>
                            </tr>
                            <tr>
                                <th>Chauffeur:</th>
                                <td><?php echo htmlspecialchars($car['chauffeur_nom'] ?? 'Non assigné'); ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $car['status'] === 'en service' ? 'success' : 
                                            ($car['status'] === 'en panne' ? 'danger' : 'warning'); 
                                    ?>">
                                        <?php echo htmlspecialchars($car['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h5>Documents et Dates d'Expiration</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Document</th>
                                        <th>Référence</th>
                                        <th>Date d'Expiration</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                        echo '<td>' . htmlspecialchars($doc['name']) . '</td>';
                                        echo '<td>';
                                        if (!empty($car[$doc['ref']])) {
                                            echo '<a href="../../' . htmlspecialchars($car[$doc['ref']]) . '" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-file-pdf"></i> Voir le PDF
                                                  </a>';
                                        } else {
                                            echo 'Non disponible';
                                        }
                                        echo '</td>';
                                        echo '<td>' . $car[$doc['date']] . '</td>';
                                        echo '<td>';
                                        if ($is_expired) {
                                            echo '<span class="badge bg-danger">Expiré</span>';
                                        } elseif ($days_remaining <= 30) {
                                            echo '<span class="badge bg-warning">Expire dans ' . $days_remaining . ' jours</span>';
                                        } else {
                                            echo '<span class="badge bg-success">Valide</span>';
                                        }
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button class="btn btn-warning edit-car" data-matricule="<?php echo htmlspecialchars($car['matricule']); ?>">
                                Modifier
                            </button>
                            <button class="btn btn-danger delete-car" data-matricule="<?php echo htmlspecialchars($car['matricule']); ?>">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Suppression -->
    <div class="modal fade" id="deleteCarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cette voiture ?</p>
                    <form id="deleteCarForm" method="POST" action="gestion_cars.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="matricule" id="delete_matricule">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Gestionnaire pour le bouton de modification
            $('.edit-car').click(function() {
                const matricule = $(this).data('matricule');
                window.location.href = 'gestion_cars.php?action=edit&matricule=' + encodeURIComponent(matricule);
            });

            // Gestionnaire pour le bouton de suppression
            $('.delete-car').click(function() {
                const matricule = $(this).data('matricule');
                $('#delete_matricule').val(matricule);
                $('#deleteCarModal').modal('show');
            });
        });
    </script>
</body>
</html>
