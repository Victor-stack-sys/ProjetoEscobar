<?php

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}


function addFlashMessage($type, $message) {
    $_SESSION[FLASH_KEY] = [
        'type' => $type,
        'message' => $message
    ];
}


function hasFlashMessage() {
    return isset($_SESSION[FLASH_KEY]);
}


function getFlashMessage() {
    $message = $_SESSION[FLASH_KEY]['message'];
    unset($_SESSION[FLASH_KEY]);
    return $message;
}


function getFlashMessageType() {
    return $_SESSION[FLASH_KEY]['type']; 
}


function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}


function getCategoryName($id) {
    $category = queryOne("SELECT nome FROM categorias WHERE id = :id", ['id' => $id]);
    return $category ? $category['nome'] : 'Categoria Desconhecida';
}


function generateUniqueId() {
    return uniqid();
}