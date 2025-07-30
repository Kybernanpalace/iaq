<?php
    session_start();
    if(empty($_SESSION)){
        print "<script>location.href='index.php';</script>";
    }

    include 'config.php';

    // Query to get nome, telefone, empresa, foto from cadcandidato
    $sql = "SELECT nome, telefone, empresa, foto FROM cadcandidato";
    $result = $conn->query($sql);

    // Query to get all companies from cadempresas
    $sqlEmpresas = "SELECT id, nfantasia FROM cadempresas";
    $resultEmpresas = $conn->query($sqlEmpresas);

    $empresaMap = [];
    if ($resultEmpresas && $resultEmpresas->num_rows > 0) {
        while ($row = $resultEmpresas->fetch_assoc()) {
            $empresaMap[$row['id']] = $row['nfantasia'];
        }
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">


    </head>
   
    



       <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
<a href="usuario.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; font-size: 14px; letter-spacing: 0.05em;">Cad. Usuários Sistema</a>
<a href="ficha.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; font-size: 14px; letter-spacing: 0.05em;">Ficha de Observação</a>
<a href="logout.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; margin-top: 20px; font-size: 14px; letter-spacing: 0.05em;">Sair</a>
        </div>

        <div class="container mt-4" style="margin-left: 220px; width: calc(100% - 220px);">
            <h2 style="display: inline-block; margin-right: 10px;">Ficha de Observação</h2>
        
            <table class="table table-bordered">
                <thead>
                    <tr class="text-center">
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Empresa</th>
                        <th>Ficha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td class='text-center'>";
                            if (!empty($row['foto'])) {
                                echo "<img src='uploads/" . htmlspecialchars($row['foto']) . "' alt='Foto' style='width:50px; height:50px; object-fit:cover; border-radius:50%;' />";
                            } else {
                                echo "N/A";
                            }
                            echo "</td>";
                            echo "<td class='text-center'>". htmlspecialchars($row['nome']) . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['telefone']) . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($empresaMap[$row['empresa']] ?? 'N/A') . "</td>";
                            echo "<td class='text-center'><button type='button' class='btn btn-secondary btn-sm'>Observações</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>Nenhum jovem cadastrado encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
