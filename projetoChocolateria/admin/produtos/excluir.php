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


$orderCount = queryOne("SELECT COUNT(*) as count FROM vendas_itens WHERE produto_id = :id", ['id' => $id])['count'];

if ($orderCount > 0) {
    addFlashMessage('danger', 'Não é possível excluir o produto porque ele está associado a pedidos');
    header('Location: index.php');
    exit;
}


if (!empty($produto['imagem'])) {
    $imagePath = '../../uploads/' . $produto['imagem'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}


try {
    execute("DELETE FROM produtos WHERE id = :id", ['id' => $id]);
    addFlashMessage('success', 'Produto excluído com sucesso!');
} catch (Exception $e) {
    addFlashMessage('danger', 'Erro ao excluir produto: ' . $e->getMessage());
}

header('Location: index.php');
exit;