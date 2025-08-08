<?php
    session_start();
    if(empty($_SESSION)){
        print "<script>location.href='index.php';</script>";
        exit;
    }

    include 'config.php';

    // Query to get all users
    $sql = "SELECT * FROM usuarios";
    $result = $conn->query($sql);

    // Get total users count
    $total_users = $result->num_rows;
    
    // Get admin users count
    $admin_sql = "SELECT COUNT(*) as admin_count FROM usuarios WHERE tipo = 'admin'";
    $admin_result = $conn->query($admin_sql);
    $admin_count = $admin_result->fetch_assoc()['admin_count'];
    
    // Get regular users count
    $regular_sql = "SELECT COUNT(*) as regular_count FROM usuarios WHERE tipo = 'user'";
    $regular_result = $conn->query($regular_sql);
    $regular_count = $regular_result->fetch_assoc()['regular_count'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IAQ - Gestão de Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            text-align: center;
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
            border-radius: 25<ask_followup_question>

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
                <a href="cadastroaprendizes.php" class="nav-link">
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
                <a href="usuario.php" class="nav-link active">
                    <i class="fas fa-users"></i> Usuários
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
                    <h1><i class="fas fa-users"></i> Gestão de Usuários</h1>
                    <p>Sistema de Cadastro e Gerenciamento de Usuários do Sistema</p>
                </div>

                <!-- Stats Cards -->
                <div class="container-fluid p-4">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-number"><?= $total_users ?></div>
                                <div class="stats-label">Total de Usuários</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-number"><?= $admin_count ?></div>
                                <div class="stats-label">Administradores</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-number"><?= $regular_count ?></div>
                                <div class="stats-label">Usuários Comuns</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0"><i class="fas fa-table"></i> Lista de Usuários</h3>
                        <a href="novo_usuario.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Novo Usuário
                        </a>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive fade-in">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-user"></i> Nome</th>
                                    <th><i class="fas fa-user-tag"></i> Usuário</th>
                                    <th><i class="fas fa-key"></i> Senha</th>
                                    <th><i class="fas fa-user-cog"></i> Tipo</th>
                                    <th><i class="fas fa-cogs"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td><strong>" . htmlspecialchars($row['id']) . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
                                        echo "<td>••••••••</td>";
                                        echo "<td><span class='badge bg-" . ($row['tipo'] == 'admin' ? 'danger' : 'primary') . "'>" . htmlspecialchars($row['tipo']) . "</span></td>";
                                        echo "<td>";
                                        echo "<div class='action-buttons'>";
                                        echo "<a href='editar_usuario.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-warning me-2'>";
                                        echo "<i class='fas fa-edit'></i> Editar";
                                        echo "</a>";
                                        echo "<a href='excluir_usuario.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Confirma exclusão?');\">";
                                        echo "<i class='fas fa-trash'></i> Excluir";
                                        echo "</a>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Nenhum usuário encontrado.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
    </script>
</body>
</html>
