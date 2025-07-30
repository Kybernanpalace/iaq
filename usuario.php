<?php
    session_start();
    if(empty($_SESSION)){
        print "<script>location.href='index.php';</script>";
    }

    include 'config.php';

    // Query to get all users
    $sql = "SELECT * FROM usuarios";
    $result = $conn->query($sql);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Usuários</title>
    
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    </head>
   
    <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body {
  font-family: Arial, Helvetica, sans-serif;
}

.navbar {
  overflow: hidden;
  background-color: #808080;
}

.navbar a {
  float: left;
  font-size: 16px;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

.dropdown {
  float: left;
  overflow: hidden;
}

.dropdown .dropbtn {
  font-size: 16px;  
  border: none;
  outline: none;
  color: white;
  padding: 14px 16px;
  background-color: inherit;
  font-family: inherit;
  margin: 0;
}

.navbar a:hover, .dropdown:hover .dropbtn {
  background-color: red;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  float: none;
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  text-align: left;
}

.dropdown-content a:hover {
  background-color: #ddd;
}

.dropdown:hover .dropdown-content {
  display: block;
}
</style>
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

        <div style="flex-grow: 1; padding: 20px;">
            <h2>Usuários Cadastrados</h2>
            <a href="novo_usuario.php" class="btn btn-primary mb-3">Novo</a>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Usuário</th>
                        <th>Senha</th>
                        <th>tipo</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";  
                            echo "<td>" . htmlspecialchars($row['senha']) . "</td>";
                            echo "<td>". htmlspecialchars($row["tipo"]) . "</td>";
                            echo "<td>";
                            echo "<a href='editar_usuario.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-primary me-2'>Editar</a>";
                            echo "<a href='excluir_usuario.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Tem certeza que deseja excluir este usuário?');\">Excluir</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Nenhum usuário encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
