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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>IAQ - Editar Termos do Contrato</title>
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

        .contract-form-container {
            padding: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .contract-textarea {
            min-height: 400px;
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
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
        <div class="sidebar" style="width: 250px;">
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
                <a href="empresas.php" class="nav-link">
                    <i class="fas fa-building"></i> Empresas
                </a>
                <a href="Contrato.php" class="nav-link active">
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
                    <h1><i class="fas fa-file-contract"></i> Editar Termos do Contrato</h1>
                    <p>Sistema de Gerenciamento de Termos de Contrato de Aprendizagem</p>
                </div>

                <!-- Contract Form -->
                <div class="contract-form-container fade-in">
                    <form method="post" action="save_contract_terms.php" enctype="multipart/form-data">
                        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> Termos do contrato salvos com sucesso!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <label for="contractTerms" class="form-label fw-bold">
                                <i class="fas fa-file-alt"></i> Termos do Contrato
                            </label>
                            <textarea id="contractTerms" name="contractTerms" class="form-control contract-textarea" 
                                      rows="15" placeholder="Digite os termos do contrato aqui. Use placeholders para dados do candidato, ex: {nome}, {cargo}, {data_inicio}"><?= htmlspecialchars($contractTerms) ?></textarea>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" name="save_terms" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Termos
                            </button>
                            <button type="submit" name="edit_terms" class="btn btn-secondary">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        // Adicionar feedback visual ao salvar
        document.querySelector('form').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"][name="save_terms"]');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
                btn.disabled = true;
            }
        });

        // Adicionar classe fade-in ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('fade-in');
        });
    </script>
</body>
</html>
