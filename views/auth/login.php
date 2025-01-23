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