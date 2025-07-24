<?php
session_start();
if(empty($_SESSION)){
    header("Location: index.php");
    exit;
}

$host = 'localhost';
$db   = 'sislogin';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     http_response_code(500);
     echo "Erro ao conectar ao banco de dados.";
     exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo "ID do contrato não fornecido.";
    exit;
}

$stmt = $pdo->prepare("SELECT contract_content FROM saved_contracts WHERE id = ?");
$stmt->execute([$id]);
$contract = $stmt->fetch();

if (!$contract) {
    http_response_code(404);
    echo "Contrato não encontrado.";
    exit;
}

header('Content-Type: text/plain; charset=utf-8');
echo $contract['contract_content'];
?>
