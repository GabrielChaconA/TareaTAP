<?php
session_start();
$usuarios = file_exists("usuarios.json") ? json_decode(file_get_contents("usuarios.json"), true) : [];
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

// Procesar registro
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["registro"])) {
    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = password_hash(trim($_POST["password"] ?? ""), PASSWORD_DEFAULT);
    
    if (!empty($username) && !empty($password)) {
        if (isset($usuarios[$username])) {
            $mensaje = "El usuario ya existe.";
        } else {
            $usuarios[$username] = ["nombre" => $nombre, "apellido" => $apellido, "password" => $password];
            file_put_contents("usuarios.json", json_encode($usuarios));
            $mensaje = "Registro exitoso. Ahora inicia sesión.";
            $page = "login";
        }
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}

// Procesar login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $username = trim($_POST["username"] ?? "");
    
    $password = trim($_POST["password"] ?? "");
    
    if (isset($usuarios[$username]) && password_verify($password, $usuarios[$username]["password"])) {
        $_SESSION["username"] = $username;
        $page = "bienvenida";
    } else {
        $mensaje = "Usuario o contraseña incorrectos.";
    }
}

// Procesar logout
if ($page === "logout") {
    session_destroy();
    header("Location: ?page=login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticación PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color:rgb(0, 0, 0);
            color: white
        }
        .container {
            width: 350px;
            height: 500px;
            margin: 100px auto;
            background:rgb(0, 0, 0);
            padding: 20px;
          
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        input, button {
            width: 95%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 20px;
        }
        button {
            background:rgb(0, 0, 0);
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background:rgb(255, 255, 255);
            color: black
            
        }
        nav {
            text-align: center;
            margin-bottom: 20px;
            height: 100px;
        }
        nav a {
            margin: 0 10px;
            text-decoration: none;
            color:rgb(255, 255, 255);
        }
    </style>
</head>
<body>
    <nav>

        <a href="?page=login">Login</a> 
        <a href="?page=registro">Registro</a> 
        <?php if (isset($_SESSION["username"])): ?>
            <a href="?page=bienvenida">Bienvenida</a> 
            <a href="?page=logout">Cerrar sesión</a>
        <?php endif; ?>
    </nav>
    
    <div class="container">
        <?php if (isset($mensaje)) echo "<p>$mensaje</p>"; ?>
        
        <?php if ($page === "registro"): ?>
            <h2>Registro</h2>
            <form method="POST">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellido" placeholder="Apellido" required>
                <input type="text" name="username" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit" name="registro">Registrarse</button>
            </form>
        <?php elseif ($page === "login"): ?>
            <h2>USER LOGIN</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit" name="login">Iniciar sesión</button>
            </form>
        <?php elseif ($page === "bienvenida" && isset($_SESSION["username"])): ?>
            <h2>Bienvenido, <?php echo htmlspecialchars($usuarios[$_SESSION["username"]]["nombre"] ?? "Usuario"); ?>!</h2>
            <a href="?page=logout">Cerrar sesión</a>
        <?php else: ?>
            <p>No tienes acceso a esta página.</p>
        <?php endif; ?>
    </div>
</body>
</html>
