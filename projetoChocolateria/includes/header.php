<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Loja Virtual</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <span>La Cacau</span>
                </a>
                
                <nav class="main-nav">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <?php
                      
                        if (!isset($categorias)) {
                            $categorias = query("SELECT * FROM categorias ORDER BY nome LIMIT 5");
                        }
                        
                        foreach ($categorias as $cat): 
                        ?>
                            <li><a href="categoria.php?id=<?php echo $cat['id']; ?>"><?php echo $cat['nome']; ?></a></li>
                        <?php endforeach; ?>

                    <li>
                        <a href="admin/login.php">   
                            <img src="admin/img/user.png" alt="Administrador" style="width: 30px; height: 30px; border-radius: 50%;">
                        </a>
                    </li>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <?php
                    
                    $cartCount = 0;
                    if (isset($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item) {
                            $cartCount += $item['quantidade'];
                        }
                    }
                    ?>
                    <a href="carrinho.php" class="cart-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <?php if ($cartCount > 0): ?>
                            <span class="cart-count"><?php echo $cartCount; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <main class="site-main">
        <?php if (hasFlashMessage()): ?>
            <div class="alert alert-<?php echo getFlashMessageType(); ?>">
                <?php echo getFlashMessage(); ?>
            </div>
        <?php endif; ?>