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

$sql = "SELECT p.*, c.nome as categoria_nome 
        FROM produtos p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.id = $id";

$produto = query($sql);

if (empty($produto)) {
    header('Location: index.php');
    exit;
}

$produto = $produto[0];


$sql = "SELECT * FROM produtos 
        WHERE categoria_id = {$produto['categoria_id']} 
        AND id != {$produto['id']} 
        LIMIT 4";
$relacionados = query($sql);

$page_title = $produto['nome'];
include 'includes/header.php';
?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> &gt; 
        <a href="index.php?category_id=<?php echo $produto['categoria_id']; ?>"><?php echo $produto['categoria_nome']; ?></a> &gt; 
        <span><?php echo $produto['nome']; ?></span>
    </div>

    <div class="product-detail">
        <div class="product-image">
            <img src="<?php echo !empty($produto['imagem']) ? 'uploads/' . $produto['imagem'] : 'assets/imgs/product-placeholder.jpg'; ?>" alt="<?php echo $produto['nome']; ?>">
        </div>
        <div class="product-info">
            <h1><?php echo $produto['nome']; ?></h1>
            <p class="product-category"><?php echo $produto['categoria_nome']; ?></p>
            <p class="product-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
            
            <?php if (!empty($produto['descricao'])): ?>
                <div class="product-description">
                    <h3>Descrição</h3>
                    <p><?php echo nl2br($produto['descricao']); ?></p>
                </div>
            <?php endif; ?>
            
            <form action="carrinho.php" method="post" class="add-to-cart-form">
                <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                <input type="hidden" name="action" value="add">
                
                <div class="quantity-selector">
                    <label for="quantidade">Quantidade:</label>
                    <div class="quantity-controls">
                        <button type="button" class="qty-btn minus">-</button>
                        <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="99">
                        <button type="button" class="qty-btn plus">+</button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-large">Adicionar ao Carrinho</button>
            </form>
        </div>
    </div>

    <?php if (!empty($relacionados)): ?>
    <div class="related-products">
        <h2>Produtos Relacionados</h2>
        <div class="products-grid">
            <?php foreach ($relacionados as $relacionado): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="<?php echo !empty($relacionado['imagem']) ? 'uploads/' . $relacionado['imagem'] : 'assets/imgs/product-placeholder.jpg'; ?>" alt="<?php echo $relacionado['nome']; ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo $relacionado['nome']; ?></h3>
                    <p class="product-price">R$ <?php echo number_format($relacionado['preco'], 2, ',', '.'); ?></p>
                    <div class="product-actions">
                        <a href="produto.php?id=<?php echo $relacionado['id']; ?>" class="btn btn-secondary">Ver Detalhes</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const minusBtn = document.querySelector('.qty-btn.minus');
    const plusBtn = document.querySelector('.qty-btn.plus');
    const quantityInput = document.querySelector('#quantidade');
    
    minusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });
    
    plusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value < 99) {
            quantityInput.value = value + 1;
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>