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


$categoria = queryOne("SELECT * FROM categorias WHERE id = :id", ['id' => $id]);

if (!$categoria) {
    addFlashMessage('danger', 'Categoria não encontrada');
    header('Location: index.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    
    $errors = [];
    
    
    if (empty($nome)) {
        $errors[] = 'Nome da categoria é obrigatório';
    }
    
    
    if (empty($errors)) {
        $sql = "UPDATE categorias SET nome = :nome WHERE id = :id";
        $params = [
            'nome' => $nome,
            'id' => $id
        ];
        
        try {
            execute($sql, $params);
            addFlashMessage('success', 'Categoria atualizada com sucesso!');
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Erro ao atualizar categoria: ' . $e->getMessage();
        }
    }
}

$page_title = "Editar Categoria";
include '../../includes/admin_header.php';
?>

<link rel="stylesheet" href="../../assets/css/admin.css">

<div class="admin-content">
    <div class="page-header">
        <h1>Editar Categoria</h1>
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
        <form action="editar.php?id=<?php echo $id; ?>" method="post">
            <div class="form-group">
                <label for="nome">Nome da Categoria</label>
                <input type="text" id="nome" name="nome" value="<?php echo $categoria['nome']; ?>" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>