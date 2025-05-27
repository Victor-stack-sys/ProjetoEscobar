<?php
session_start();
include_once 'includes/config.php';
include_once 'includes/db.php';
include_once 'includes/functions.php';


if (empty($_SESSION['cart'])) {
    header('Location: carrinho.php');
    exit;
}


$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['preco'] * $item['quantidade'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    $endereco = isset($_POST['endereco']) ? trim($_POST['endereco']) : '';
    
    $errors = [];
    
    
    if (empty($nome)) {
        $errors[] = 'Nome é obrigatório';
    }
    
    if (empty($email)) {
        $errors[] = 'Email é obrigatório';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email inválido';
    }
    
    if (empty($telefone)) {
        $errors[] = 'Telefone é obrigatório';
    }
    
    if (empty($endereco)) {
        $errors[] = 'Endereço é obrigatório';
    }
    
   
    if (empty($errors)) {
        try {
            
            $pdo = getDb();
            $pdo->beginTransaction();
            
            
            $sql = "INSERT INTO vendas (data_venda, total, cliente_nome, cliente_email, cliente_telefone, cliente_endereco) 
                    VALUES (NOW(), :total, :nome, :email, :telefone, :endereco)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'total' => $total,
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone,
                'endereco' => $endereco
            ]);
            
            $venda_id = $pdo->lastInsertId();
            
           
            $sql = "INSERT INTO vendas_itens (venda_id, produto_id, quantidade, preco) 
                    VALUES (:venda_id, :produto_id, :quantidade, :preco)";
            $stmt = $pdo->prepare($sql);
            
            foreach ($_SESSION['cart'] as $item) {
                $stmt->execute([
                    'venda_id' => $venda_id,
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco' => $item['preco']
                ]);
            }
            
            
            $pdo->commit();
            
            
            $_SESSION['cart'] = [];
            
           
            header("Location: agradecimento.php?order_id=$venda_id");
            exit;
        } catch (Exception $e) {
            
            $pdo->rollBack();
            $errors[] = 'Erro ao processar pedido: ' . $e->getMessage();
        }
    }
}

$page_title = "Finalizar Compra";
include 'includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Finalizar Compra</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Informações de Contato</h2>
            <form action="finalizar.php" method="post">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" value="<?php echo isset($nome) ? $nome : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" value="<?php echo isset($telefone) ? $telefone : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="endereco">Endereço Completo</label>
                    <textarea id="endereco" name="endereco" rows="3" required><?php echo isset($endereco) ? $endereco : ''; ?></textarea>
                </div>
                
                <h2>Método de Pagamento</h2>
                <div class="payment-methods">
                    <div class="payment-method">
                        <input type="radio" id="payment-card" name="payment_method" value="card" checked>
                        <label for="payment-card">Cartão de Crédito</label>
                    </div>
                    
                    <div class="payment-method">
                        <input type="radio" id="payment-boleto" name="payment_method" value="boleto">
                        <label for="payment-boleto">Boleto Bancário</label>
                    </div>
                    
                    <div class="payment-method">
                        <input type="radio" id="payment-pix" name="payment_method" value="pix">
                        <label for="payment-pix">PIX</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="carrinho.php" class="btn btn-secondary">Voltar ao Carrinho</a>
                    <button type="submit" class="btn btn-primary">Confirmar Pedido</button>
                </div>
            </form>
        </div>
        
        <div class="order-summary">
            <h2>Resumo do Pedido</h2>
            <div class="cart-items">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="summary-item">
                        <div class="item-info">
                            <img src="<?php echo !empty($item['imagem']) ? 'uploads/' . $item['imagem'] : 'assets/imgs/product-placeholder.jpg'; ?>" alt="<?php echo $item['nome']; ?>">
                            <div>
                                <h4><?php echo $item['nome']; ?></h4>
                                <p>Quantidade: <?php echo $item['quantidade']; ?></p>
                            </div>
                        </div>
                        <div class="item-price">
                            R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="totals">
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
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>