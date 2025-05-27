<?php 
session_start(); 
include_once 'includes/config.php';
include_once 'includes/db.php';
include_once 'includes/functions.php'; 


$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null; 

$sql = "SELECT p.*, c.nome as categoria_nome 
        FROM produtos p 
        JOIN categorias c ON p.categoria_id = c.id";

if ($category_id) {
    $sql .= " WHERE p.categoria_id = $category_id";
}

$productos = query($sql);


$categorias = query("SELECT * FROM categorias ORDER BY nome");

$page_title = "Home";
include 'includes/header.php';
?>

<div class="hero">
    <div class="hero-content">
        <h1>Bem-vindo à nossa Chocolateria</h1>
        <p>Encontre os chocolates mais gourmertizados do Brasil</p>
        <a href="#produtos" class="btn btn-primary">Ver Chocolates</a>
    </div>
</div>

<div class="container">
    <div class="categories-nav">
        <h2>Categorias</h2>
        <ul>
            <li><a href="index.php" <?php echo !$category_id ? 'class="active"' : ''; ?>>Todos nossos chocolates</a></li>
            <?php foreach ($categorias as $categoria): ?>
            <li>
                <a href="index.php?category_id=<?php echo $categoria['id']; ?>" 
                   <?php echo $category_id == $categoria['id'] ? 'class="active"' : ''; ?>>
                    <?php echo $categoria['nome']; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="products-section" id="produtos">
        <h2><?php echo $category_id ? '' . getCategoryName($category_id) : 'Todos os chocolates'; ?></h2>
        
        <?php if (empty($productos)): ?>
            <p class="empty-message">Nenhum  encontrado nesta categoria.</p>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($productos as $produto): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo !empty($produto['imagem']) ? 'uploads/' . $produto['imagem'] : 'img/trufa.png'; ?>" alt="<?php echo $produto['nome']; ?>">
                    </div>
                    <div class="product-info">
                        <h3><?php echo $produto['nome']; ?></h3>
                        <p class="product-category"><?php echo $produto['categoria_nome']; ?></p>
                        <p class="product-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        <div class="product-actions">
                            <a href="produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-secondary">Ver Detalhes</a>
                            <form action="carrinho.php" method="post">
                                <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="quantidade" value="1">
                                <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>