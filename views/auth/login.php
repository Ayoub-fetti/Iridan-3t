<?php 
require_once '../../config/Database.php';
require_once '../../models/user.php';

session_start();

// Si l'utilisateur est déjà connecté, le rediriger selon son rôle
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin_principale') {
        header('Location: ../admin/admin_stat.php');
    } elseif ($_SESSION['role'] === 'fonctionnaire') {
        header('Location: ../fonctionnaire/iridan_dash.php');
    } else {
        header('Location: ../user/list_cars.php'); 
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $database = new Database();
        $db = $database->connect();
        $user = new User($db);

        if ($user->login($email, $password)) {
            if ($_SESSION['role'] === 'admin_principale') {
                header('Location: ../admin/admin_stat.php');
            } elseif ($_SESSION['role'] === 'fonctionnaire') {
                header('Location: ../fonctionnaire/gestion_users.php');
            }
            exit();
        } else {
            $error = 'Email ou mot de passe incorrect';
        }
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .background-image {
            background-image: url('../../public/images/driver.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="background-image h-screen flex items-center justify-center">
    <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-white mb-6 text-center">Se connecter</h2>
       
        <form action="login.php" method="post">
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-2 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <div class="mb-4">
                <label class="block text-white mb-2" for="email">Enter your email</label>
                <input class="w-full p-2 border border-gray-300 rounded bg-transparent text-white" type="email" name="email" id="email" placeholder="example@example.com" required>
            </div>
            <div class="mb-4">
                <label class="block text-white mb-2" for="password">Enter your password</label>
                <input class="w-full p-2 border border-gray-300 rounded bg-transparent text-white" type="password" name="password" id="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full bg-white text-black py-2 rounded font-bold">Log In</button>
        </form>
      
    </div>
</body>
</html>