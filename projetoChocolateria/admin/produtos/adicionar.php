<?php
session_start();
include_once '../../includes/config.php';
include_once '../../includes/db.php';
include_once '../../includes/functions.php';


if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
    
}



$categorias = query("SELECT * FROM categorias ORDER BY nome");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $preco = isset($_POST['preco']) ? str_replace(',', '.', $_POST['preco']) : 0;
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : 0;
    
    $errors = [];
    
  
    if (empty($nome)) {
        $errors[] = 'Nome do produto é obrigatório';
    }
    
    if (!is_numeric($preco) || $preco <= 0) {
        $errors[] = 'Preço deve ser um número positivo';
    }
    
    if ($categoria_id <= 0) {
        $errors[] = 'Categoria é obrigatória';
    }
    
    
    $imagem = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        
       
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = basename($_FILES['imagem']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        
        $uniqueName = uniqid() . '.' . $fileExt;
        $targetFile = $uploadDir . $uniqueName;
        
       
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExt, $allowedTypes)) {
            $errors[] = 'Apenas imagens JPG, JPEG, PNG e GIF são permitidas';
        } else {
           
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $targetFile)) {
                $imagem = $uniqueName;
            } else {
                $errors[] = 'Falha ao fazer upload da imagem';
            }
        }
    }
    
 
    if (empty($errors)) {
        $sql = "INSERT INTO produtos (nome, preco, descricao, categoria_id, imagem) 
                VALUES (:nome, :preco, :descricao, :categoria_id, :imagem)";
        $params = [
            'nome' => $nome,
            'preco' => $preco,
            'descricao' => $descricao,
            'categoria_id' => $categoria_id,
            'imagem' => $imagem
        ];
        
        try {
            execute($sql, $params);
            addFlashMessage('success', 'Produto adicionado com sucesso!');
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Erro ao adicionar produto: ' . $e->getMessage();
        }
    }
}

$page_title = "Adicionar Produto";
include '../../includes/admin_header.php';
?>
<link rel="stylesheet" href="../../assets/css/admin.css">

<div class="admin-content">
    <div class="page-header">
        <h1>Adicionar Produto</h1>
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
        <form action="adicionar.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">Nome do Produto</label>
                <input type="text" id="nome" name="nome" value="<?php echo isset($nome) ? $nome : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="preco">Preço (R$)</label>
                <input type="text" id="preco" name="preco" value="<?php echo isset($preco) ? $preco : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="categoria_id">Categoria</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>" <?php echo isset($categoria_id) && $categoria_id == $categoria['id'] ? 'selected' : ''; ?>>
                            <?php echo $categoria['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="5"><?php echo isset($descricao) ? $descricao : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="imagem">Imagem</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">
                <p class="help-text">Selecione uma imagem (JPG, JPEG, PNG ou GIF)</p>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>