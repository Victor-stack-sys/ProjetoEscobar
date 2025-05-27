<?php
session_start();
include_once '../../includes/config.php';
include_once '../../includes/db.php';
include_once '../../includes/functions.php';


if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}


$venda = queryOne("SELECT * FROM vendas WHERE id = :id", ['id' => $id]);

if (!$venda) {
    addFlashMessage('danger', 'Venda não encontrada');
    header('Location: index.php');
    exit;
}


$itens = query("
    SELECT vi.*, p.nome as produto_nome, p.imagem 
    FROM vendas_itens vi 
    JOIN produtos p ON vi.produto_id = p.id 
    WHERE vi.venda_id = :id
", ['id' => $id]);

$page_title = "Detalhes da Venda #$id";
include '../../includes/admin_header.php';
?>

<link rel="stylesheet" href="../../assets/css/admin.css">

<div class="admin-content">
    <div class="page-header">
        <h1>Detalhes da Venda #<?php echo $id; ?></h1>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
    
    <div class="order-details-container">
        <div class="order-info">
            <h2>Informações da Venda</h2>
            <div class="info-group">
                <label>Data:</label>
                <span><?php echo formatDate($venda['data_venda']); ?></span>
            </div>
            <div class="info-group">
                <label>Total:</label>
                <span>R$ <?php echo number_format($venda['total'], 2, ',', '.'); ?></span>
            </div>
        </div>
        
        <div class="customer-info">
            <h2>Informações do Cliente</h2>
            <div class="info-group">
                <label>Nome:</label>
                <span><?php echo $venda['cliente_nome']; ?></span>
            </div>
            <div class="info-group">
                <label>Email:</label>
                <span><?php echo $venda['cliente_email']; ?></span>
            </div>
            <div class="info-group">
                <label>Telefone:</label>
                <span><?php echo $venda['cliente_telefone']; ?></span>
            </div>
            <div class="info-group">
                <label>Endereço:</label>
                <span><?php echo nl2br($venda['cliente_endereco']); ?></span>
            </div>
        </div>
    </div>
    
    <div class="order-items">
        <h2>Itens da Venda</h2>
        
        <?php if (empty($itens)): ?>
            <p class="empty-message">Nenhum item encontrado.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Chocolate</th>
                        <th>Preço Unitário</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td class="product-cell">
                                <div class="product-info">
                                    <img src="<?php echo !empty($item['imagem']) ? '../../uploads/' . $item['imagem'] : '../../assets/imgs/product-placeholder.jpg'; ?>" alt="<?php echo $item['produto_nome']; ?>">
                                    <span><?php echo $item['produto_nome']; ?></span>
                                </div>
                            </td>
                            <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                            <td><?php echo $item['quantidade']; ?></td>
                            <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                        <td>R$ <?php echo number_format($venda['total'], 2, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>