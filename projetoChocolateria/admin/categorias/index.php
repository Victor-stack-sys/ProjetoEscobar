<?php
session_start();
include_once '../../includes/config.php';
include_once '../../includes/db.php';
include_once '../../includes/functions.php';


if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}


$categorias = query("SELECT c.*, COUNT(p.id) as total_produtos 
                    FROM categorias c 
                    LEFT JOIN produtos p ON c.id = p.categoria_id 
                    GROUP BY c.id 
                    ORDER BY c.nome");

$page_title = "Gerenciar Categorias";
include '../../includes/admin_header.php';
?>
<link rel="stylesheet" href="../../assets/css/admin.css">
<div class="admin-content">
    <div class="page-header">
        <h1>Gerenciar Categorias</h1>
        <a href="adicionar.php" class="btn btn-primary">Nova Categoria</a>
    </div>
    
    <?php if (hasFlashMessage()): ?>
        <div class="alert alert-<?php echo getFlashMessageType(); ?>">
            <?php echo getFlashMessage(); ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($categorias)): ?>
        <p class="empty-message">Nenhuma categoria encontrada.</p>
    <?php else: ?>
        <div class="data-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Total de Produtos</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?php echo $categoria['id']; ?></td>
                            <td><?php echo $categoria['nome']; ?></td>
                            <td><?php echo $categoria['total_produtos']; ?></td>
                            <td class="actions">
                                <a href="editar.php?id=<?php echo $categoria['id']; ?>" class="btn btn-sm btn-secondary">Editar</a>
                                <a href="excluir.php?id=<?php echo $categoria['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta categoria?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/admin_footer.php'; ?>