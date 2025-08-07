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

// Get candidate ID from URL
$candidate_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch candidate data
$stmt = $pdo->prepare("SELECT * FROM cadcandidato WHERE id = ?");
$stmt->execute([$candidate_id]);
$candidate = $stmt->fetch();

if (!$candidate) {
    die("Aprendiz não encontrado!");
}

// Handle form submission
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
    $foto = $candidate['foto']; // Keep existing photo if no new upload
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
            // Delete old photo if exists
            if ($candidate['foto'] && file_exists($uploadDir . $candidate['foto'])) {
                unlink($uploadDir . $candidate['foto']);
            }
            $foto = $newFileName;
        }
    }

    // Update record
    $stmt = $pdo->prepare("UPDATE cadcandidato SET 
        nome=?, mae=?, pai=?, nascimento=?, telefone=?, telefone2=?, sexo=?, email=?, cpf=?, 
        cep=?, cidade=?, endereco=?, nctps=?, sctps=?, nescolaridade=?, escola=?, reservista=?, 
        dfcontratacao=?, jornada=?, hrtrabalho=?, salario=?, dtcontratacao=?, duracaodocurso=?, 
        dtrabalho=?, dcurso=?, hrcurso=?, dtcursoinicial=?, dtcursofinal=?, foto=?, cbo=?, empresa=? 
        WHERE id=?");
    $stmt->execute([
        $nome, $mae, $pai, $nascimento, $telefone, $telefone2, $sexo, $email, $cpf,
        $cep, $cidade, $endereco, $nctps, $sctps, $nescolaridade, $escola, $reservista,
        $dfcontratacao, $jornada, $hrtrabalho, $salario, $dtcontratacao, $duracaodocurso,
        $dtrabalho, $dcurso, $hrcurso, $dtcursoinicial, $dtcursofinal, $foto, $cbo, $empresa, $id
    ]);

    $_SESSION['success'] = "Dados do aprendiz atualizados com sucesso!";
    header("Location: cadastroaprendizes.php");
    exit;
}

