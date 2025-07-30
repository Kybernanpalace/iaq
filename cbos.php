<?php
session_start();
if(empty($_SESSION)){
    print "<script>location.href='index.php';</script>";
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

// Handle form submissions for create and update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $cod = $_POST['cod'] ?? '';
    $atividades = $_POST['atividades'] ?? '';

    if ($id) {
        $stmt = $pdo->prepare("UPDATE cadcbos SET cod = ?, atividades = ? WHERE id = ?");
        $stmt->execute([$cod, $atividades, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cadcbos (cod, atividades) VALUES (?, ?)");
        $stmt->execute([$cod, $atividades]);
    }
    header("Location: cbos.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM cadcbos WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: cbos.php");
    exit;
}

// Fetch all cadcbos records
$stmt = $pdo->query("SELECT * FROM cadcbos ORDER BY id DESC");
$cadcbos_items = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro Cadcbos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
</head>
<body style="background-color:white;">

 
         <div style="display: flex; min-height: 100vh;">
        <div id="sidebar" style="width: 220px; background-color: #333; color: white; padding-top: 20px; flex-shrink: 0;">
<a href="dashboard.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; font-size: 14px; letter-spacing: 0.05em;">Início</a>
<a href="cadastroaprendizes.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; font-size: 14px; letter-spacing: 0.05em;">Cadastro</a>
<a href="cbos.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; font-size: 14px; letter-spacing: 0.05em;">CBO</a>
<a href="empresas.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; font-size: 14px; letter-spacing: 0.05em;">Empresas</a>
<!--<a href="usuarios.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; font-size: 14px; letter-spacing: 0.05em;">Usuários</a></!-->
<a href="Contrato.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; font-size: 14px; letter-spacing: 0.05em;">Contrato Modelo</a>
<!--<a href="ficha.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; font-size: 14px; letter-spacing: 0.05em;">Ficha</a></!-->
<a href="logout.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; margin-top: 20px; font-size: 14px; letter-spacing: 0.05em;">Sair</a>
        </div>

    <div style="flex-grow: 1; padding: 20px; overflow-y: auto;">
        <h2>CBOS - Itens Cadastrados</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#cadcbosModal" onclick="openModal()">Novo</button>
        <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th style="width: 15%;">Cod</th>
                    <th style="width: 60%;">Atividades</th>
                    <th style="width: 25%;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cadcbos_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['cod']) ?></td>
                    <td class="text-start"><?= htmlspecialchars($item['atividades']) ?></td>
                    <td>
                       <!-- <button class="btn btn-sm btn-info me-1" onclick='viewItem(<?= json_encode($item) ?>)'>Visualizar</button></!-->
                        <button class="btn btn-sm btn-warning me-1" onclick='editItem(<?= json_encode($item) ?>)'>Alterar</button>
                        <a href="?delete=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirma exclusão?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="cadcbosModal" tabindex="-1" aria-labelledby="cadcbosModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <form method="post" id="cadcbosForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cadcbosModalLabel">Novo Cadastro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="itemId" />
        <div class="mb-3">
            <label for="cod" class="form-label">Cod</label>
            <input type="text" class="form-control" id="cod" name="cod" required />
        </div>
        <div class="mb-3">
            <label for="atividades" class="form-label">Atividades</label>
            <textarea class="form-control" id="atividades" name="atividades" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
    const cadcbosModal = new bootstrap.Modal(document.getElementById('cadcbosModal'));

    function openModal() {
        document.getElementById('cadcbosForm').reset();
        document.getElementById('itemId').value = '';
        cadcbosModal.show();
    }

    function editItem(item) {
        document.getElementById('itemId').value = item.id;
        document.getElementById('cod').value = item.cod;
        document.getElementById('atividades').value = item.atividades;
        cadcbosModal.show();
    }

    function viewItem(item) {
        alert('Cod: ' + item.cod + '\\nAtividades: ' + item.atividades);
    }
</script>

</body>
</html>
