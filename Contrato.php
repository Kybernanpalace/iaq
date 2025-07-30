<?php
session_start();
if(empty($_SESSION)){
    print "<script>location.href='index.php';</script>";
}

$contractTerms = '';

try {
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
    $pdo = new PDO($dsn, $user, $pass, $options);

    $stmtTerms = $pdo->query("SELECT terms FROM contratos ORDER BY id DESC LIMIT 1");
    $contractTermsRow = $stmtTerms->fetch();
    $contractTerms = $contractTermsRow ? $contractTermsRow['terms'] : '';
} catch (Exception $e) {
    $contractTerms = '';
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Termos do Contrato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous" />
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

        <div class="container" style="max-width: 800px; padding: 20px;">
            <h3 class="mb-4">Editar Termos do Contrato</h3>
<form method="post" action="save_contract_terms.php" enctype="multipart/form-data">
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="alert alert-success" role="alert">
                        Termos do contrato salvos com sucesso.
                    </div>
                <?php endif; ?>
<div class="mb-3">
    <label for="contractTerms" class="form-label">Termos do Contrato</label>
    <textarea id="contractTerms" name="contractTerms" class="form-control" rows="10" placeholder="Digite os termos do contrato aqui. Use placeholders para dados do candidato, ex: {nome}, {cargo}, {data_inicio}"><?= htmlspecialchars($contractTerms) ?></textarea>
</div>
<!--<button type="submit" name="save_terms" class="btn btn-primary">Salvar</button></!-->
<button type="submit" name="edit_terms" class="btn btn-secondary ms-2">Editar</button>
<!--<div class="mb-3">
    <label for="companyLogo" class="form-label">Logo da Empresa</label>
    <input type="file" id="companyLogo" name="companyLogo" class="form-control" accept="image/*"<!-->
</div>

            </form>
        </div>
    </div>

</body>
</html>
