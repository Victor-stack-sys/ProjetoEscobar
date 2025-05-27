<?php
session_start();
include_once '../includes/config.php';
include_once '../includes/db.php';
include_once '../includes/functions.php';


if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
   
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Usuário ou senha inválidos';
    }
}

$page_title = "Login Administrativo";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>Área Administrativa</h1>
                <p>Faça login para acessar o painel</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="username">Usuário</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                </div>
            </form>
            
            <div class="login-footer">
                <a href="../index.php">Voltar para a loja</a>
            </div>
        </div>
    </div>
</body>
</html>