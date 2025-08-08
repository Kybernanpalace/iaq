<?php
session_start();
if(empty($_SESSION)){
    print "<script>location.href='index.php';</script>";
}

include 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: teste.php");
    exit();
}

$nome = $usuario = $senha = $tipo = "";
$nome_err = $usuario_err = $senha_err = $tipo_err = "";
$success_msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (empty(trim($_POST["nome"]))) {
        $nome_err = "Por favor, insira o nome.";
    } else {
        $nome = trim($_POST["nome"]);
    }

    if (empty(trim($_POST["usuario"]))) {
        $usuario_err = "Por favor, insira o usuário.";
    } else {
        // Check if usuario already exists for other users
        $sql = "SELECT id FROM usuarios WHERE usuario = ? AND id != ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $param_usuario, $param_id);
            $param_usuario = trim($_POST["usuario"]);
            $param_id = $id;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $usuario_err = "Este usuário já está em uso.";
                } else {
                    $usuario = trim($_POST["usuario"]);
                }
            } else {
                $error = "Erro ao verificar usuário.";
            }
            $stmt->close();
        }
    }

    if (empty(trim($_POST["senha"]))) {
        $senha_err = "Por favor, insira a senha.";
    } else {
        $senha = trim($_POST["senha"]);
    }

    if (empty(trim($_POST["tipo"]))) {
        $tipo_err = "Por favor, insira o tipo.";
    } else {
        $tipo = trim($_POST["tipo"]);
    }

    // If no errors, update database
    if (empty($nome_err) && empty($usuario_err) && empty($senha_err) && empty($tipo_err)) {
        $sql = "UPDATE usuarios SET nome=?, usuario=?, senha=?, tipo=? WHERE id=?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssi", $param_nome, $param_usuario, $param_senha, $param_tipo, $param_id);

            $param_nome = $nome;
            $param_usuario = $usuario;
            $param_senha = $senha;
            $param_tipo = $tipo;
            $param_id = $id;

            if ($stmt->execute()) {
                $success_msg = "Usuário atualizado com sucesso.";
            } else {
                $error = "Erro ao atualizar usuário.";
            }
            $stmt->close();
        }
    }
} else {
    // Fetch user data
    $sql = "SELECT nome, usuario, senha, tipo FROM usuarios WHERE id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $id;
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            header("Location: teste.php");
            exit();
        }
        $user = $result->fetch_assoc();
        $nome = $user['nome'];
        $usuario = $user['usuario'];
        $senha = $user['senha'];
        $tipo = $user['tipo'];
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
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
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
           s display: flex;
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

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats-label {
            color: var(--text-dark);
            font-weight: 500;
            margin-top: 10px;
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

        .logo-section {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .logo-section img {
            max-width: 120px;
            height: auto;
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

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilos adicionais para o formulário */
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            padding: 40px;
            margin: 20px;
        }

        .form-container h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid var(--border-color);
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        .form-select {
            border-radius: 10px;
            border: 1px solid var(--border-color);
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        .btn-secondary {
            background: var(--secondary-color);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .btn-secondary:hover {
            background: #00a352;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: var(--shadow);
        }

        .alert-success {
            background: linear-gradient(135deg, var(--secondary-color), #00a352);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5" style="max-width: 600px;">
        <div class="form-container fade-in">
            <h2><i class="fas fa-edit"></i> Editar Usuário</h2>
    <?php 
    if (!empty($success_msg)) {
        echo '<div class="alert alert-success">' . $success_msg . '</div>';
    }
    if (!empty($error)) {
        echo '<div class="alert alert-danger">' . $error . '</div>';
    }
    ?>
    <form action="editar_usuario.php?id=<?= htmlspecialchars($id) ?>" method="post" novalidate>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($nome) ?>">
            <div class="invalid-feedback"><?= $nome_err ?></div>
        </div>
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" name="usuario" id="usuario" class="form-control <?php echo (!empty($usuario_err)) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($usuario) ?>">
            <div class="invalid-feedback"><?= $usuario_err ?></div>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="text" name="senha" id="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($senha) ?>">
            <div class="invalid-feedback"><?= $senha_err ?></div>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-select <?php echo (!empty($tipo_err)) ? 'is-invalid' : ''; ?>">
                <option value="">Selecione o tipo</option>
                <option value="FULL" <?php echo ($tipo == 'FULL') ? 'selected' : ''; ?>>FULL</option>
                <option value="Administrativo" <?php echo ($tipo == 'Administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                <option value="Professores" <?php echo ($tipo == 'Professores') ? 'selected' : ''; ?>>Professores</option>
            </select>
            <div class="invalid-feedback"><?= $tipo_err ?></div>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="usuario.php" class="btn btn-secondary ms-2">Voltar</a>
    </form>
</div>

</body>
</html>
