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

// Get candidate ID from query parameter
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID do candidato não fornecido.";
    exit;
}

// Fetch candidate data
$stmt = $pdo->prepare("SELECT * FROM cadcandidato WHERE id = ?");
$stmt->execute([$id]);
$candidate = $stmt->fetch();

if (!$candidate) {
    echo "Candidato não encontrado.";
    exit;
}

// Fetch cadcbos data using candidate's cbo field
$stmtCbos = $pdo->prepare("SELECT id, cod, atividades FROM cadcbos WHERE id = ?");
$stmtCbos->execute([$candidate['cbo']]);
$cbosData = $stmtCbos->fetch();

if (!$cbosData) {
    // If no matching cadcbos found, set empty defaults
    $cbosData = ['id' => '', 'cod' => '', 'atividades' => ''];
}

// Fetch cadempresas data using candidate's empresa field
$stmtEmpresa = $pdo->prepare("SELECT id, rsocial, cnpj, rempresa, cep, cidade, endereco, raprendiz, telefoneemp  FROM cadempresas WHERE id = ?");
$stmtEmpresa->execute([$candidate['empresa']]);
$empresaData = $stmtEmpresa->fetch();

if (!$empresaData) {
    $empresaData = ['id' => '', 'rsocial', 'cnpj', 'rempresa,', 'cep', 'cidade', 'endereco', 'raprendiz' => ''];
}


$stmtTerms = $pdo->query("SELECT terms FROM contratos LIMIT 1");
$contractTermsRow = $stmtTerms->fetch();
$contractTerms = $contractTermsRow ? $contractTermsRow['terms'] : '';

//iddd verificar
$stmtidcontrato = $pdo->prepare("SELECT id FROM saved_contracts WHERE candidate_id = ? ORDER BY id DESC LIMIT 1");
$stmtidcontrato->execute([$id]);
$idcontrato = $stmtidcontrato->fetch();



function formatDate($dateStr) {
    if (!$dateStr) return '';
    $date = DateTime::createFromFormat('Y-m-d', $dateStr);
    if ($date) {
        return $date->format('d/m/Y');
    }
    return htmlspecialchars($dateStr);
}

function formatCNPJ($cnpj) {
    $cnpj = preg_replace('/\D/', '', $cnpj);
    if (strlen($cnpj) !== 14) {
        return htmlspecialchars($cnpj);
    }
    return substr($cnpj, 0, 2) . '.' .
           substr($cnpj, 2, 3) . '/' .
           substr($cnpj, 5, 4) . '-' .
           substr($cnpj, 9, 4);
}

$contabilizar = 0;
$jornada = $candidate['jornada'] ?? '';
$hrtrabalho = floatval($candidate['hrtrabalho'] ?? 0);

if ($jornada === 'Segunda a Sexta') {
    $contabilizar = 5 * $hrtrabalho;
} elseif ($jornada === 'Segunda a Sábado') {
    $contabilizar = 6 * $hrtrabalho;
}

    $placeholders = [


    //idcontrato verificar
    '{idcontrato}' => '<strong>' . htmlspecialchars($idcontrato ? $idcontrato['id'] : '') . '</strong>',


    //Aprendiz
    '{nome}' => '<strong>' . htmlspecialchars($candidate['nome']) . '</strong>',
    '{pai}' => '<strong>' . htmlspecialchars($candidate['pai']) . '</strong>',
    '{mae}' => '<strong>' . htmlspecialchars($candidate['mae']) . '</strong>',
    '{nascimento}' => '<strong>' . formatDate($candidate['nascimento']) . '</strong>',
    '{telefone}' => '<strong>' . htmlspecialchars($candidate['telefone']) . '</strong>',
    '{cpf}' => '<strong>' . htmlspecialchars($candidate['cpf']) . '</strong>',
    '{cep}' => '<strong>' . htmlspecialchars($candidate['cep']) . '</strong>',
    '{cidade}' => '<strong>' . htmlspecialchars($candidate['cidade']) . '</strong>',
    '{endereco}' => '<strong>' . htmlspecialchars($candidate['endereco']) . '</strong>',
    '{nctps}' => '<strong>' . htmlspecialchars($candidate['nctps']) . '</strong>',
    '{sctps}' => '<strong>' . htmlspecialchars($candidate['sctps']) . '</strong>',
    '{cbo}' => '<strong>' . htmlspecialchars($candidate['cbo']) . '</strong>',
    '{hrtrabalho}' => '<strong>' . htmlspecialchars($candidate['hrtrabalho']) . '</strong>',
    '{salario}' => '<strong>' . htmlspecialchars($candidate['salario']) . '</strong>',
    '{dfcontratacao}' => '<strong>' . formatDate($candidate['dfcontratacao']) . '</strong>',
    '{dtcontratacao}' => '<strong>' . formatDate($candidate['dtcontratacao']) . '</strong>',
    '{dcurso}' => '<strong>' . formatDate($candidate['dcurso']) . '</strong>',
    '{hrcurso}' => '<strong>' . formatDate($candidate['hrcurso']) . '</strong>',
    '{dtcursoinicial}' => '<strong>' . formatDate($candidate['dtcursoinicial']) . '</strong>',
    '{dtcursofinal}' => '<strong>' . formatDate($candidate['dtcursofinal']) . '</strong>',
     '{jornada}' => '<strong>' . htmlspecialchars($candidate['jornada']) . '</strong>',
     '{contabilizar}' => '<strong>' . htmlspecialchars($contabilizar) . '</strong>',


    //cbos
    '{cbos_id}' => '<strong>' . htmlspecialchars($cbosData['id']) . '</strong>',
    '{cbos_cod}' => '<strong>' . htmlspecialchars($cbosData['cod']) . '</strong>',
    '{cbos_atividades}' => nl2br('<strong>' . htmlspecialchars($cbosData['atividades']) . '</strong>'),

    //empresa
    '{empresa_id}' => '<strong>' . htmlspecialchars($empresaData['id']) . '</strong>',
    '{empresa_rsocial}' => '<strong>' . htmlspecialchars($empresaData['rsocial']) . '</strong>',
    '{empresa_cnpj}' => '<strong>' . htmlspecialchars($empresaData['cnpj']) . '</strong>',
    '{empresa_endereco}' => '<strong>' . htmlspecialchars($empresaData['endereco']) . '</strong>',
    '{empresa_cidade}' => '<strong>' . htmlspecialchars($empresaData['cidade']) . '</strong>',
    '{empresa_cep}' => '<strong>' . htmlspecialchars($empresaData['cep']) . '</strong>',
    '{empresa_rempresa}' => '<strong>' . htmlspecialchars($empresaData['rempresa']) . '</strong>',
    '{empresa_raprendiz}' => '<strong>' . htmlspecialchars($empresaData['raprendiz']) . '</strong>',
    '{empresa_telefoneemp}' => '<strong>' . htmlspecialchars($empresaData['telefoneemp']) . '</strong>',


];

