<?php
session_start();
include_once '../../includes/config.php';
include_once '../../includes/db.php';
include_once '../../includes/functions.php';


if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}


$vendas = query("SELECT * FROM vendas ORDER BY data_venda DESC");

$page_title = "Gerenciar Vendas";
include '../../includes/admin_header.php';
?>

<link rel="stylesheet" href="../../assets/css/admin.css">

<div class="admin-content">
    <div class="page-header">
        <h1>Gerenciar Vendas</h1>
    </div>
    
    <?php if (hasFlashMessage()): ?>
        <div class="alert alert-<?php echo getFlashMessageType(); ?>">
            <?php echo getFlashMessage(); ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($vendas)): ?>
        <p class="empty-message">Nenhuma venda encontrada.</p>
    <?php else: ?>
        <div class="data-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendas as $venda): ?>
                        <tr>
                            <td>#<?php echo $venda['id']; ?></td>
                            <td><?php echo formatDate($venda['data_venda']); ?></td>
                            <td><?php echo $venda['cliente_nome']; ?></td>
                            <td>R$ <?php echo number_format($venda['total'], 2, ',', '.'); ?></td>
                            <td class="actions">
                                <a href="visualizar.php?id=<?php echo $venda['id']; ?>" class="btn btn-sm btn-secondary">Ver Detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/admin_footer.php'; ?>