$empresaMap = [];
foreach ($cadempresas as $empresa) {
    $empresaMap[$empresa['id']] = $empresa['rsocial'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IAQ - Editar Aprendiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }

        .edit-container {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin: 20px auto;
            max-width: 1200px;
            overflow: hidden;
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

        .form-section {
            padding: 40px;
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

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #0052a3;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
        }

        .photo-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            margin: 10px 0;
        }

        .required::after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="edit-container">
            <!-- Header Section -->
            <div class="header-section">
                <h1><i class="fas fa-edit"></i> Editar Aprendiz</h1>
                <p>Atualize os dados do aprendiz</p>
            </div>

            <!-- Form Section -->
            <div class="form-section">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $candidate['id'] ?>">
                    
                    <h4 class="section-title"><i class="fas fa-user"></i> Dados Pessoais</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label required">Nome Completo</label>
                            <input type="text" class="form-control" name="nome" id="nome" value="<?= htmlspecialchars($candidate['nome']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mae" class="form-label">Nome da Mãe</label>
                            <input type="text" class="form-control" name="mae" id="mae" value="<?= htmlspecialchars($candidate['mae']) ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="pai" class="form-label">Nome do Pai</label>
                            <input type="text" class="form-control" name="pai" id="pai" value="<?= htmlspecialchars($candidate['pai']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" name="nascimento" id="nascimento" value="<?= htmlspecialchars($candidate['nascimento']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-select" name="sexo" id="sexo">
                                <option value="Masculino" <?= $candidate['sexo'] == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                <option value="Feminino" <?= $candidate['sexo'] == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                                <option value="Não informar" <?= $candidate['sexo'] == 'Não informar' ? 'selected' : '' ?>>Não informar</option>
                            </select>
                        </div>
                    </div>

                    <h4 class="section-title"><i class="fas fa-id-card"></i> Documentos</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cpf" class="form-label required">CPF</label>
                            <input type="text" class="form-control" name="cpf" id="cpf" value="<?= htmlspecialchars($candidate['cpf']) ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="nctps" class="form-label">Nº CTPS</label>
                            <input type="text" class="form-control" name="nctps" id="nctps" value="<?= htmlspecialchars($candidate['nctps']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sctps" class="form-label">Série CTPS</label>
                            <input type="text" class="form-control" name="sctps" id="sctps" value="<?= htmlspecialchars($candidate['sctps']) ?>">
                        </div>
                    </div>

                    <h4 class="section-title"><i class="fas fa-map-marker-alt"></i> Endereço</h4>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control" name="cep" id="cep" value="<?= htmlspecialchars($candidate['cep']) ?>">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control" name="endereco" id="endereco" value="<?= htmlspecialchars($candidate['endereco']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" name="cidade" id="cidade" value="<?= htmlspecialchars($candidate['cidade']) ?>">
                        </div>
                    </div>

                    <h4 class="section-title"><i class="fas fa-phone"></i> Contato</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="tel" class="form-control" name="telefone" id="telefone" value="<?= htmlspecialchars($candidate['telefone']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefone2" class="form-label">Telefone 2</label>
                            <input type="tel" class="form-control" name="telefone2" id="telefone2" value="<?= htmlspecialchars($candidate['telefone2']) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($candidate['email']) ?>">
                    </div>

                    
                    <h4 class="section-title"><i class="fas fa-briefcase"></i> Informações Profissionais</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="empresa" class="form-label">Empresa</label>
                            <select class="form-select" name="empresa" id="empresa">
                                <option value="">Selecione a Empresa</option>
                                <?php foreach ($cadempresas as $empresa): ?>
                                    <option value="<?= htmlspecialchars($empresa['id']) ?>" <?= $candidate['empresa'] == $empresa['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($empresa['rsocial']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cbo" class="form-label">CBO</label>
                            <select class="form-select" name="cbo" id="cbo">
                                <option value="">Selecione o CBO</option>
                                <?php foreach ($cadcbos as $cbo): ?>
                                    <option value="<?= htmlspecialchars($cbo['id']) ?>" <?= $candidate['cbo'] == $cbo['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cbo['cod'] . ' - ' . $cbo['atividades']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <h4 class="section-title"><i class="fas fa-clock"></i> Informações do Contrato</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="dtcontratacao" class="form-label">Data de Contratação</label>
                            <input type="date" class="form-control" name="dtcontratacao" id="dtcontratacao" value="<?= htmlspecialchars($candidate['dtcontratacao']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="dfcontratacao" class="form-label">Data de Encerramento</label>
                            <input type="date" class="form-control" name="dfcontratacao" id="dfcontratacao" value="<?= htmlspecialchars($candidate['dfcontratacao']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="salario" class="form-label">Salário</label>
                            <input type="text" class="form-control" name="salario" id="salario" value="<?= htmlspecialchars($candidate['salario']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jornada" class="form-label">Jornada</label>
                            <select class="form-select" name="jornada" id="jornada">
                                <option value="Segunda a Sexta" <?= $candidate['jornada'] == 'Segunda a Sexta' ? 'selected' : '' ?>>Segunda a Sexta</option>
                                <option value="Segunda a Sábado" <?= $candidate['jornada'] == 'Segunda a Sábado' ? 'selected' : '' ?>>Segunda a Sábado</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hrtrabalho" class="form-label">Horas de Trabalho</label>
                            <select class="form-select" name="hrtrabalho" id="hrtrabalho">
                                <option value="4 horas" <?= $candidate['hrtrabalho'] == '4 horas' ? 'selected' : '' ?>>4 horas</option>
                                <option value="6 horas" <?= $candidate['hrtrabalho'] == '6 horas' ? 'selected' : '' ?>>6 horas</option>
                                <option value="8 horas" <?= $candidate['hrtrabalho'] == '8 horas' ? 'selected' : '' ?>>8 horas</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dcurso" class="form-label">Dia do Curso</label>
                            <select class="form-select" name="dcurso" id="dcurso">
                                <option value="Segunda" <?= $candidate['dcurso'] == 'Segunda' ? 'selected' : '' ?>>Segunda</option>
                                <option value="Terça" <?= $candidate['dcurso'] == 'Terça' ? 'selected' : '' ?>>Terça</option>
                                <option value="Quarta" <?= $candidate['dcurso'] == 'Quarta' ? 'selected' : '' ?>>Quarta</option>
                                <option value="Quinta" <?= $candidate['dcurso'] == 'Quinta' ? 'selected' : '' ?>>Quinta</option>
                                <option value="Sexta" <?= $candidate['dcurso'] == 'Sexta' ? 'selected' : '' ?>>Sexta</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hrcurso" class="form-label">Horário do Curso</label>
                            <input type="text" class="form-control" name="hrcurso" id="hrcurso" value="<?= htmlspecialchars($candidate['hrcurso']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dtcursoinicial" class="form-label">Data Início Módulo Básico</label>
                            <input type="date" class="form-control" name="dtcursoinicial" id="dtcursoinicial" value="<?= htmlspecialchars($candidate['dtcursoinicial']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dtcursofinal" class="form-label">Data Fim Módulo Básico</label>
                            <input type="date" class="form-control" name="dtcursofinal" id="dtcursofinal" value="<?= htmlspecialchars($candidate['dtcursofinal']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duracaodocurso" class="form-label">Duração do Contrato</label>
                            <input type="text" class="form-control" name="duracaodocurso" id="duracaodocurso" value="<?= htmlspecialchars($candidate['duracaodocurso']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dtrabalho" class="form-label">Horário do Trabalho</label>
                            <input type="text" class="form-control" name="dtrabalho" id="dtrabalho" value="<?= htmlspecialchars($candidate['dtrabalho']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nescolaridade" class="form-label">Nível Escolaridade</label>
                            <select class="form-select" name="nescolaridade" id="nescolaridade">
                                <option value="Fundamental" <?= $candidate['nescolaridade'] == 'Fundamental' ? 'selected' : '' ?>>Fundamental</option>
                                <option value="Médio Incompleto" <?= $candidate['nescolaridade'] == 'Médio Incompleto' ? 'selected' : '' ?>>Médio Incompleto</option>
                                <option value="Médio Completo" <?= $candidate['nescolaridade'] == 'Médio Completo' ? 'selected' : '' ?>>Médio Completo</option>
                                <option value="Superior Incompleto" <?= $candidate['nescolaridade'] == 'Superior Incompleto' ? 'selected' : '' ?>>Superior Incompleto</option>
                                <option value="Superior Completo" <?= $candidate['nescolaridade'] == 'Superior Completo' ? 'selected' : '' ?>>Superior Completo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="escola" class="form-label">Escola</label>
                            <input type="text" class="form-control" name="escola" id="escola" value="<?= htmlspecialchars($candidate['escola']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reservista" class="form-label">Reservista</label>
                            <select class="form-select" name="reservista" id="reservista">
                                <option value="Sim" <?= $candidate['reservista'] == 'Sim' ? 'selected' : '' ?>>Sim</option>
                                <option value="Não" <?= $candidate['reservista'] == 'Não' ? 'selected' : '' ?>>Não</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="escola" class="form-label">Escola</label>
                            <input type="text" class="form-control" name="escola" id="escola" value="<?= htmlspecialchars($candidate['escola']) ?>">
                        </div>
                    </div>

                    <h4 class="section-title"><i class="fas fa-camera"></i> Foto</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="foto" class="form-label">Alterar Foto</label>
                            <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <?php if (!empty($candidate['foto'])): ?>
                                <img src="uploads/<?= htmlspecialchars($candidate['foto']) ?>" alt="Foto Atual" class="photo-preview">
                                <p class="text-muted">Foto atual</p>
                            <?php else: ?>
                                <p class="text-muted">Nenhuma foto cadastrada</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Botões Salvar e Voltar -->
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                            <a href="cadastroaprendizes.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
