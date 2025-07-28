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

// Fetch cadcbos for cbo select
$stmtCbos = $pdo->query("SELECT id, cod, atividades FROM cadcbos ORDER BY cod");
$cadcbos = $stmtCbos->fetchAll();

// Fetch cadempresas for empresa select
$stmtEmpresas = $pdo->query("SELECT id, rsocial FROM cadempresas ORDER BY rsocial");
$cadempresas = $stmtEmpresas->fetchAll();

// Handle form submissions for create and update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'] ?? '';
        $mae = $_POST['mae'] ?? '';
        $pai = $_POST['pai'] ?? '';
        $nascimento = $_POST['nascimento'] ?? null;
        $telefone = $_POST['telefone'] ?? '';
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

        // Validate cbo and empresa to allow null or empty string
        if ($cbo === '') {
            $cbo = null;
        }
        if ($empresa === '') {
            $empresa = null;
        }

        // Handle photo upload
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
            // Update existing record
            if ($foto) {
                $stmt = $pdo->prepare("UPDATE cadcandidato SET nome=?, mae=?, pai=?, nascimento=?, telefone=?, sexo=?, email=?, cpf=?, cep=?, cidade=?, endereco=?, nctps=?, sctps=?, nescolaridade=?, escola=?, reservista=?, dfcontratacao=?, jornada=?, hrtrabalho=?, salario=?, dtcontratacao=?, duracaodocurso=?, dtrabalho=?, dcurso=?, hrcurso=?, dtcursoinicial=?, dtcursofinal=?, foto=?, cbo=?, empresa=? WHERE id=?");
                $stmt->execute([$nome, $mae, $pai, $nascimento, $telefone, $sexo, $email, $cpf, $cep, $cidade, $endereco, $nctps, $sctps, $nescolaridade, $escola, $reservista, $dfcontratacao, $jornada, $hrtrabalho, $salario, $dtcontratacao, $duracaodocurso, $dtrabalho, $dcurso, $hrcurso, $dtcursoinicial, $dtcursofinal, $foto, $cbo, $empresa, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE cadcandidato SET nome=?, mae=?, pai=?, nascimento=?, telefone=?, sexo=?, email=?, cpf=?, cep=?, cidade=?, endereco=?, nctps=?, sctps=?, nescolaridade=?, escola=?, reservista=?, dfcontratacao=?, jornada=?, hrtrabalho=?, salario=?, dtcontratacao=?, duracaodocurso=?, dtrabalho=?, dcurso=?, hrcurso=?, dtcursoinicial=?, dtcursofinal=?, cbo=?, empresa=? WHERE id=?");
                $stmt->execute([$nome, $mae, $pai, $nascimento, $telefone, $sexo, $email, $cpf, $cep, $cidade, $endereco, $nctps, $sctps, $nescolaridade, $escola, $reservista, $dfcontratacao, $jornada, $hrtrabalho, $salario, $dtcontratacao, $duracaodocurso, $dtrabalho, $dcurso, $hrcurso, $dtcursoinicial, $dtcursofinal, $cbo, $empresa, $id]);
            }
        } else {
            // Insert new record
            $stmt = $pdo->prepare("INSERT INTO cadcandidato (nome, mae, pai, nascimento, telefone, sexo, email, cpf, cep, cidade, endereco, nctps, sctps, nescolaridade, escola, reservista, dfcontratacao, jornada, hrtrabalho, salario, dtcontratacao, duracaodocurso, dtrabalho, dcurso, hrcurso, dtcursoinicial, dtcursofinal, cbo, empresa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $mae, $pai, $nascimento, $telefone, $sexo, $email, $cpf, $cep, $cidade, $endereco, $nctps, $sctps, $nescolaridade, $escola, $reservista, $dfcontratacao, $jornada, $hrtrabalho, $salario, $dtcontratacao, $duracaodocurso, $dtrabalho, $dcurso, $hrcurso, $dtcursoinicial, $dtcursofinal, $cbo, $empresa]);
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

// Fetch all candidates with full fields for editing
$stmt = $pdo->query("SELECT * FROM cadcandidato ORDER BY id DESC");
$candidates = $stmt->fetchAll();

?>
<html lang="en">

<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
     <title>Cadastro de Candidatos</title></H1>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
</head>

    
</nav>

<div class="d-flex">
 <body style="background-color:white;">

 <div style="display: flex; min-height: 100vh;">
        <div id="sidebar" style="width: 220px; background-color: #333; color: white; padding-top: 20px; flex-shrink: 0;">
            <a href="dashboard.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Início</a>
            <a href="cadastroaprendizes.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Cadastro</a>
            <a href="cbos.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">CBO</a>
            <a href="empresas.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Empresas</a>
            <a href="usuarios.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Usuários</a>
            <a href="Contrato.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Contrato Modelo</a>
            <a href="ficha.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Ficha</a>
            <a href="usuarios.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Usuários</a>
            <a href="logout.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; margin-top: 20px;">Sair</a>
        </div>

        <div style="flex-grow: 1; padding: 20px;">
           
  </div>
</div>

</body>
  <div class="container mt-4" style="margin-left: 220px; width: calc(100% - 220px);">
    <h2>Cadastro de Candidatos</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#candidateModal" onclick="openModal()">Novo</button>
    <button id="btnPesquisa" class="btn btn-secondary mb-3" type="button">Pesquisar Nome</button>
    <input type="text" id="inputPesquisa" class="form-control mb-3" placeholder="Pesquisar por nome" style="display:none; max-width: 300px;" />
    <table class="table table-bordered">
        <thead>
            <tr class="text-center">
                <th>Foto</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Cidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidates as $candidate): ?>
            <tr>
                <td class="text-center">
                    <?php if (!empty($candidate['foto'])): ?>
                        <img src="uploads/<?= htmlspecialchars($candidate['foto']) ?>" alt="Foto do Candidato" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;" />
                    <?php else: ?>
                        <span>Sem Foto</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($candidate['nome']) ?></td>
                <td><?= htmlspecialchars($candidate['cpf']) ?></td>
                <td><?= htmlspecialchars($candidate['cidade']) ?></td>
<td class="text-center">
    <button class="btn btn-sm btn-warning" onclick='editCandidate(<?= json_encode($candidate) ?>)'>Editar</button>
    <!--<button class="btn btn-sm btn-info" onclick='viewCandidate(<?= json_encode($candidate) ?>)'>Visualizar</button></!-->
    <a href="cadastroaprendizes.php?delete=<?= $candidate['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>

    <a href="generate_contract.php?id=<?= $candidate['id'] ?>" target="_blank" class="btn btn-sm btn-primary">Gerar Contrato</a>
</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="candidateModal" tabindex="-1" aria-labelledby="candidateModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <form method="post" id="candidateForm" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title" id="candidateModalLabel">Cadastro de Candidato</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="candidateId" />
        <div class="row mb-3">
          <div class="col">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" id="nome" required />
          </div>
          <div class="col">
            <label for="mae" class="form-label">Mãe</label>
            <input type="text" class="form-control" name="mae" id="mae" />
          </div>
          <div class="col">
            <label for="pai" class="form-label">Pai</label>
            <input type="text" class="form-control" name="pai" id="pai" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="nascimento" class="form-label">Nascimento</label>
            <input type="date" class="form-control" name="nascimento" id="nascimento" />
          </div>
          <div class="col">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="tel" class="form-control" name="telefone" id="telefone" />
          </div>
          <div class="col">
            <label for="sexo" class="form-label">Sexo</label>
            <select class="form-select" name="sexo" id="sexo">
              <option value="Masculino">Masculino</option>
              <option value="Feminino">Feminino</option>
              <option value="Não informar" selected>Não informar</option>
            </select>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" />
          </div>
          <div class="col">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" class="form-control" name="cpf" id="cpf" />
          </div>
          <div class="col">
            <label for="cep" class="form-label">CEP</label>
            <input type="text" class="form-control" name="cep" id="cep" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="cidade" class="form-label">Cidade</label>
            <input type="text" class="form-control" name="cidade" id="cidade" />
          </div>
          <div class="col">
            <label for="endereco" class="form-label">Endereço</label>
            <input type="text" class="form-control" name="endereco" id="endereco" />
          </div>
          <div class="col">
            <label for="nctps" class="form-label">Nº CTPS</label>
            <input type="text" class="form-control" name="nctps" id="nctps" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="sctps" class="form-label">Série CTPS</label>
            <input type="text" class="form-control" name="sctps" id="sctps" />
          </div>
          <div class="col">
            <label for="nescolaridade" class="form-label">Nível Escolaridade</label>
            <select class="form-select" name="nescolaridade" id="nescolaridade">
              <option value="Fundamental">Fundamental</option>
              <option value="Médio">Médio Incompleto</option>
              <option value="Médio">Médio Completo</option>
            </select>
          </div>
          <div class="col">
            <label for="escola" class="form-label">Escola</label>
            <input type="text" class="form-control" name="escola" id="escola" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="reservista" class="form-label">Reservista?</label>
            <select class="form-select" name="reservista" id="reservista">
              <option value="Sim">Sim</option>
              <option value="Não" selected>Não</option>
            </select>
          </div>
          <!--<div class="col">
            <label for="escolaridade" class="form-label">Escolaridade</label>
            <input type="text" class="form-control" name="escolaridade" id="escolaridade" />
          </div>
        </div><!-->
        <div class="row mb-3">
          <div class="col">
            <label for="jornada" class="form-label">Jornada</label>
            <select class="form-select" name="jornada" id="jornada">
              <option value="Segunda a Sexta">Segunda a Sexta</option>
              <option value="Segunda a Sábado">Segunda a Sábado</option>
            </select>
          </div>
          <div class="col">
            <label for="hrtrabalho" class="form-label">Horas de Trabalho</label>
            <select class="form-select" name="hrtrabalho" id="hrtrabalho">
              <option value="4 horas">4 horas</option>
              <option value="6 horas">6 horas</option>
              <option value="6 horas">8 horas</option>
            </select>
          </div>
          <div class="col">
            <label for="salario" class="form-label">Salário</label>
            <input type="text" class="form-control" name="salario" id="salario" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="dtcontratacao" class="form-label">Data de Contratação</label>
            <input type="date" class="form-control" name="dtcontratacao" id="dtcontratacao" />
          </div>
          <div class="col">
            <label for="dfcontratacao" class="form-label">Data de Encerramento</label>
            <input type="date" class="form-control" name="dfcontratacao" id="dfcontratacao" />
          </div>
          <div class="col">
            <label for="duracaodocurso" class="form-label">Duração do Contrato</label>
            <input type="text" class="form-control" name="duracaodocurso" id="duracaodocurso" />
          </div>
          <div class="col">
            <label for="dtrabalho" class="form-label">Horário do Trabalho</label>
            <input type="text" class="form-control" name="dtrabalho" id="dtrabalho" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="dcurso" class="form-label">Dia do Curso</label>
            <select class="form-select" name="dcurso" id="dcurso">
              <option value="Segunda">Segunda</option>
              <option value="Terça">Terça</option>
              <option value="Quarta">Quarta</option>
              <option value="Quinta">Quinta</option>
              <option value="Sexta">Sexta</option>
            </select>
          </div>
          <div class="col">
            <label for="hrcurso" class="form-label">Horário do Curso</label>
            <input type="text" class="form-control" name="hrcurso" id="hrcurso" />
          </div>
          <div class="col">
            <label for="dtcursoinicial" class="form-label">Data Inicial do Curso</label>
            <input type="date" class="form-control" name="dtcursoinicial" id="dtcursoinicial" />
          </div>
          <div class="col">
            <label for="dtcursofinal" class="form-label">Data Final do Curso</label>
            <input type="date" class="form-control" name="dtcursofinal" id="dtcursofinal" />
          </div>
        </div>
      <div class="row mb-3">
          <div class="col">
            <label for="foto" class="form-label">Foto do Candidato</label>
            <input type="file" class="form-control" name="foto" id="foto" accept="image/*" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="cbo" class="form-label">CBO</label>
            <select class="form-select" name="cbo" id="cbo">
              <option value="">Selecione o CBO</option>
              <?php foreach ($cadcbos as $cboOption): ?>
                <option value="<?= htmlspecialchars($cboOption['id']) ?>"><?= htmlspecialchars($cboOption['cod'] . ' - ' . $cboOption['atividades']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="empresa" class="form-label">Empresa</label>
            <select class="form-select" name="empresa" id="empresa">
              <option value="">Selecione a Empresa</option>
              <?php foreach ($cadempresas as $empresaOption): ?>
                <option value="<?= htmlspecialchars($empresaOption['id']) ?>"><?= htmlspecialchars($empresaOption['rsocial']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
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
  const candidateModal = new bootstrap.Modal(document.getElementById('candidateModal'));


<h1>sdadsa</h1>


<div class="container mt-4">
    <h2>Cadastro de Candidatos</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#candidateModal" onclick="openModal()">Novo</button>
    <table class="table table-bordered">
        <thead>
            <tr class="text-center">
                <th>Nome</th>
                <th>CPF</th>
                <th>Cidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidates as $candidate): ?>
            <tr>
                <td><?= htmlspecialchars($candidate['nome']) ?></td>
                <td><?= htmlspecialchars($candidate['cpf']) ?></td>
                <td><?= htmlspecialchars($candidate['cidade']) ?></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning" onclick='editCandidate(<?= json_encode($candidate) ?>)'>Editar</button>
                    <button class="btn btn-sm btn-info" onclick='viewCandidate(<?= json_encode($candidate) ?>)'>Visualizar</button>
                    <a href="cadastroaprendizes.php?delete=<?= $candidate['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


</h1>

<!-- Modal -->
<div class="modal fade" id="candidateModal" tabindex="-1" aria-labelledby="candidateModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <form method="post" id="candidateForm" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title" id="candidateModalLabel">Cadastro de Candidato</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="candidateId" />
        <div class="row mb-3">
          <div class="col">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" id="nome" required />
          </div>
          <div class="col">
            <label for="mae" class="form-label">Mãe</label>
            <input type="text" class="form-control" name="mae" id="mae" />
          </div>
          <div class="col">
            <label for="pai" class="form-label">Pai</label>
            <input type="text" class="form-control" name="pai" id="pai" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="nascimento" class="form-label">Nascimento</label>
            <input type="date" class="form-control" name="nascimento" id="nascimento" />
          </div>
          <div class="col">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="tel" class="form-control" name="telefone" id="telefone" />
          </div>
          <div class="col">
            <label for="sexo" class="form-label">Sexo</label>
            <select class="form-select" name="sexo" id="sexo">
              <option value="Masculino">Masculino</option>
              <option value="Feminino">Feminino</option>
              <option value="Não informar" selected>Não informar</option>
            </select>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" />
          </div>
          <div class="col">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" class="form-control" name="cpf" id="cpf" />
          </div>
          <div class="col">
            <label for="cep" class="form-label">CEP</label>
            <input type="text" class="form-control" name="cep" id="cep" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="cidade" class="form-label">Cidade</label>
            <input type="text" class="form-control" name="cidade" id="cidade" />
          </div>
          <div class="col">
            <label for="endereco" class="form-label">Endereço</label>
            <input type="text" class="form-control" name="endereco" id="endereco" />
          </div>
          <div class="col">
            <label for="nctps" class="form-label">Nº CTPS</label>
            <input type="text" class="form-control" name="nctps" id="nctps" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="sctps" class="form-label">Série CTPS</label>
            <input type="text" class="form-control" name="sctps" id="sctps" />
          </div>
          <div class="col">
            <label for="nescolaridade" class="form-label">Nível Escolaridade</label>
            <select class="form-select" name="nescolaridade" id="nescolaridade">
              <option value="Fundamental">Fundamental</option>
              <option value="Médio">Médio</option>
            </select>
          </div>
          <div class="col">
            <label for="escola" class="form-label">Escola</label>
            <input type="text" class="form-control" name="escola" id="escola" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="reservista" class="form-label">Reservista?</label>
            <select class="form-select" name="reservista" id="reservista">
              <option value="Sim">Sim</option>
              <option value="Não" selected>Não</option>
            </select>
          </div>
          <div class="col">
            <label for="dfcontratacao" class="form-label">Data de Encerramento</label>
            <input type="text" class="form-control" name="dfcontratacao" id="dfcontratacao" />
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="foto" class="form-label">Foto do Candidato</label>
            <input type="file" class="form-control" name="foto" id="foto" accept="image/*" />
          </div>
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
  const candidateModal = new bootstrap.Modal(document.getElementById('candidateModal'));

  function openModal() {
    document.getElementById('candidateForm').reset();
    document.getElementById('candidateId').value = '';
    enableFormFields(true);
    document.querySelector('#candidateForm button[type="submit"]').style.display = 'inline-block';
    candidateModal.show();
  }

  function editCandidate(candidate) {
    document.getElementById('candidateId').value = candidate.id;
    document.getElementById('nome').value = candidate.nome || '';
    document.getElementById('mae').value = candidate.mae || '';
    document.getElementById('pai').value = candidate.pai || '';
    document.getElementById('nascimento').value = candidate.nascimento || '';
    document.getElementById('telefone').value = candidate.telefone || '';
    document.getElementById('sexo').value = candidate.sexo || 'Não informar';
    document.getElementById('email').value = candidate.email || '';
    document.getElementById('cpf').value = candidate.cpf || '';
    document.getElementById('cep').value = candidate.cep || '';
    document.getElementById('cidade').value = candidate.cidade || '';
    document.getElementById('endereco').value = candidate.endereco || '';
    document.getElementById('nctps').value = candidate.nctps || '';
    document.getElementById('sctps').value = candidate.sctps || '';
    document.getElementById('nescolaridade').value = candidate.nescolaridade || '';
    document.getElementById('escola').value = candidate.escola || '';
    document.getElementById('reservista').value = candidate.reservista || 'Não';
    document.getElementById('dfcontratacao').value = candidate.dfcontratacao || '';
    document.getElementById('jornada').value = candidate.jornada || '';
    document.getElementById('hrtrabalho').value = candidate.hrtrabalho || '';
    document.getElementById('salario').value = candidate.salario || '';
    document.getElementById('dtcontratacao').value = candidate.dtcontratacao || '';
    document.getElementById('duracaodocurso').value = candidate.duracaodocurso || '';
    document.getElementById('dtrabalho').value = candidate.dtrabalho || '';
    document.getElementById('dcurso').value = candidate.dcurso || '';
    document.getElementById('hrcurso').value = candidate.hrcurso || '';
    document.getElementById('dtcursoinicial').value = candidate.dtcursoinicial || '';
    document.getElementById('dtcursofinal').value = candidate.dtcursofinal || '';
    document.getElementById('cbo').value = candidate.cbo || '';
    document.getElementById('empresa').value = candidate.empresa || '';
    enableFormFields(true);
    document.querySelector('#candidateForm button[type="submit"]').style.display = 'inline-block';
    candidateModal.show();
  }

  function viewCandidate(candidate) {
    document.getElementById('candidateId').value = candidate.id;
    document.getElementById('nome').value = candidate.nome || '';
    document.getElementById('mae').value = candidate.mae || '';
    document.getElementById('pai').value = candidate.pai || '';
    document.getElementById('nascimento').value = candidate.nascimento || '';
    document.getElementById('telefone').value = candidate.telefone || '';
    document.getElementById('sexo').value = candidate.sexo || 'Não informar';
    document.getElementById('email').value = candidate.email || '';
    document.getElementById('cpf').value = candidate.cpf || '';
    document.getElementById('cep').value = candidate.cep || '';
    document.getElementById('cidade').value = candidate.cidade || '';
    document.getElementById('endereco').value = candidate.endereco || '';
    document.getElementById('nctps').value = candidate.nctps || '';
    document.getElementById('sctps').value = candidate.sctps || '';
    document.getElementById('nescolaridade').value = candidate.nescolaridade || '';
    document.getElementById('escola').value = candidate.escola || '';
    document.getElementById('reservista').value = candidate.reservista || 'Não';
    document.getElementById('dfcontratacao').value = candidate.dfcontratacao || '';
    document.getElementById('jornada').value = candidate.jornada || '';
    document.getElementById('hrtrabalho').value = candidate.hrtrabalho || '';
    document.getElementById('salario').value = candidate.salario || '';
    document.getElementById('dtcontratacao').value = candidate.dtcontratacao || '';
    document.getElementById('duracaodocurso').value = candidate.duracaodocurso || '';
    document.getElementById('dtrabalho').value = candidate.dtrabalho || '';
    document.getElementById('dcurso').value = candidate.dcurso || '';
    document.getElementById('hrcurso').value = candidate.hrcurso || '';
    document.getElementById('dtcursoinicial').value = candidate.dtcursoinicial || '';
    document.getElementById('dtcursofinal').value = candidate.dtcursofinal || '';
    document.getElementById('cbo').value = candidate.cbo || '';
    document.getElementById('empresa').value = candidate.empresa || '';

    // Show photo if available
    const photoImg = document.getElementById('candidatePhoto');
    if (candidate.foto) {
      photoImg.src = 'uploads/' + candidate.foto;
      photoImg.style.display = 'block';
    } else {
      photoImg.style.display = 'none';
    }

    enableFormFields(false);
    document.querySelector('#candidateForm button[type="submit"]').style.display = 'none';

    // Change modal title
    document.getElementById('candidateModalLabel').textContent = 'Visualizar Candidato';

    // Add a close button if not present
    if (!document.getElementById('closeViewBtn')) {
      const closeBtn = document.createElement('button');
      closeBtn.type = 'button';
      closeBtn.id = 'closeViewBtn';
      closeBtn.className = 'btn btn-secondary';
      closeBtn.setAttribute('data-bs-dismiss', 'modal');
      closeBtn.textContent = 'Fechar';
      const footer = document.querySelector('.modal-footer');
      footer.appendChild(closeBtn);
    }

    candidateModal.show();
  }

  function enableFormFields(enable) {
    const formElements = document.querySelectorAll('#candidateForm input, #candidateForm select');
    formElements.forEach(el => {
      el.disabled = !enable;
    });
  }
</script>

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

<script>
function viewContractByCandidate(candidateId) {
    fetch('get_saved_contract_by_candidate.php?candidate_id=' + candidateId)
    .then(response => {
        if (!response.ok) {
            throw new Error('Contrato não encontrado para o candidato.');
        }
        return response.text();
    })
    .then(data => {
        document.getElementById('contractContent').innerText = data;
        var contractModal = new bootstrap.Modal(document.getElementById('contractModal'));
        contractModal.show();
    })
    .catch(error => alert('Erro ao carregar contrato: ' + error.message));
}
</script>

<script>
document.getElementById('btnPesquisa').addEventListener('click', function() {
    const input = document.getElementById('inputPesquisa');
    if (input.style.display === 'none' || input.style.display === '') {
        input.style.display = 'block';
        input.focus();
    } else {
        input.style.display = 'none';
        input.value = '';
        filterTable('');
    }
});

document.getElementById('inputPesquisa').addEventListener('input', function() {
    filterTable(this.value);
});

function filterTable(searchTerm) {
    const table = document.querySelector('table.table-bordered tbody');
    const rows = table.getElementsByTagName('tr');
    const filter = searchTerm.toLowerCase();

    for (let i = 0; i < rows.length; i++) {
        const nomeCell = rows[i].getElementsByTagName('td')[1]; // Nome column index is 1
        if (nomeCell) {
            const nomeText = nomeCell.textContent || nomeCell.innerText;
            if (nomeText.toLowerCase().indexOf(filter) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
}
</script>

</body>
</html>
