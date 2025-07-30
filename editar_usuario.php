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
</head>
<body style="background-color:white;">

<div class="container mt-5" style="max-width: 600px;">
    <h2>Editar Usuário</h2>
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
