<?php
session_start();
include_once 'includes/config.php';
include_once 'includes/db.php';
include_once 'includes/functions.php';


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $produto_id = isset($_POST['produto_id']) ? (int)$_POST['produto_id'] : 0;
    
    if ($action === 'add' && $produto_id > 0) {
        $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
        
        
        $produto = query("SELECT * FROM produtos WHERE id = $produto_id");
        
        if (!empty($produto)) {
            $produto = $produto[0];
            
            
            $found = false;
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['produto_id'] == $produto_id) {
                    $_SESSION['cart'][$key]['quantidade'] += $quantidade;
                    $found = true;
                    break;
                }
            }
            
           
            if (!$found) {
                $_SESSION['cart'][] = [
                    'produto_id' => $produto_id,
                    'nome' => $produto['nome'],
                    'preco' => $produto['preco'],
                    'quantidade' => $quantidade,
                    'imagem' => $produto['imagem'] ?? ''
                ];
            }
            
            addFlashMessage('success', 'Produto adicionado ao carrinho!');
        }
    } elseif ($action === 'update' && $produto_id > 0) {
        $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
        
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['produto_id'] == $produto_id) {
                if ($quantidade <= 0) {
                    unset($_SESSION['cart'][$key]);
                } else {
                    $_SESSION['cart'][$key]['quantidade'] = $quantidade;
                }
                break;
            }
        }
        
        
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    } elseif ($action === 'remove' && $produto_id > 0) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['produto_id'] == $produto_id) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        
       
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        
        addFlashMessage('success', 'Produto removido do carrinho!');
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        addFlashMessage('success', 'Carrinho esvaziado com sucesso!');
    }
    
    
    header('Location: carrinho.php');
    exit;
}


$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['preco'] * $item['quantidade'];
}

$page_title = "Carrinho de Compras";
include 'includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Carrinho de Compras</h1>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <p>Seu carrinho está vazio.</p>
            <a href="index.php" class="btn btn-primary">Continuar Comprando</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <div class="cart-items">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Chocolate</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <tr>
                                <td class="product-cell">
                                    <div class="product-info">
                                        <img src="<?php echo !empty($item['imagem']) ? 'uploads/' . $item['imagem'] : 'assets/imgs/product-placeholder.jpg'; ?>" alt="<?php echo $item['nome']; ?>">
                                        <span><?php echo $item['nome']; ?></span>
                                    </div>
                                </td>
                                <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                <td>
                                    <form action="carrinho.php" method="post" class="update-quantity-form">
                                        <input type="hidden" name="produto_id" value="<?php echo $item['produto_id']; ?>">
                                        <input type="hidden" name="action" value="update">
                                        <div class="quantity-controls cart-quantity">
                                            <button type="button" class="qty-btn minus">-</button>
                                            <input type="number" name="quantidade" value="<?php echo $item['quantidade']; ?>" min="1" max="99" class="qty-input">
                                            <button type="button" class="qty-btn plus">+</button>
                                        </div>
                                        <button type="submit" class="btn btn-xs btn-secondary update-btn">Atualizar</button>
                                    </form>
                                </td>
                                <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                                <td>
                                    <form action="carrinho.php" method="post" class="remove-form">
                                        <input type="hidden" name="produto_id" value="<?php echo $item['produto_id']; ?>">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit" class="btn btn-xs btn-danger">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="cart-summary">
                <h2>Resumo do pedido</h2>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                </div>
                <div class="summary-row">
                    <span>Entrega:</span>
                    <span>Grátis</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                </div>
                <a href="finalizar.php" class="btn btn-primary btn-block">Finalizar Compra</a>
                <div class="cart-actions">
                    <a href="index.php" class="btn btn-secondary">Continuar Comprando</a>
                    <form action="carrinho.php" method="post" class="clear-cart-form">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn btn-danger">Esvaziar Carrinho</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    document.querySelectorAll('.quantity-controls').forEach(control => {
        const minusBtn = control.querySelector('.minus');
        const plusBtn = control.querySelector('.plus');
        const input = control.querySelector('input');
        
        minusBtn.addEventListener('click', function() {
            let value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
            }
        });
        
        plusBtn.addEventListener('click', function() {
            let value = parseInt(input.value);
            if (value < 99) {
                input.value = value + 1;
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>