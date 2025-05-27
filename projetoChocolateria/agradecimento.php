<?php
session_start();
include_once 'includes/config.php';
include_once 'includes/db.php';
include_once 'includes/functions.php';


$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$order_id) {
    header('Location: index.php');
    exit;
}


$sql = "SELECT * FROM vendas WHERE id = :id";
$order = queryOne($sql, ['id' => $order_id]);

if (!$order) {
    header('Location: index.php');
    exit;
}

$page_title = "Pedido Confirmado";
include 'includes/header.php';
?>

<div class="container">
    <div class="thank-you-container">
        <div class="thank-you-header">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <h1>Obrigado pela sua compra!</h1>
            <p>Seu pedido foi recebido e está sendo processado.</p>
        </div>
        
        <div class="order-details">
            <h2>Detalhes do Pedido</h2>
            <div class="details-row">
                <span>Número do Pedido:</span>
                <span>#<?php echo $order_id; ?></span>
            </div>
            <div class="details-row">
                <span>Data:</span>
                <span><?php echo formatDate($order['data_venda']); ?></span>
            </div>
            <div class="details-row">
                <span>Total:</span>
                <span>R$ <?php echo number_format($order['total'], 2, ',', '.'); ?></span>
            </div>
        </div>
        
        <div class="thank-you-actions">
            <a href="index.php" class="btn btn-primary">Continuar Comprando</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>