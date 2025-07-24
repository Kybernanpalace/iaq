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
    $stmtSave = $pdo->prepare("INSERT INTO saved_contracts (candidate_id, contract_content) VALUES (?, ?)");
    $stmtSave->execute([$id, $contractContent]);
    $savedMessage = "Contrato salvo com sucesso.";
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
            padding: 15px 20px;
            line-height: 1.3;
            font-size: 13px;
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
            margin: 4px 0;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
    <header>
        <img src="https://via.placeholder.com/150x80?text=Company+Logo" alt="Company Logo" />
        <h1>Empresa XYZ Ltda.</h1>
        
    </header>
    <h2>Contrato de Aprendizagem</h2>
    <?php if (!empty($savedMessage)): ?>
        <div class="message"><?= $savedMessage ?></div>
    <?php endif; ?>
    <div class="contract">
        
        <div class="section contract-terms">
            <h3>Termos do Contrato</h3>
            <p><?= nl2br(htmlspecialchars($contractContent)) ?></p>
        </div>
        <div class="signature-section">
            <div class="signature-block">
                <p>______________________________</p>
                <p>Assinatura da Empresa</p>
            </div>
            <div class="signature-block">
                <p>______________________________</p>
                <p>Assinatura do Candidato</p>
            </div>
        </div>
        <div class="signature-section" style="margin-top: 30px;">
            <div class="signature-block">
                <p>______________________________</p>
                <p>Adilson Mariz de Moraes - Presidente                
                        IAQ - Instituto Aprender de Qualificação
                    CNPJ: 12.388.176.0001-41 </p>
            </div>
            <div class="signature-block">
                <p>______________________________</p>
                <p>Responsável pelo aprendiz</p>
            </div>
        </div>

        <div class="signature-section" style="margin-top: 30px;">
            <div class="signature-block">
                <p>Nome:______________________________</p>
                <p>CPF:______________________________</p>
                <p>Assinatura:________________________</p> 
                   
            </div>

            <div class="signature-block">
                <p>Nome:______________________________</p>
                <p>CPF:______________________________</p>
                <p>Assinatura:________________________</p> 
            </div>
    </div>
    <form method="post" style="margin-top: 30px; text-align: center;">
        <button type="submit" name="save_contract">Salvar Contrato</button>
        <button type="button" onclick="window.location.href='teste.php'" style="margin-left: 15px;">Voltar</button>
        <button type="button" id="printBtn" style="margin-left: 15px;">Imprimir</button>
    </form>
    <script>
        document.getElementById('printBtn').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.setFontSize(10);
            const contractElement = document.querySelector('.contract');
            let y = 10;
            const pageHeight = doc.internal.pageSize.height;
            const leftMargin = 10;
            const rightMargin = 190;
            const lineHeight = 5;
            const maxLineWidth = rightMargin - leftMargin;

            function addText(text) {
                const splitText = doc.splitTextToSize(text, maxLineWidth);
                for (let i = 0; i < splitText.length; i++) {
                    if (y + lineHeight > pageHeight - 10) {
                        doc.addPage();
                        y = 10;
                    }
                    doc.text(splitText[i], leftMargin, y);
                    y += lineHeight;
                }
            }

            // Add header
            addText("Empresa XYZ Ltda.");
            addText("CNPJ: 00.000.000/0001-00 | Rua Exemplo, 123 - Cidade - Estado | Telefone: (00) 0000-0000");
            addText("");
            addText("Contrato de Aprendizagem");
            addText("");

            // Add candidate info
            addText("Dados do Candidato:");
            addText("Nome: <?= htmlspecialchars($candidate['nome']) ?>");
            addText("CPF: <?= htmlspecialchars($candidate['cpf']) ?>");
            addText("Cidade: <?= htmlspecialchars($candidate['cidade']) ?>");
            addText("Email: <?= htmlspecialchars($candidate['email']) ?>");
            addText("Telefone: <?= htmlspecialchars($candidate['telefone']) ?>");
            addText("");

            // Add contract terms
            addText("Termos do Contrato:");
            addText(`<?= str_replace("\n", "\\n", addslashes($contractContent)) ?>`);
            addText("");

            // Add signature lines
            addText("______________________________");
            addText("Assinatura do Candidato");
            addText("");
            addText("______________________________");
            addText("Assinatura da Empresa");
            

            doc.save('contrato.pdf');
        });
    </script>
</body>
</html>
