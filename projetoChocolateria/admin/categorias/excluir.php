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


$productCount = queryOne("SELECT COUNT(*) as count FROM produtos WHERE categoria_id = :id", ['id' => $id])['count'];

if ($productCount > 0) {
    addFlashMessage('danger', 'Não é possível excluir a categoria porque ela possui produtos associados');
    header('Location: index.php');
    exit;
}


try {
    execute("DELETE FROM categorias WHERE id = :id", ['id' => $id]);
    addFlashMessage('success', 'Categoria excluída com sucesso!');
} catch (Exception $e) {
    addFlashMessage('danger', 'Erro ao excluir categoria: ' . $e->getMessage());
}

header('Location: index.php');
exit;