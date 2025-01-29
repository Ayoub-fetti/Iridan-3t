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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .user-info {
            padding: 20px;
            border-bottom: 1px solid #495057;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
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
    <div class="main-content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col">
                    <h2><i class="fas fa-car"></i> Gestion des Voitures</h2>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarModal">
                        <i class="fas fa-plus"></i> Ajouter une voiture
                    </button>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="carsTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Marque</th>
                                    <th>Ville</th>
                                    <th>Chauffeur</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cars = $fonctionnaire->getAllCars();
                                if ($cars['success'] && !empty($cars['data'])) {
                                    foreach ($cars['data'] as $car) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($car['matricule']) . '</td>';
                                        echo '<td>' . htmlspecialchars($car['marque']) . '</td>';
                                        echo '<td>' . htmlspecialchars($car['ville']) . '</td>';
                                        echo '<td>' . htmlspecialchars($car['chauffeur_nom'] ?? 'Non assigné') . '</td>';
                                        echo '<td>';
                                        $statusClass = '';
                                        switch ($car['status']) {
                                            case 'en service':
                                                $statusClass = 'success';
                                                break;
                                            case 'en panne':
                                                $statusClass = 'danger';
                                                break;
                                            case 'en maintenance':
                                                $statusClass = 'warning';
                                                break;
                                        }
                                        echo '<span class="badge bg-' . $statusClass . '">' . htmlspecialchars($car['status']) . '</span>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<div class="btn-group" role="group">';
                                        echo '<a href="view_car.php?matricule=' . urlencode($car['matricule']) . '" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>';
                                        echo '<button type="button" class="btn btn-warning btn-sm edit-car" data-matricule="' . htmlspecialchars($car['matricule']) . '"><i class="fas fa-edit"></i></button>';
                                        echo '<button type="button" class="btn btn-danger btn-sm delete-car" data-matricule="' . htmlspecialchars($car['matricule']) . '"><i class="fas fa-trash"></i></button>';
                                        echo '</div>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajout Voiture -->
    <div class="modal fade" id="addCarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une voiture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addCarForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="matricule" class="form-label">Matricule</label>
                                <input type="text" class="form-control" id="matricule" name="matricule" required>
                            </div>
                            <div class="col-md-4">
                                <label for="marque" class="form-label">Marque</label>
                                <input type="text" class="form-control" id="marque" name="marque" required>
                            </div>
                            <div class="col-md-4">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="ville" name="ville" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="chauffeurs_id" class="form-label">Chauffeur</label>
                                <select class="form-select" id="chauffeurs_id" name="chauffeurs_id">
                                    <option value="">Sélectionner un chauffeur</option>
                                    <?php foreach($chauffeurs as $chauffeur): ?>
                                        <option value="<?php echo $chauffeur['id']; ?>">
                                            <?php echo htmlspecialchars($chauffeur['nom'] . ' ' . $chauffeur['prenom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="en service">En service</option>
                                    <option value="en panne">En panne</option>
                                    <option value="en maintenance">En maintenance</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Carte Grise (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="carte_grise" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_carte_grise" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Visite Technique (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="visite_technique" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_visite" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Assurance (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="assurance" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_assurance" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vignette (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="vignette" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_vignette" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Feuille de Circulation (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="feuille_circulation" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_circulation" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Feuille d'Extincteur (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="feuille_extincteur" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_extincteur" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Feuille de Tachygraphe (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="feuille_tachygraphe" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_tachygraphe" required>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Modification Voiture -->
    <div class="modal fade" id="editCarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier une voiture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCarForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="old_matricule" id="edit_old_matricule">
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="edit_matricule" class="form-label">Matricule</label>
                                <input type="text" class="form-control" id="edit_matricule" name="matricule" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_marque" class="form-label">Marque</label>
                                <input type="text" class="form-control" id="edit_marque" name="marque" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="edit_ville" name="ville" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="en service">En service</option>
                                    <option value="en panne">En panne</option>
                                    <option value="en maintenance">En maintenance</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="chauffeurs_id" class="form-label">Chauffeur</label>
                                <select class="form-select" id="edit_chauffeurs_id" name="chauffeurs_id">
                                    <option value="">Sélectionner un chauffeur</option>
                                    <?php
                                    $chauffeurs = $fonctionnaire->getAllPersonnel()['data'] ?? [];
                                    foreach ($chauffeurs as $chauffeur) {
                                        echo '<option value="' . $chauffeur['id'] . '">' . htmlspecialchars($chauffeur['nom_complet']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Carte Grise (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="carte_grise" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_carte_grise" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Visite Technique (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="visite_technique" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_visite" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Assurance (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="assurance" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_assurance" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vignette (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="vignette" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_vignette" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Feuille de Circulation (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="feuille_circulation" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_circulation" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Feuille d'Extincteur (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="feuille_extincteur" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_extincteur" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Feuille de Tachygraphe (PDF)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="feuille_tachygraphe" accept=".pdf" required>
                                    <input type="date" class="form-control" name="date_expiration_tachygraphe" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </form>
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
                    <form id="deleteCarForm" method="POST">
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialiser DataTables
            $('#carsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                }
            });

            // Gestionnaire pour le bouton de modification
            $('.edit-car').click(function() {
                const matricule = $(this).data('matricule');
                // Charger les données de la voiture via AJAX et remplir le formulaire
                $.get('get_car.php', { matricule: matricule }, function(data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    // Remplir le formulaire avec les données
                    $('#edit_old_matricule').val(data.matricule);
                    $('#edit_matricule').val(data.matricule);
                    $('#edit_marque').val(data.marque);
                    $('#edit_ville').val(data.ville);
                    $('#edit_status').val(data.status);
                    $('#edit_chauffeurs_id').val(data.chauffeurs_id);
                    $('#edit_carte_grise').val(data.carte_grise);
                    $('#edit_date_expiration_carte_grise').val(data.date_expiration_carte_grise);
                    $('#edit_visite_technique').val(data.visite_technique);
                    $('#edit_date_expiration_visite').val(data.date_expiration_visite);
                    $('#edit_assurance').val(data.assurance);
                    $('#edit_date_expiration_assurance').val(data.date_expiration_assurance);
                    $('#edit_vignette').val(data.vignette);
                    $('#edit_date_expiration_vignette').val(data.date_expiration_vignette);
                    $('#edit_feuille_circulation').val(data.feuille_circulation);
                    $('#edit_date_expiration_circulation').val(data.date_expiration_circulation);
                    $('#edit_feuille_extincteur').val(data.feuille_extincteur);
                    $('#edit_date_expiration_extincteur').val(data.date_expiration_extincteur);
                    $('#edit_feuille_tachygraphe').val(data.feuille_tachygraphe);
                    $('#edit_date_expiration_tachygraphe').val(data.date_expiration_tachygraphe);
                    
                    $('#editCarModal').modal('show');
                });
            });

            // Gestionnaire pour le bouton de suppression
            $('.delete-car').click(function() {
                const matricule = $(this).data('matricule');
                $('#delete_matricule').val(matricule);
                $('#deleteCarModal').modal('show');
            });

            // Gestionnaire pour le bouton voir
            $('.view-car').click(function() {
                const matricule = $(this).data('matricule');
                window.location.href = 'view_car.php?matricule=' + encodeURIComponent(matricule);
            });

            // Rafraîchir la page après la fermeture d'un modal si une action a été effectuée
            $('.modal').on('hidden.bs.modal', function () {
                if ($('.alert').length > 0) {
                    location.reload();
                }
            });
        });
    </script>
</body>
</html>