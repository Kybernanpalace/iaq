<?php
session_start();
if(empty($_SESSION)){
    header("Location: index.php");
    exit();
}

include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: usuario.php");
    exit();
}

$id = intval($_GET['id']);

// Delete user from database
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: usuario.php?msg=Usuário excluído com sucesso");
    exit();
} else {
    header("Location: usuario.php?error=Erro ao excluir usuário");
    exit();
}
?>
