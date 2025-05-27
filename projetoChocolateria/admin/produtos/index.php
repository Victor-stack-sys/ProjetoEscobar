<?php
session_start();
include_once '../../includes/config.php';
include_once '../../includes/db.php';
include_once '../../includes/functions.php';


if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}


$produtos = query("SELECT p.*, c.nome as categoria_nome 
                  FROM produtos p 
                  JOIN categorias c ON p.categoria_id = c.id 
                  ORDER BY p.nome");

$page_title = "Gerenciar Chocolates";
include '../../includes/admin_header.php';
?>
<link rel="stylesheet" href="../../assets/css/admin.css">

<div class="admin-content">
    <div class="page-header">
        <h1>Gerenciar Chocolates</h1>
        <a href="adicionar.php" class="btn btn-primary">Novo Chocolate</a>
    </div>
    
    <?php if (hasFlashMessage()): ?>
        <div class="alert alert-<?php echo getFlashMessageType(); ?>">
            <?php echo getFlashMessage(); ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($produtos)): ?>
        <p class="empty-message">Nenhum chocolate encontrado.</p>
    <?php else: ?>
        <div class="data-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?php echo $produto['id']; ?></td>
                            <td class="product-image-cell">
                                <img src="<?php echo !empty($produto['imagem']) ? '../../uploads/' . $produto['imagem'] : '../../assets/imgs/product-placeholder.jpg'; ?>" alt="<?php echo $produto['nome']; ?>">
                            </td>
                            <td><?php echo $produto['nome']; ?></td>
                            <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                            <td><?php echo $produto['categoria_nome']; ?></td>
                            <td class="actions">
                                <a href="editar.php?id=<?php echo $produto['id']; ?>" class="btn btn-sm btn-secondary">Editar</a>
                                <a href="excluir.php?id=<?php echo $produto['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/admin_footer.php'; ?>