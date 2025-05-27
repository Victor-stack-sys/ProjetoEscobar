<?php
session_start();
include_once '../../includes/config.php';
include_once '../../includes/db.php';
include_once '../../includes/functions.php';


if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    
    $errors = [];
    
    
    if (empty($nome)) {
        $errors[] = 'Nome da categoria é obrigatório';
    }
    
    
    if (empty($errors)) {
        $sql = "INSERT INTO categorias (nome) VALUES (:nome)";
        $params = ['nome' => $nome];
        
        try {
            execute($sql, $params);
            addFlashMessage('success', 'Categoria adicionada com sucesso!');
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Erro ao adicionar categoria: ' . $e->getMessage();
        }
    }
}

$page_title = "Adicionar Categoria";
include '../../includes/admin_header.php';
?>

<link rel="stylesheet" href="../../assets/css/admin.css">

<div class="admin-content">
    <div class="page-header">
        <h1>Adicionar Categoria</h1>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <form action="adicionar.php" method="post">
            <div class="form-group">
                <label for="nome">Nome da Categoria</label>
                <input type="text" id="nome" name="nome" value="<?php echo isset($nome) ? $nome : ''; ?>" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>