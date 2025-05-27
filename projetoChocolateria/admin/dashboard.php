<?php
session_start();
include_once '../includes/config.php';
include_once '../includes/db.php';
include_once '../includes/functions.php';


if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}


$totalProducts = queryOne("SELECT COUNT(*) as count FROM produtos")['count'];
$totalCategories = queryOne("SELECT COUNT(*) as count FROM categorias")['count'];
$totalOrders = queryOne("SELECT COUNT(*) as count FROM vendas")['count'];
$revenue = queryOne("SELECT SUM(total) as total FROM vendas")['total'] ?? 0;


$recentOrders = query("SELECT * FROM vendas ORDER BY data_venda DESC LIMIT 5");


$topProducts = query("
    SELECT p.id, p.nome, p.preco, SUM(vi.quantidade) as total_vendido, COUNT(DISTINCT v.id) as total_pedidos
    FROM produtos p
    JOIN vendas_itens vi ON p.id = vi.produto_id
    JOIN vendas v ON vi.venda_id = v.id
    GROUP BY p.id
    ORDER BY total_vendido DESC
    LIMIT 5
");

$page_title = "Dashboard";
include '../includes/admin_header.php';
?>

<div class="dashboard">
    <h1 class="page-title">Dashboard</h1>
    
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon products-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
            <div class="stat-info">
                <h3>Chocolates</h3>
                <span class="stat-value"><?php echo $totalProducts; ?></span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon categories-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"></line>
                    <line x1="8" y1="12" x2="21" y2="12"></line>
                    <line x1="8" y1="18" x2="21" y2="18"></line>
                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
            </div>
            <div class="stat-info">
                <h3>Categorias</h3>
                <span class="stat-value"><?php echo $totalCategories; ?></span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orders-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
            </div>
            <div class="stat-info">
                <h3>Pedidos</h3>
                <span class="stat-value"><?php echo $totalOrders; ?></span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon revenue-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <div class="stat-info">
                <h3>Receita</h3>
                <span class="stat-value">R$ <?php echo number_format($revenue, 2, ',', '.'); ?></span>
            </div>
        </div>
    </div>
    
    <div class="dashboard-widgets">
        <div class="widget recent-orders">
            <h2>Pedidos Recentes</h2>
            
            <?php if (empty($recentOrders)): ?>
                <p class="empty-message">Nenhum pedido encontrado.</p>
            <?php else: ?>
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
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo formatDate($order['data_venda']); ?></td>
                                <td><?php echo $order['cliente_nome']; ?></td>
                                <td>R$ <?php echo number_format($order['total'], 2, ',', '.'); ?></td>
                                <td>
                                    <a href="vendas/visualizar.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-secondary">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="widget-footer">
                    <a href="vendas/index.php" class="view-all">Ver Todos</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="widget top-products">
            <h2>Chocolates Mais Vendidos</h2>
            
            <?php if (empty($topProducts)): ?>
                <p class="empty-message">Nenhum chocolate vendido ainda.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Chocolate</th>
                            <th>Preço</th>
                            <th>Qtd. Vendida</th>
                            <th>Pedidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProducts as $product): ?>
                            <tr>
                                <td><?php echo $product['nome']; ?></td>
                                <td>R$ <?php echo number_format($product['preco'], 2, ',', '.'); ?></td>
                                <td><?php echo $product['total_vendido']; ?></td>
                                <td><?php echo $product['total_pedidos']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="widget-footer">
                    <a href="produtos/index.php" class="view-all">Ver Todos nossos chocolates</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>