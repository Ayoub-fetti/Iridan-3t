<?php
require_once '../../config/Database.php';
require_once '../../models/user.php';

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
  <title>Analytics Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
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
     <h1 class="text-2xl font-bold">Bienvenue Admin</h1>
    </div>
    <nav class="mt-6">
     <ul>
      <li class="px-6 py-2 text-gray-700 hover:bg-gray-200">
       <i class="fas fa-tachometer-alt mr-2"></i>
       Tableau de bord
      </li>
      <li class="px-6 py-2 text-gray-700 hover:bg-gray-200">
       <i class="fas fa-users mr-2"></i>
       Utilisateurs
      </li>
      <li class="px-6 py-2 text-gray-700 hover:bg-gray-200">
       <i class="fas fa-chart-line mr-2"></i>
       Statistiques
      </li>
      <li class="px-6 py-2 text-gray-700 hover:bg-red-200">
       <i class="fas fa-sign-out-alt mr-2"></i>
       Déconnexion
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
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom d'utilisateur</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php foreach ($users as $user): ?>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['id']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['full_name']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['email']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['role']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap">
              <button class="text-blue-600 hover:text-blue-900 mr-2"><i class="fas fa-edit"></i></button>
              <button class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
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
            <input type="text" name="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
            <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Rôle</label>
            <select name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
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

    // Fermer le modal si on clique en dehors
    window.onclick = function(event) {
      if (event.target == document.getElementById('createUserModal')) {
        closeModal();
      }
    }

    $(document).ready(function() {
      $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
          url: '../../controllers/create_user.php',
          method: 'POST',
          data: $(this).serialize(),
          success: function(response) {
            const data = JSON.parse(response);
            
            if (data.success) {
              // Fermer le modal
              document.getElementById('createUserModal').style.display = 'none';
              $('#createUserForm')[0].reset();
              
              // Afficher une alerte de succès
              alert('Utilisateur créé avec succès!');
              
              // Recharger la page
              window.location.reload();
            } else {
              $('#message').html(`<div class="p-4 bg-red-100 text-red-700 rounded-md">${data.message}</div>`);
            }
          },
          error: function(xhr, status, error) {
            console.error('Erreur AJAX:', status, error);
            $('#message').html(`<div class="p-4 bg-red-100 text-red-700 rounded-md">Erreur de connexion au serveur: ${error}</div>`);
          }
        });
      });
    });
  </script>
 </body>
</html>