$contractContent = strtr($contractTerms, $placeholders);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_contract'])) {
    // Check if a contract already exists for this candidate
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM saved_contracts WHERE candidate_id = ?");
    $stmtCheck->execute([$id]);
    $contractExists = $stmtCheck->fetchColumn() > 0;

    if ($contractExists) {
        //$savedMessage = "Erro: Já existe um contrato gerado para este CPF.";
    } else {
        if (isset($_POST['save_contract'])) {
            // Save contract content to database without generating PDF
            $stmtSave = $pdo->prepare("INSERT INTO saved_contracts (candidate_id, contract_content) VALUES (?, ?)");
            $stmtSave->execute([$id, $contractContent]);
            $_SESSION['savedMessage'] = "Contrato salvo com sucesso.";
            header("Location: generate_contract.php?id=" . urlencode($id));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Contrato - <?= htmlspecialchars($candidate['nome']) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 40px auto;
            max-width: 800px;
            color: #333;
            background-color: #fff;
        }
        header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        header img {
            max-height: 80px;
            margin-bottom: 10px;
        }
        header h1 {
            font-size: 24px;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        header p {
            margin: 0;
            font-size: 14px;
            font-style: italic;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
      
       
        .contract h2 {
            text-align: center;
            text-decoration: underline;
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        .contract-terms p {
            font-size: 13px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            margin-bottom: 10px;
            font-size: 18px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            color: #222;
        }
        .candidate-info p {
            margin: 2px 0;
            line-height: 1.2;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-block {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        button {
            background-color: #004080;
            color: white;
            border: none;
            padding: 10px 18px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #003060;
        }
        @media print {
            #contractForm {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    
<?php
    if (!empty($_SESSION['savedMessage'])) {
        $savedMessage = $_SESSION['savedMessage'];
        unset($_SESSION['savedMessage']);
        echo "<script>alert(" . json_encode($savedMessage) . ");</script>";
    }
?>
    <form method="post" id="contractForm" style="margin-top: 30px;">
        <div style="display: flex; justify-content: center; gap: 15px;">
            <button type="submit" name="save_contract">Salvar Contrato</button>
            <button type="button" onclick="window.print()">Imprimir Contrato</button>
            <button type="button" onclick="window.location.href='cadastroaprendizes.php'">Voltar</button>
        </div>
    </form>
   
            
            
            
        <h2 style="text-align:center"><img src="iaq.png" alt="IAQ Logo" style="max-height: 80px; margin-bottom: 2px;" /></h2>
        <h3 style="text-align:center"> <strong>CONTRATO DE APRENDIZAGEM</strong></h3>

        
      
         <div class="contract">
            <!-- cabeçalho com id e ano (ano está manual)</!-->
        <div class="section contract-header" style="text-align:center;">
            <p ><strong>Nº. Contrato:</strong> <?= htmlspecialchars($idcontrato ? $idcontrato['id'] : '') ?>/2025</p>
        </div>
        <div class="section contract-terms">
            
            <p><?= nl2br($contractContent) ?></p>
           
            <p style="text-align:center">Brasília, <?= formatDate($candidate['dtcontratacao']) ?></p>
        </div>
        <div class="signature-section">
            <div class="signature-block">
                ______________________________<br>
                Assinatura da Empresa
            </div>
            <div class="signature-block">
                ______________________________<br>
                Assinatura do Candidato
            </div>
        </div>
        <div class="signature-section" style="margin-top: 40px; display: flex; justify-content: space-between;">
            <div class="signature-block" style="width: 45%; text-align: center;">
                ______________________________<br>
                Adilson Mariz de Moraes - Presidente<br>
                IAQ - Instituto Aprender de Qualificação<br>
                CNPJ: 12.388.176.0001-41
            </div>
            <div class="signature-block" style="width: 45%; text-align: center;">
                ______________________________<br>
                Responsável pelo aprendiz
            </div>
        </div>
        <div class="signature-section" style="margin-top: 40px; display: flex; justify-content: space-between;">
            <div class="signature-block" style="width: 45%; text-align: left;">
                Nome: ______________________________<br>
                CPF: ______________________________<br>
                Assinatura: ________________________
            </div>
            <div class="signature-block" style="width: 45%; text-align: left;">
                Nome: ______________________________<br>
                CPF: ______________________________<br>
                Assinatura: ________________________
            </div>
        </div>
    </div>
</body>
</html>
