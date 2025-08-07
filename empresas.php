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
    $cnpj = $_POST['cnpj'] ?? '';
    $rsocial = $_POST['rsocial'] ?? '';
    $nfantasia = $_POST['nfantasia'] ?? '';
    $email = $_POST['email'] ?? '';
    $rempresa = $_POST['rempresa'] ?? '';
    $raprendiz = $_POST['raprendiz'] ?? '';
    $telefoneemp = $_POST['telefoneemp'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $codatividade = $_POST['codatividade'] ?? '';

    if ($id) {
        $stmt = $pdo->prepare("UPDATE cadempresas SET cnpj = ?, rsocial = ?, nfantasia = ?, email = ?, rempresa = ?, raprendiz = ?, telefoneemp = ?, cep = ?, cidade = ?, endereco = ?, codatividade = ? WHERE id = ?");
        $stmt->execute([$cnpj, $rsocial, $nfantasia, $email, $rempresa, $raprendiz, $telefoneemp, $cep, $cidade, $endereco, $codatividade, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cadempresas (cnpj, rsocial, nfantasia, email, rempresa, raprendiz, telefoneemp, cep, cidade, endereco, codatividade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cnpj, $rsocial, $nfantasia, $email, $rempresa, $raprendiz, $telefoneemp, $cep, $cidade, $endereco, $codatividade]);
    }
    header("Location: empresas.php");
    exit;
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM cadempresas WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: empresas.php");
    exit;
}

// Fetch records with search
$searchQuery = '';
$empresas_items = [];
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchQuery = '%' . trim($_GET['search']) . '%';
    $stmt = $pdo->prepare("SELECT * FROM cadempresas WHERE cnpj LIKE ? OR rsocial LIKE ? OR nfantasia LIKE ? ORDER BY id DESC");
    $stmt->execute([$searchQuery, $searchQuery, $searchQuery]);
} else {
    $stmt = $pdo->query("SELECT * FROM cadempresas ORDER BY id DESC");
}
$empresas_items = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>IAQ - Gestão de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #00cc66;
            --accent-color: #ff6b35;
            --bg-light: #f8f9fa;
            --text-dark: #2c3e50;
            --border-color: #e9ecef;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }

        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, #004499 100%);
            color: white;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .main-content {
            background: white;
            margin: 20px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-left: 270px;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header-section p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            background: #0052a3;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(0, 102, 204, 0.05);
            transform: scale(1.01);
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-color: var(--border-color);
        }

        .action-buttons .btn {
            margin: 0 2px;
            padding: 8px 15px;
            font-size: 0.9rem;
            border-radius: 25px;
        }

        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: var(--shadow-hover);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
        }

        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        .logo-section {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .logo-section img {
            max-width: 120px;
            height: auto;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .stats-label {
            font-size: 1rem;
            color: var(--text-dark);
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            
            .main-content {
                margin: 10px;
                margin-left: 10px;
            }
            
            .header-section h1 {
                font-size: 2rem;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .search-container {
            position: relative;
            margin-bottom: 20px;
        }

        .search-input {
            border: 2px solid var(--border-color);
            border-radius: 50px;
            padding: 12px 20px 12px 45px;
            width: 100%;
            max-width: 400px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dark);
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div style="display: flex; min-height: 100vh;">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo-section">
                <img src="iaq.png" alt="IAQ Logo" class="img-fluid">
            </div>
            <nav class="nav flex-column px-3">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="cadastroaprendizes.php" class="nav-link">
                    <i class="fas fa-user-plus"></i> Cadastro
                </a>
                <a href="cbos.php" class="nav-link">
                    <i class="fas fa-list-alt"></i> CBOs
                </a>
                <a href="empresas.php" class="nav-link active">
                    <i class="fas fa-building"></i> Empresas
                </a>
                <a href="Contrato.php" class="nav-link">
                    <i class="fas fa-file-contract"></i> Contratos
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div style="flex-grow: 1;">
            <div class="main-content">
                <!-- Header Section -->
                <div class="header-section">
                    <h1><i class="fas fa-building"></i> Gestão de Empresas</h1>
                    <p>Sistema de Cadastro e Gerenciamento de Empresas Parceiras</p>
                </div>

                <!-- Stats Cards -->
                <div class="container-fluid p-4">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <div class="stats-number"><?= count($empresas_items) ?></div>
                                <div class="stats-label">Total de Empresas</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <div class="stats-number">100%</div>
                                <div class="stats-label">Ativas</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <div class="stats-number">IAQ</div>
                                <div class="stats-label">Sistema</div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0"><i class="fas fa-table"></i> Lista de Empresas</h3>
                        <div class="d-flex align-items-center gap-3">
                            <div class="search-container">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" id="searchInput" placeholder="Pesquisar empresas...">
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#empresaModal" onclick="openModal()">
                                <i class="fas fa-plus"></i> Nova Empresa
                            </button>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive fade-in">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-id-card"></i> CNPJ</th>
                                    <th><i class="fas fa-building"></i> Razão Social</th>
                                    <th><i class="fas fa-store"></i> Nome Fantasia</th>
                                    <th><i class="fas fa-envelope"></i> E-mail</th>
                                    <th><i class="fas fa-cogs"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($empresas_items as $item): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($item['cnpj']) ?></strong></td>
                                    <td><?= htmlspecialchars($item['rsocial']) ?></td>
                                    <td><?= htmlspecialchars($item['nfantasia']) ?></td>
                                    <td><?= htmlspecialchars($item['email']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-warning" onclick='editItem(<?= json_encode($item) ?>)'>
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <a href="?delete_id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirma exclusão?')">
                                                <i class="fas fa-trash"></i> Excluir
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="empresaModal" tabindex="-1" aria-labelledby="empresaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="post" id="empresaForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="empresaModalLabel">
                        <i class="fas fa-plus-circle"></i> Nova Empresa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="itemId" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cnpj" class="form-label">
                                    <i class="fas fa-id-card"></i> CNPJ
                                </label>
                                <input type="text" class="form-control" id="cnpj" name="cnpj" required 
                                       placeholder="Digite o CNPJ da empresa" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rsocial" class="form-label">
                                    <i class="fas fa-building"></i> Razão Social
                                </label>
                                <input type="text" class="form-control" id="rsocial" name="rsocial" required 
                                       placeholder="Digite a razão social" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nfantasia" class="form-label">
                                    <i class="fas fa-store"></i> Nome Fantasia
                                </label>
                                <input type="text" class="form-control" id="nfantasia" name="nfantasia" 
                                       placeholder="Digite o nome fantasia" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> E-mail
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Digite o e-mail" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rempresa" class="form-label">
                                    <i class="fas fa-user-tie"></i> Responsável Empresa
                                </label>
                                <input type="text" class="form-control" id="rempresa" name="rempresa" 
                                       placeholder="Nome do responsável" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="raprendiz" class="form-label">
                                    <i class="fas fa-user-graduate"></i> Responsável Aprendiz
                                </label>
                                <input type="text" class="form-control" id="raprendiz" name="raprendiz" 
                                       placeholder="Nome do responsável pelo aprendiz" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefoneemp" class="form-label">
                                    <i class="fas fa-phone"></i> Telefone
                                </label>
                                <input type="text" class="form-control" id="telefoneemp" name="telefoneemp" 
                                       placeholder="Telefone da empresa" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cep" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> CEP
                                </label>
                                <input type="text" class="form-control" id="cep" name="cep" 
                                       placeholder="Digite o CEP" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cidade" class="form-label">
                                    <i class="fas fa-city"></i> Cidade
                                </label>
                                <input type="text" class="form-control" id="cidade" name="cidade" 
                                       placeholder="Digite a cidade" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endereco" class="form-label">
                                    <i class="fas fa-road"></i> Endereço
                                </label>
                                <input type="text" class="form-control" id="endereco" name="endereco" 
                                       placeholder="Digite o endereço" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="codatividade" class="form-label">
                                    <i class="fas fa-barcode"></i> Código Atividade
                                </label>
                                <input type="text" class="form-control" id="codatividade" name="codatividade" 
                                       placeholder="Digite o código da atividade" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        // Adicionar classe fade-in aos elementos
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.table tbody tr');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
                el.classList.add('fade-in');
            });
        });

        const empresaModal = new bootstrap.Modal(document.getElementById('empresaModal'));

        function openModal() {
            document.getElementById('empresaForm').reset();
            document.getElementById('itemId').value = '';
            document.getElementById('empresaModalLabel').innerHTML = '<i class="fas fa-plus-circle"></i> Nova Empresa';
            empresaModal.show();
        }

        function editItem(item) {
            document.getElementById('itemId').value = item.id;
            document.getElementById('cnpj').value = item.cnpj;
            document.getElementById('rsocial').value = item.rsocial;
            document.getElementById('nfantasia').value = item.nfantasia;
            document.getElementById('email').value = item.email;
            document.getElementById('rempresa').value = item.rempresa;
            document.getElementById('raprendiz').value = item.raprendiz;
            document.getElementById('telefoneemp').value = item.telefoneemp;
            document.getElementById('cep').value = item.cep;
            document.getElementById('cidade').value = item.cidade;
            document.getElementById('endereco').value = item.endereco;
            document.getElementById('codatividade').value = item.codatividade;
            
            document.getElementById('empresaModalLabel').innerHTML = '<i class="fas fa-edit"></i> Editar Empresa';
            empresaModal.show();
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Adicionar feedback visual ao salvar
        document.getElementById('empresaForm').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
<question>Should I proceed with applying the modern teste.php layout to empresas.php? This will completely transform the appearance and user experience of the empresas page.</question>
</ask_followup_question>
