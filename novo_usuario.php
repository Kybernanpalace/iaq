<?php
session_start();
if(empty($_SESSION)){
    print "<script>location.href='index.php';</script>";
}

include 'config.php';

$nome = $usuario = $senha = $tipo = "";
$nome_err = $usuario_err = $senha_err = $tipo_err = "";
$success_msg = "";

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
        // Check if usuario already exists
        $sql = "SELECT id FROM usuarios WHERE usuario = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_usuario);
            $param_usuario = trim($_POST["usuario"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $usuario_err = "Este usuário já está em uso.";
                } else {
                    $usuario = trim($_POST["usuario"]);
                }
            } else {
                echo "Erro ao verificar usuário.";
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

    // If no errors, insert into database
    if (empty($nome_err) && empty($usuario_err) && empty($senha_err) && empty($tipo_err)) {
        $sql = "INSERT INTO usuarios (nome, usuario, senha, tipo) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $param_nome, $param_usuario, $param_senha, $param_tipo);

            $param_nome = $nome;
            $param_usuario = $usuario;
            $param_senha = $senha; // Save password as plain text (no hashing)
            $param_tipo = $tipo;

            if ($stmt->execute()) {
                $success_msg = "Usuário cadastrado com sucesso.";
                // Clear form values
                $nome = $usuario = $senha = $tipo = "";
            } else {
                echo "Erro ao cadastrar usuário.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>
<body style="background-color:white;">

<div class="container mt-5" style="max-width: 600px;">
    <h2>Cadastrar Novo Usuário</h2>
    <?php 
    if (!empty($success_msg)) {
        echo '<div class="alert alert-success">' . $success_msg . '</div>';
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($nome); ?>">
            <div class="invalid-feedback"><?php echo $nome_err; ?></div>
        </div>
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" name="usuario" id="usuario" class="form-control <?php echo (!empty($usuario_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($usuario); ?>">
            <div class="invalid-feedback"><?php echo $usuario_err; ?></div>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="text" name="senha" id="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>" value="">
            <div class="invalid-feedback"><?php echo $senha_err; ?></div>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-select <?php echo (!empty($tipo_err)) ? 'is-invalid' : ''; ?>">
                <option value="">Selecione o tipo</option>
                <option value="Administrativo" <?php echo ($tipo == 'Administrativo') ? 'selected' : ''; ?>>FULL</option>
                <option value="Administrativo" <?php echo ($tipo == 'Administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                <option value="Professores" <?php echo ($tipo == 'Professores') ? 'selected' : ''; ?>>Professores</option>
            </select>
            <div class="invalid-feedback"><?php echo $tipo_err; ?></div>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="usuario.php" class="btn btn-secondary ms-2">Voltar</a>
    </form>
</div>

</body>
</html>
