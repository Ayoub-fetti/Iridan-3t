<?php
require_once 'check_admin.php';
require_once '../../config/Database.php';
require_once '../../models/user.php';

// Débogage des chemins
error_log("Current script path: " . __FILE__);
error_log("Document root: " . $_SERVER['DOCUMENT_ROOT']);
error_log("Request URI: " . $_SERVER['REQUEST_URI']);

// Initialiser la connexion à la base de données
$database = new Database();
$db = $database->connect();
$user = new User($db);

// Récupérer la liste des utilisateurs
$users = $user->getAllUsers();
?>
<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>gestion des utilisateurs</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet"/>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
     margin: 10% auto;
     padding: 20px;
     width: 90%;
     max-width: 500px;
     border-radius: 8px;
   }
  </style>
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
       <a href="admin_dash.php" class="flex items-center px-6 py-2 text-gray-700 bg-gray-200 <?php echo basename($_SERVER['PHP_SELF']) === 'admin_dash.php' ? 'bg-gray-200' : ''; ?>">
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
   <div class="flex-1 p-10">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Gestion des Utilisateurs</h2>
      <button onclick="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
        <i class="fas fa-plus mr-2"></i>Ajouter un utilisateur
      </button>
    </div>

    <!-- Table des utilisateurs -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
           
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom d'utilisateur</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Password</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php foreach ($users as $user): ?>
          <tr>
          
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['full_name']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['email']); ?></td>
            <?php if ($user['role'] == 'admin'): ?>
              <td class="px-6 py-4 whitespace-nowrap">••••••••</td>
              <?php endif; ?>
              <?php if ($user['role'] !== 'admin'): ?>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['password']); ?></td>
            <?php endif; ?>
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['role']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap">
              <?php if ($user['role'] !== 'admin'): ?>
              <button onclick='openEditModal(<?php echo json_encode($user); ?>)' class="text-blue-600 hover:text-blue-800 mr-2">
                <i class="fas fa-edit"></i>
              </button>
              <button onclick="confirmDelete(<?php echo $user['id']; ?>)" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
              </button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Modal pour créer un utilisateur -->
    <div id="createUserModal" class="modal">
      <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Créer un nouvel utilisateur</h3>
          <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="createUserForm" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
            <input type="text" name="username" class="mt-1 block w-full rounded-md border border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" class="mt-1 block w-full rounded-md border border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
            <input type="password" name="password" class="mt-1 block w-full rounded-md border border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Rôle</label>
            <select name="role" class="mt-1 block w-full rounded-md border border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
              <option value="fonctionnaire">Fonctionnaire</option>
              <option value="admin">Administrateur</option>
            </select>
          </div>
          <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Créer l'utilisateur
          </button>
        </form>
        <div id="message" class="mt-4"></div>
      </div>
    </div>

    <!-- Modal pour éditer un utilisateur -->
    <div id="editModal" class="modal">
      <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Éditer un utilisateur</h3>
          <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="editUserForm" class="space-y-4">
          <input type="hidden" id="editUserId" name="id">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
            <input type="text" id="editUsername" name="username" class="mt-1 block w-full rounded-md border border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="editEmail" name="email" class="mt-1 block w-full rounded-md border border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
            <input type="text" id="editPassword" name="password" class="mt-1 block w-full rounded-md border border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Laissez vide pour ne pas modifier">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Rôle</label>
            <select id="editRole" name="role" class="mt-1 block w-full rounded-md border border-black shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
              <option value="fonctionnaire">Fonctionnaire</option>
              <option value="admin">Administrateur</option>
            </select>
          </div>
          <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Éditer l'utilisateur
          </button>
        </form>
        <div id="message" class="mt-4"></div>
      </div>
    </div>
   </div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function openModal() {
      document.getElementById('createUserModal').style.display = 'block';
    }

    function closeModal() {
      document.getElementById('createUserModal').style.display = 'none';
    }

    function openEditModal(user) {
      console.log('Opening edit modal for user:', user);
      document.getElementById('editUserId').value = user.id;
      document.getElementById('editUsername').value = user.full_name;
      document.getElementById('editEmail').value = user.email;
      document.getElementById('editPassword').value = user.password;
      document.getElementById('editRole').value = user.role;
      document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    // Fonction utilitaire pour construire les chemins
    function getControllerUrl(controller) {
      const basePath = '../../controllers/';
      return basePath + controller;
    }

    function updateUser(event) {
      event.preventDefault();
      
      // Récupérer les données du formulaire
      const userId = document.getElementById('editUserId').value;
      const username = document.getElementById('editUsername').value;
      const email = document.getElementById('editEmail').value;
      const password = document.getElementById('editPassword').value;
      const role = document.getElementById('editRole').value;

      // Valider les données
      if (!userId || !username || !email || !role) {
        Swal.fire({
          icon: 'error',
          title: 'Erreur',
          text: 'Tous les champs sont requis'
        });
        return;
      }

      // Créer l'objet de données
      const formData = {
        id: userId,
        username: username,
        email: email,
        password: password,
        role: role
      };

      console.log('Sending update request with data:', formData);

      // Désactiver le formulaire pendant la requête
      const form = document.getElementById('editUserForm');
      const submitButton = form.querySelector('button[type="submit"]');
      submitButton.disabled = true;

      $.ajax({
        url: getControllerUrl('update_user.php'),
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
          console.log('Update response:', response);
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Succès',
              text: 'Utilisateur mis à jour avec succès',
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Erreur',
              text: response.message || 'Erreur inconnue'
            });
          }
        },
        error: function(xhr, status, error) {
          console.error('Ajax error:', status, error);
          console.error('Response:', xhr.responseText);
          console.error('Status:', xhr.status);
          try {
            const response = JSON.parse(xhr.responseText);
            Swal.fire({
              icon: 'error',
              title: 'Erreur',
              text: response.message || 'Erreur inconnue'
            });
          } catch (e) {
            Swal.fire({
              icon: 'error',
              title: 'Erreur',
              text: 'Erreur lors de la mise à jour: ' + error
            });
          }
        },
        complete: function() {
          submitButton.disabled = false;
        }
      });
    }

    // Attacher l'événement submit au formulaire
    document.getElementById('editUserForm').addEventListener('submit', updateUser);

    function confirmDelete(userId) {
      console.log('Confirming delete for user ID:', userId);
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
            url: getControllerUrl('delete_user.php'),
            type: 'POST',
            data: { id: userId },
            dataType: 'json',
            success: function(response) {
              console.log('Delete response:', response);
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'Succès',
                  text: 'Utilisateur supprimé avec succès',
                  timer: 2000,
                  showConfirmButton: false
                }).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Erreur',
                  text: response.message || 'Erreur inconnue'
                });
              }
            },
            error: function(xhr, status, error) {
              console.error('Ajax error:', status, error);
              console.error('Response:', xhr.responseText);
              console.error('Status:', xhr.status);
              try {
                const response = JSON.parse(xhr.responseText);
                Swal.fire({
                  icon: 'error',
                  title: 'Erreur',
                  text: response.message || 'Erreur inconnue'
                });
              } catch (e) {
                Swal.fire({
                  icon: 'error',
                  title: 'Erreur',
                  text: 'Erreur lors de la suppression: ' + error
                });
              }
            }
          });
        }
      });
    }

    // Fermer la modale si l'utilisateur clique en dehors
    window.onclick = function(event) {
      if (event.target.className === 'modal') {
        event.target.style.display = 'none';
      }
    }

    $(document).ready(function() {
      $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
          url: getControllerUrl('create_user.php'),
          method: 'POST',
          data: $(this).serialize(),
          dataType: 'json',
          success: function(data) {
            if (data.success) {
              // Réinitialiser le formulaire
              $('#createUserForm')[0].reset();
              
              // Fermer le modal
              document.getElementById('createUserModal').style.display = 'none';
              
              // Afficher une alerte de succès
              Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: 'Utilisateur créé avec succès!',
                timer: 2000,
                showConfirmButton: false
              }).then(() => {
                location.reload();
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: data.message
              });
            }
          },
          error: function(xhr, status, error) {
            Swal.fire({
              icon: 'error',
              title: 'Erreur',
              text: 'Erreur de connexion au serveur: ' + error
            });
          }
        });
      });
    });
  </script>
 </body>
</html>
