<?php
session_start();
include_once 'includes/config.php';
include_once 'includes/db.php';
include_once 'includes/functions.php';


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}


$categoria = query("SELECT * FROM categorias WHERE id = $id");

if (empty($categoria)) {
    header('Location: index.php');
    exit;
}

$categoria = $categoria[0];


$produtos = query("SELECT * FROM produtos WHERE categoria_id = $id ORDER BY nome");

$page_title = $categoria['nome'];
include 'includes/header.php';
?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> &gt; 
        <span><?php echo $categoria['nome']; ?></span>
    </div>

    <div class="category-header">
        <h1><?php echo $categoria['nome']; ?></h1>
    </div>

    <div class="products-section">
        <?php if (empty($produtos)): ?>
            <p class="empty-message">Nenhum chocolate encontrado nesta categoria.</p>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($produtos as $produto): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo !empty($produto['imagem']) ? 'uploads/' . $produto['imagem'] : 'assets/imgs/product-placeholder.jpg'; ?>" alt="<?php echo $produto['nome']; ?>">
                    </div>
                    <div class="product-info">
                        <h3><?php echo $produto['nome']; ?></h3>
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