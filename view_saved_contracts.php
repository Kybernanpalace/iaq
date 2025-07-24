<?php
session_start();
if(empty($_SESSION)){
    header("Location: index.php");
    exit;
}

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
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Fetch saved contracts with candidate info
$stmt = $pdo->query("SELECT sc.id, sc.contract_content, sc.created_at, c.nome FROM saved_contracts sc JOIN cadcandidato c ON sc.candidate_id = c.id ORDER BY sc.created_at DESC");
$savedContracts = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Contratos Salvos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
</head>
<body>
<div class="container mt-4">
    <h1>Contratos Salvos</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Candidato</th>
                <th>Data de Salvamento</th>
                <th>Visualizar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($savedContracts as $contract): ?>
            <tr>
                <td><?= htmlspecialchars($contract['nome']) ?></td>
                <td><?= htmlspecialchars($contract['created_at']) ?></td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick="viewContract(<?= $contract['id'] ?>)">Visualizar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal to show contract content -->
<div class="modal fade" id="contractModal" tabindex="-1" aria-labelledby="contractModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contractModalLabel">Contrato</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="contractContent" style="white-space: pre-wrap;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
function viewContract(id) {
    fetch('get_saved_contract.php?id=' + id)
    .then(response => response.text())
    .then(data => {
        document.getElementById('contractContent').innerText = data;
        var contractModal = new bootstrap.Modal(document.getElementById('contractModal'));
        contractModal.show();
    })
    .catch(error => alert('Erro ao carregar contrato: ' + error));
}
</script>
</body>
</html>
