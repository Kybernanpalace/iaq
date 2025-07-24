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

// Fetch contract terms - assuming contratos table with terms field
$stmtTerms = $pdo->query("SELECT terms FROM contratos LIMIT 1");
$contractTermsRow = $stmtTerms->fetch();
$contractTerms = $contractTermsRow ? $contractTermsRow['terms'] : '';

// Generate contract content by combining candidate data and contract terms
// For simplicity, we will replace placeholders in contract terms with candidate data if any placeholders exist
// Example placeholders: {nome}, {cpf}, {cidade}, etc.

function formatDate($dateStr) {
    if (!$dateStr) return '';
    $date = DateTime::createFromFormat('Y-m-d', $dateStr);
    if ($date) {
        return $date->format('d/m/Y');
    }
    return htmlspecialchars($dateStr);
}
$placeholders = [
    '{nome}' => htmlspecialchars($candidate['nome']),
    '{pai}' => htmlspecialchars($candidate['pai']),
    '{mae}' => htmlspecialchars($candidate['mae']),
    '{nascimento}' => formatDate($candidate['nascimento']),
    '{telefone}' => htmlspecialchars($candidate['telefone']),
    '{cpf}' => htmlspecialchars($candidate['cpf']),
    '{cep}' => htmlspecialchars($candidate['cep']),
    '{cidade}' => htmlspecialchars($candidate['cidade']),
    '{endereco}' => htmlspecialchars($candidate['endereco']),
    '{nctps}' => htmlspecialchars($candidate['nctps']),
    '{sctps}' => htmlspecialchars($candidate['sctps']),
    '{cbo}' => htmlspecialchars($candidate['cbo']),
    '{hrtrabalho}' => htmlspecialchars($candidate['hrtrabalho']),
    '{salario}' => htmlspecialchars($candidate['salario']),
    '{dtcontratacao}' => formatDate($candidate['dtcontratacao']),
    // Add more placeholders as needed
];

$contractContent = strtr($contractTerms, $placeholders);

// Handle saving contract if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_contract'])) {
    // Save contract content to database without generating PDF
    $stmtSave = $pdo->prepare("INSERT INTO saved_contracts (candidate_id, contract_content) VALUES (?, ?)");
    $stmtSave->execute([$id, $contractContent]);
    $savedMessage = "Contrato salvo com sucesso.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_pdf'])) {
    require_once __DIR__ . '/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();

    // Build HTML content for PDF
   
    
    $html .= '<p>' . nl2br(htmlspecialchars($contractContent)) . '</p>';
    $html .= '<p style="text-align:center; margin-top: 40px;">Brasília, ' . formatDate($candidate['dtcontratacao']) . '</p>';

    // Signature section styled as per model
    $html .= '<table style="width:100%; margin-top:60px; font-family: Times New Roman, serif; font-size: 12pt;">';
    $html .= '<tr>';
    $html .= '<td style="width:50%; text-align:center;">______________________________<br>Assinatura da Empresa</td>';
    $html .= '<td style="width:50%; text-align:center;">______________________________<br>Assinatura do Candidato</td>';
    $html .= '</tr><tr><td style="height:40px;"></td><td></td></tr>';
    $html .= '<tr>';
    $html .= '<td style="width:50%; text-align:center;">______________________________<br>Adilson Mariz de Moraes - Presidente<br>IAQ - Instituto Aprender de Qualificação<br>CNPJ: 12.388.176.0001-41</td>';
    $html .= '<td style="width:50%; text-align:center;">______________________________<br>Responsável pelo aprendiz</td>';
    $html .= '</tr><tr><td style="height:40px;"></td><td></td></tr>';
    $html .= '<tr>';
    $html .= '<td style="width:50%; text-align:left;">Nome: ______________________________<br>CPF: ______________________________<br>Assinatura: ________________________</td>';
    $html .= '<td style="width:50%; text-align:left;">Nome: ______________________________<br>CPF: ______________________________<br>Assinatura: ________________________</td>';
    $html .= '</tr></table>';

    $mpdf->WriteHTML($html);
    $pdfContent = $mpdf->Output('', 'S');

    // Save PDF content to database or file as needed
    $stmtSave = $pdo->prepare("INSERT INTO saved_contracts (candidate_id, contract_content) VALUES (?, ?)");
    $stmtSave->execute([$id, $pdfContent]);
    $savedMessage = "Contrato salvo com sucesso.";

    // Output PDF to browser for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="contrato.pdf"');
    echo $pdfContent;
    exit;
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
        .contract {
            border: 1px solid #000;
            padding: 30px 40px;
            line-height: 1.6;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        .contract h2 {
            text-align: center;
            text-decoration: underline;
            margin-bottom: 20px;
            font-size: 20px;
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
    </style>
</head>
<body>
    <header>
        <img src="https://via.placeholder.com/150x80?text=Company+Logo" alt="Company Logo" />
        
        
    </header>
    
    <?php if (!empty($savedMessage)): ?>
        <div class="message"><?= $savedMessage ?></div>
    <?php endif; ?>
    <div class="contract">
        <div class="section contract-header" style="margin-bottom: 20px;">
                </div>
        <div class="section contract-terms">
            <h3>CONTRATO DE APRENDIZAGEM</h3>
            <p><?= nl2br(htmlspecialchars($contractContent)) ?></p>
            <p style="margin-top: 40px;">Brasília, <?= formatDate($candidate['dtcontratacao']) ?></p>
        </div>
        <div class="signature-section">
            
          
    </div>
    <form method="post" style="margin-top: 30px; text-align: center;">
        <button type="submit" name="save_contract">Salvar Contrato</button>
        <button type="submit" name="generate_pdf" style="margin-left: 15px;">Imprimir Contrato</button>
        <button type="button" onclick="window.location.href='cadastroaprendizes.php'" style="margin-left: 15px;">Voltar</button>
    </form>
</body>
</html>
