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

// Select CBO
$stmtCbos = $pdo->query("SELECT id, cod, atividades FROM cadcbos ORDER BY cod");
$cadcbos = $stmtCbos->fetchAll();

// Select Empresas
$stmtEmpresas = $pdo->query("SELECT id, rsocial FROM cadempresas ORDER BY rsocial");
$cadempresas = $stmtEmpresas->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nome = $_POST['nome'] ?? '';
    $mae = $_POST['mae'] ?? '';
    $pai = $_POST['pai'] ?? '';
    $nascimento = $_POST['nascimento'] ?? null;
    $telefone = $_POST['telefone'] ?? '';
    $telefone2 = $_POST['telefone2'] ?? '';
    $sexo = $_POST['sexo'] ?? 'Não informar';
    $email = $_POST['email'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $nctps = $_POST['nctps'] ?? '';
    $sctps = $_POST['sctps'] ?? '';
    $nescolaridade = $_POST['nescolaridade'] ?? null;
    $escola = $_POST['escola'] ?? '';
    $reservista = $_POST['reservista'] ?? 'Não';
    $dfcontratacao = $_POST['dfcontratacao'] ?? '';
    $jornada = $_POST['jornada'] ?? '';
    $hrtrabalho = $_POST['hrtrabalho'] ?? '';
    $salario = $_POST['salario'] ?? '';
    $dtcontratacao = $_POST['dtcontratacao'] ?? null;
    $duracaodocurso = $_POST['duracaodocurso'] ?? '';
    $dtrabalho = $_POST['dtrabalho'] ?? '';
    $dcurso = $_POST['dcurso'] ?? '';
    $hrcurso = $_POST['hrcurso'] ?? '';
    $dtcursoinicial = $_POST['dtcursoinicial'] ?? null;
    $dtcursofinal = $_POST['dtcursofinal'] ?? null;
    $cbo = $_POST['cbo'] ?? null;
    $empresa = $_POST['empresa'] ?? null;

    // Handle file upload
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = basename($_FILES['foto']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid('foto_', true) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $foto = $newFileName;
        }
    }

    if ($id) {
        // Update record
        if ($foto) {
            $stmt = $pdo->prepare("UPDATE cadcandidato SET nome=?, mae=?, pai=?, nascimento=?, telefone=?, telefone2=?, sexo=?, email=?, cpf=?, cep=?, cidade=?, endereco=?, nctps=?, sctps=?, nescolaridade=?, escola=?, reservista=?, dfcontratacao=?, jornada=?, hrtrabalho=?, salario=?, dtcontratacao=?, duracaodocurso=?, dtrabalho=?, dcurso=?, hrcurso=?, dtcursoinicial=?, dtcursofinal=?, foto=?, cbo=?, empresa=? WHERE id=?");
            $stmt->execute([$nome, $mae, $pai, $nascimento, $telefone, $telefone2, $sexo, $email, $cpf, $cep, $cidade, $endereco, $nctps, $sctps, $nescolaridade, $escola, $reservista, $dfcontratacao, $jornada, $hrtrabalho, $salario, $dtcontratacao, $duracaodocurso, $dtrabalho, $dcurso, $hrcurso, $dtcursoinicial, $dtcursofinal, $foto, $cbo, $empresa, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE cadcandidato SET nome=?, mae=?, pai=?, nascimento=?, telefone=?, telefone2=?, sexo=?, email=?, cpf=?, cep=?, cidade=?, endereco=?, nctps=?, sctps=?, nescolaridade=?, escola=?, reservista=?, dfcontratacao=?, jornada=?, hrtrabalho=?, salario=?, dtcontratacao=?, duracaodocurso=?, dtrabalho=?, dcurso=?, hrcurso=?, dtcursoinicial=?, dtcursofinal=?, cbo=?, empresa=? WHERE id=?");
            $stmt->execute([$nome, $mae, $pai, $nascimento, $telefone, $telefone2, $sexo, $email, $cpf, $cep, $cidade, $endereco, $nctps, $sctps, $nescolaridade, $escola, $reservista, $dfcontratacao, $jornada, $hrtrabalho, $salario, $dtcontratacao, $duracaodocurso, $dtrabalho, $dcurso, $hrcurso, $dtcursoinicial, $dtcursofinal, $cbo, $empresa, $id]);
        }
    } else {
        // Insert new record
        $stmt = $pdo->prepare("INSERT INTO cadcandidato (nome, mae, pai, nascimento, telefone, telefone2, sexo, email, cpf, cep, cidade, endereco, nctps, sctps, nescolaridade, escola, reservista, dfcontratacao, jornada, hrtrabalho, salario, dtcontratacao, duracaodocurso, dtrabalho, dcurso, hrcurso, dtcursoinicial, dtcursofinal, cbo, empresa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $mae, $pai, $nascimento, $telefone, $telefone2, $sexo, $email, $cpf, $cep, $cidade, $endereco, $nctps, $sctps, $nescolaridade, $escola, $reservista, $dfcontratacao, $jornada, $hrtrabalho, $salario, $dtcontratacao, $duracaodocurso, $dtrabalho, $dcurso, $hrcurso, $dtcursoinicial, $dtcursofinal, $cbo, $empresa]);
    }
    header("Location: cadastroaprendizes.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM cadcandidato WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: cadastroaprendizes.php");
    exit;
}

// Fetch all candidates
$stmt = $pdo->query("SELECT * FROM cadcandidato ORDER BY id DESC");
$candidates = $stmt->fetchAll();

$empresaMap = [];
foreach ($cadempresas as $empresa) {
    $empresaMap[$empresa['id']] = $empresa['rsocial'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>IAQ - Cadastro de Aprendizes</title>
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
            flex-shrink: 0;
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
            flex-grow: 1;
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

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stats-label {
            color: var(--text-dark);
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin: 10px;
            }
            
            .header-section h1 {
                font-size: 2rem;
            }
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
                <a href="cadastroaprendizes.php" class="nav-link active">
                    <i class="fas fa-user-plus"></i> Cadastro
                </a>
                <a href="cbos.php" class="nav-link">
                    <i class="fas fa-list-alt"></i> CBOs
                </a>
                <a href="empresas.php" class="nav-link">
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
                    <h1><i class="fas fa-user-plus"></i> Cadastro de Aprendizes</h1>
                    <p>Sistema de Cadastro e Gerenciamento de Aprendizes</p>
                </div>

                <!-- Stats Cards -->
                <div class="container-fluid p-4">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <div class="stats-number"><?= count($candidates) ?></div>
                                <div class="stats-label">Total de Aprendizes</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <div class="stats-number">100%</div>
                                <div class="stats-label">Ativos</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <div class="stats-number">IAQ</div>
                                <div class="stats-label">Sistema</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0"><i class="fas fa-table"></i> Lista de Aprendizes</h3>
                        <div class="d-flex gap-2">
                            <button class="btn btn-info" id="btnBuscar" onclick="toggleSearch()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#candidateModal" onclick="openModal()">
                                <i class="fas fa-plus"></i> Novo Aprendiz
                            </button>
                        </div>
                    </div>

                    <!-- Search Field -->
                    <div id="searchContainer" style="display: none;" class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Digite o nome para buscar..." onkeyup="filterTable()">
                            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive fade-in">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-image"></i> Foto</th>
                                    <th><i class="fas fa-user"></i> Nome</th>
                                    <th><i class="fas fa-phone"></i> Telefone</th>
                                    <th><i class="fas fa-building"></i> Empresa</th>
                                    <th><i class="fas fa-graduation-cap"></i> Curso</th>
                                    <th><i class="fas fa-city"></i> Cidade</th>
                                    <th><i class="fas fa-cogs"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($candidates as $candidate): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if (!empty($candidate['foto'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($candidate['foto']) ?>" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;" />
                                        <?php else: ?>
                                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($candidate['nome']) ?></strong></td>
                                    <td><?= htmlspecialchars($candidate['telefone']) ?></td>
                                    <td><?= htmlspecialchars($empresaMap[$candidate['empresa']] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($candidate['dcurso']) ?></td>
                                    <td><?= htmlspecialchars($candidate['cidade']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                        <a href="editaprendizes.php?id=<?= $candidate['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="cadastroaprendizes.php?delete=<?= $candidate['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirma exclusão?')">
                                                <i class="fas fa-trash"></i> Excluir
                                            </a>
                                            <a href="generate_contract.php?id=<?= $candidate['id'] ?>" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fas fa-file-contract"></i> Contrato
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
    <div class="modal fade" id="candidateModal" tabindex="-1" aria-labelledby="candidateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="post" id="candidateForm" class="modal-content" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="candidateModalLabel">
                        <i class="fas fa-user-plus"></i> Cadastro de Aprendiz
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="candidateId" />
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" name="nome" id="nome" required />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mae" class="form-label">Nome da Mãe</label>
                            <input type="text" class="form-control" name="mae" id="mae" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pai" class="form-label">Nome da Pai</label>
                            <input type="text" class="form-control" name="pai" id="pai" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" name="nascimento" id="nascimento" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control" name="cpf" id="cpf" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-select" name="sexo" id="sexo">
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                                <option value="Não informar">Não informar</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="tel" class="form-control" name="telefone" id="telefone" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefone2" class="form-label">Telefone 2</label>
                            <input type="tel" class="form-control" name="telefone2" id="telefone2" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" class="form-control" name="foto" id="foto" accept="image/*" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control" name="cep" id="cep" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" name="cidade" id="cidade" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control" name="endereco" id="endereco" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="empresa" class="form-label">Empresa</label>
                            <select class="form-select" name="empresa" id="empresa">
                                <option value="">Selecione a Empresa</option>
                                <?php foreach ($cadempresas as $empresa): ?>
                                    <option value="<?= htmlspecialchars($empresa['id']) ?>"><?= htmlspecialchars($empresa['rsocial']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cbo" class="form-label">CBO</label>
                            <select class="form-select" name="cbo" id="cbo">
                                <option value="">Selecione o CBO</option>
                                <?php foreach ($cadcbos as $cbo): ?>
                                    <option value="<?= htmlspecialchars($cbo['id']) ?>"><?= htmlspecialchars($cbo['cod'] . ' - ' . $cbo['atividades']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nctps" class="form-label">Nº CTPS</label>
                            <input type="text" class="form-control" name="nctps" id="nctps" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sctps" class="form-label">Série CTPS</label>
                            <input type="text" class="form-control" name="sctps" id="sctps" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nescolaridade" class="form-label">Nível Escolaridade</label>
                            <select class="form-select" name="nescolaridade" id="nescolaridade">
                                <option value="Fundamental">Fundamental</option>
                                <option value="Médio Incompleto">Médio Incompleto</option>
                                <option value="Médio Completo">Médio Completo</option>
                                <option value="Superior Incompleto">Superior Incompleto</option>
                                <option value="Superior Completo">Superior Completo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="escola" class="form-label">Escola</label>
                            <input type="text" class="form-control" name="escola" id="escola" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reservista" class="form-label">Reservista</label>
                            <select class="form-select" name="reservista" id="reservista">
                                <option value="Sim">Sim</option>
                                <option value="Não" selected>Não</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jornada" class="form-label">Jornada</label>
                            <select class="form-select" name="jornada" id="jornada">
                                <option value="Segunda a Sexta">Segunda a Sexta</option>
                                <option value="Segunda a Sábado">Segunda a Sábado</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hrtrabalho" class="form-label">Horas de Trabalho</label>
                            <select class="form-select" name="hrtrabalho" id="hrtrabalho">
                                <option value="4 horas">4 horas</option>
                                <option value="6 horas">6 horas</option>
                                <option value="8 horas">8 horas</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salario" class="form-label">Salário</label>
                            <input type="text" class="form-control" name="salario" id="salario" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dtcontratacao" class="form-label">Data de Contratação</label>
                            <input type="date" class="form-control" name="dtcontratacao" id="dtcontratacao" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dfcontratacao" class="form-label">Data de Encerramento</label>
                            <input type="date" class="form-control" name="dfcontratacao" id="dfcontratacao" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duracaodocurso" class="form-label">Duração do Contrato</label>
                            <input type="text" class="form-control" name="duracaodocurso" id="duracaodocurso" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dtrabalho" class="form-label">Horário do Trabalho</label>
                            <input type="text" class="form-control" name="dtrabalho" id="dtrabalho" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dcurso" class="form-label">Dia do Curso</label>
                            <select class="form-select" name="dcurso" id="dcurso">
                                <option value="Segunda">Segunda</option>
                                <option value="Terça">Terça</option>
                                <option value="Quarta">Quarta</option>
                                <option value="Quinta">Quinta</option>
                                <option value="Sexta">Sexta</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hrcurso" class="form-label">Horário do Curso</label>
                            <input type="text" class="form-control" name="hrcurso" id="hrcurso" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dtcursoinicial" class="form-label">Data Início Módulo Básico</label>
                            <input type="date" class="form-control" name="dtcursoinicial" id="dtcursoinicial" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dtcursofinal" class="form-label">Data Fim Módulo Básico</label>
                            <input type="date" class="form-control" name="dtcursofinal" id="dtcursofinal" />
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
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

        // Search functionality
        function toggleSearch() {
            const searchContainer = document.getElementById('searchContainer');
            const searchInput = document.getElementById('searchInput');
            
            if (searchContainer.style.display === 'none') {
                searchContainer.style.display = 'block';
                searchInput.focus();
            } else {
                searchContainer.style.display = 'none';
                searchInput.value = '';
                filterTable(); // Clear filter when hiding
            }
        }

        function clearSearch() {
            const searchInput = document.getElementById('searchInput');
            searchInput.value = '';
            filterTable();
            document.getElementById('searchContainer').style.display = 'none';
        }

        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const table = document.querySelector('.table tbody');
            const rows = table.getElementsByTagName('tr');
            
            let visibleCount = 0;
            
            for (let i = 0; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[1]; // Nome column
                if (nameCell) {
                    const nameText = nameCell.textContent || nameCell.innerText;
                    if (nameText.toLowerCase().indexOf(searchTerm) > -1) {
                        rows[i].style.display = '';
                        visibleCount++;
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
            
            // Update stats if needed
            const statsNumber = document.querySelector('.stats-number');
            if (statsNumber) {
                statsNumber.textContent = visibleCount;
            }
        }

        // Close search when clicking outside
        document.addEventListener('click', function(event) {
            const searchContainer = document.getElementById('searchContainer');
            const btnBuscar = document.getElementById('btnBuscar');
            
            if (!searchContainer.contains(event.target) && !btnBuscar.contains(event.target)) {
                if (searchContainer.style.display === 'block' && document.getElementById('searchInput').value === '') {
                    searchContainer.style.display = 'none';
                }
            }
        });
    </script>


    