<?php
session_start();
if(empty($_SESSION)){
    header("Location: index.php");
    exit;
}

require_once 'vendor/autoload.php'; // Dompdf autoload

use Dompdf\Dompdf;

// Database connection
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
     die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Get candidate id
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID do candidato não fornecido.");
}

// Fetch contract text
$stmt = $pdo->prepare("SELECT contrato, nome FROM cadcandidato WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row || empty($row['contrato'])) {
    die("Contrato não encontrado para o candidato.");
}

$contractText = $row['contrato'];
$candidateName = $row['nome'];

