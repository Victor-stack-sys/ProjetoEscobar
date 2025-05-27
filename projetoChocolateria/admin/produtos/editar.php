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


$produto = queryOne("SELECT * FROM produtos WHERE id = :id", ['id' => $id]);

if (!$produto) {
    addFlashMessage('danger', 'Produto não encontrado');
    header('Location: index.php');
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
    
    
    $imagem = $produto['imagem'];
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
                
                if (!empty($produto['imagem']) && file_exists($uploadDir . $produto['imagem'])) {
                    unlink($uploadDir . $produto['imagem']);
                }
                $imagem = $uniqueName;
            } else {
                $errors[] = 'Falha ao fazer upload da imagem';
            }
        }
    }
    
 
    if (empty($errors)) {
        $sql = "UPDATE produtos 
                SET nome = :nome, preco = :preco, descricao = :descricao, 
                    categoria_id = :categoria_id, imagem = :imagem 
                WHERE id = :id";
        $params = [
            'nome' => $nome,
            'preco' => $preco,
            'descricao' => $descricao,
            'categoria_id' => $categoria_id,
            'imagem' => $imagem,
            'id' => $id
        ];
        
        try {
            execute($sql, $params);
            addFlashMessage('success', 'Produto atualizado com sucesso!');
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Erro ao atualizar produto: ' . $e->getMessage();
        }
    }
}

$page_title = "Editar Produto";
include '../../includes/admin_header.php';
?>
<link rel="stylesheet" href="../../assets/css/admin.css">

<div class="admin-content">
    <div class="page-header">
        <h1>Editar Produto</h1>
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
        <form action="editar.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">Nome do Produto</label>
                <input type="text" id="nome" name="nome" value="<?php echo $produto['nome']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="preco">Preço (R$)</label>
                <input type="text" id="preco" name="preco" value="<?php echo $produto['preco']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="categoria_id">Categoria</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>" <?php echo $produto['categoria_id'] == $categoria['id'] ? 'selected' : ''; ?>>
                            <?php echo $categoria['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="5"><?php echo $produto['descricao']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="imagem">Imagem</label>
                <?php if (!empty($produto['imagem'])): ?>
                    <div class="current-image">
                        <img src="../../uploads/<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>">
                        <p>Imagem atual</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="imagem" name="imagem" accept="image/*">
                <p class="help-text">Selecione uma nova imagem para substituir a atual (opcional)</p>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>