<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sislogin";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM cadempresas WHERE id = $delete_id");
    header("Location: empresas.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'insert') {
        $cnpj = $conn->real_escape_string($_POST['cnpj']);
        $rsocial = $conn->real_escape_string($_POST['rsocial']);
        $nfantasia = $conn->real_escape_string($_POST['nfantasia']);
        $email = $conn->real_escape_string($_POST['email']);
        $rempresa = $conn->real_escape_string($_POST['rempresa']);
        $raprendiz = $conn->real_escape_string($_POST['raprendiz']);
        $telefoneemp = $conn->real_escape_string($_POST['telefoneemp']);
        $cep = $conn->real_escape_string($_POST['cep']);
        $cidade = $conn->real_escape_string($_POST['cidade']);
        $endereco = $conn->real_escape_string($_POST['endereco']);
        $codatividade = $conn->real_escape_string($_POST['codatividade']);

        $sql = "INSERT INTO cadempresas (cnpj, rsocial, nfantasia, email, rempresa, raprendiz, telefoneemp, cep, cidade, endereco, codatividade)
                VALUES ('$cnpj', '$rsocial', '$nfantasia', '$email', '$rempresa', '$raprendiz', '$telefoneemp', '$cep', '$cidade', '$endereco', '$codatividade')";
        $conn->query($sql);
        header("Location: empresas.php");
        exit;
    } elseif ($_POST['action'] === 'update' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $cnpj = $conn->real_escape_string($_POST['cnpj']);
        $rsocial = $conn->real_escape_string($_POST['rsocial']);
        $nfantasia = $conn->real_escape_string($_POST['nfantasia']);
        $email = $conn->real_escape_string($_POST['email']);
        $rempresa = $conn->real_escape_string($_POST['rempresa']);
        $raprendiz = $conn->real_escape_string($_POST['raprendiz']);
        $telefoneemp = $conn->real_escape_string($_POST['telefoneemp']);
        $cep = $conn->real_escape_string($_POST['cep']);
        $cidade = $conn->real_escape_string($_POST['cidade']);
        $endereco = $conn->real_escape_string($_POST['endereco']);
        $codatividade = $conn->real_escape_string($_POST['codatividade']);

        $sql = "UPDATE cadempresas SET 
                cnpj='$cnpj', rsocial='$rsocial', nfantasia='$nfantasia', email='$email', rempresa='$rempresa', 
                raprendiz='$raprendiz', telefoneemp='$telefoneemp', cep='$cep', cidade='$cidade', endereco='$endereco', codatividade='$codatividade' 
                WHERE id=$id";
        $conn->query($sql);
        header("Location: empresas.php");
        exit;
    }
}

// Fetch records
$result = $conn->query("SELECT * FROM cadempresas");

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Cadastro de Empresas</title>
    <style>
        /* Sidebar styles */
        #sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
            height: 100%;
            background-color: #2c3e50;
            padding-top: 20px;
            box-sizing: border-box;
            color: white;
            font-family: Arial, sans-serif;
        }
        #sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        #sidebar ul li {
            padding: 15px 20px;
            cursor: pointer;
        }
        #sidebar ul li:hover {
            background-color: #34495e;
        }
        #sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        /* Adjust main content to accommodate sidebar */
        body {
            padding-left: 220px;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        #main-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #ddd;
        }
        button {
            margin-right: 5px;
        }
        /* Custom button styles */
        .novo-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .novo-btn:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .view-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .view-btn:hover {
            background-color: #2980b9;
        }
        .edit-btn {
            background-color: #f39c12;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .edit-btn:hover {
            background-color: #d68910;
        }
        #popupForm {
            display: none;
            position: fixed;
            top: 40%;
            left: 50%;
            width: 420px;
            max-height: 80vh;
            margin-left: -210px;
            margin-top: -250px;
            background-color: white;
            border: 1px solid #333;
            padding: 10px;
            z-index: 1000;
            box-shadow: 0 0 10px #000;
        }
        #scrollableFormContainer {
            max-height: 70vh;
            overflow-y: scroll;
            padding-right: 10px;
        }
        /* Webkit scrollbar styles */
        #scrollableFormContainer::-webkit-scrollbar {
            width: 8px;
        }
        #scrollableFormContainer::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        #scrollableFormContainer::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
            border: 2px solid #f1f1f1;
        }
        #popupOverlay {
            display: none;
            position: fixed;
            top:0;
            left:0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .form-group {
            margin-bottom: 10px;
        }
        label {
            display: block;
            font-weight: bold;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
    </style>
    <script>
        function openForm() {
            document.getElementById('popupForm').style.display = 'block';
            document.getElementById('popupOverlay').style.display = 'block';
            // Reset form for new record
            if(document.getElementById('action').value !== 'update' && document.getElementById('action').value !== '') {
                document.getElementById('formTitle').textContent = 'Novo Cadastro';
                document.getElementById('recordForm').reset();
                document.getElementById('id').value = '';
                document.getElementById('action').value = 'insert';
                // Make fields editable
                var inputs = document.querySelectorAll('#recordForm input');
                inputs.forEach(function(input) {
                    input.readOnly = false;
                });
                // Show submit button
                document.querySelector('#recordForm button[type="submit"]').style.display = 'inline-block';
            }
        }
        function closeForm() {
            document.getElementById('popupForm').style.display = 'none';
            document.getElementById('popupOverlay').style.display = 'none';
        }
        function viewRecord(id) {
            alert('Visualizar registro ID: ' + id);
            // Implement view logic or redirect to a view page if needed
        }
        function editRecord(record) {
            // record is a JSON object with the row data
            openForm();
            document.getElementById('formTitle').textContent = 'Alterar Cadastro';
            document.getElementById('action').value = 'update';
            document.getElementById('id').value = record.id;
            document.getElementById('cnpj').value = record.cnpj;
            document.getElementById('rsocial').value = record.rsocial;
            document.getElementById('nfantasia').value = record.nfantasia;
            document.getElementById('email').value = record.email;
            document.getElementById('rempresa').value = record.rempresa;
            document.getElementById('raprendiz').value = record.raprendiz;
            document.getElementById('telefoneemp').value = record.telefoneemp;
            document.getElementById('cep').value = record.cep;
            document.getElementById('cidade').value = record.cidade;
            document.getElementById('endereco').value = record.endereco;
            document.getElementById('codatividade').value = record.codatividade;
            

            // Make fields editable
            var inputs = document.querySelectorAll('#recordForm input');
            inputs.forEach(function(input) {
                input.readOnly = false;
            });
            // Show submit button
            document.querySelector('#recordForm button[type="submit"]').style.display = 'inline-block';
        }
    </script>
</head>
<body>
   
    <div style="display: flex; min-height: 100vh;">
        <div id="sidebar" style="width: 220px; background-color: #333; color: white; padding-top: 20px; flex-shrink: 0;">
            <a href="dashboard.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Início</a>
            <a href="cadastroaprendizes.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Cadastro</a>
            <a href="cbos.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">CBO</a>
            <a href="empresas.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Empresas</a>
            <a href="usuarios.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Usuários</a>
            <a href="Contrato.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Contrato Modelo</a>
            <a href="ficha.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Ficha</a>
            <a href="usuarios.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none;">Usuários</a>
            <a href="logout.php" style="display: block; padding: 12px 20px; color: white; text-decoration: none; margin-top: 20px;">Sair</a>
        </div>
    <div id="main-container">
    <h1>Cadastro de Empresas</h1>
    <button class="novo-btn" onclick="openForm()">Novo</button>

    <table>
            <thead>
                <tr>
                    <th>CNPJ</th>
                    <th>Razão Social</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['cnpj']); ?></td>
                            <td><?php echo htmlspecialchars($row['rsocial']); ?></td>
                            <td>
                                <button class="delete-btn" onclick="if(confirm('Confirma exclusão?')) location.href='empresas.php?delete_id=<?php echo $row['id']; ?>'">Excluir</button>
                                <button class="view-btn" onclick="viewRecord(<?php echo $row['id']; ?>)">Visualizar</button>
                                <button class="edit-btn" onclick="editRecord(<?php echo htmlspecialchars(json_encode($row)); ?>)">Alterar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">Nenhum registro encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div id="popupOverlay" onclick="closeForm()"></div>
        <div id="popupForm">
            <h2 id="formTitle">Novo Cadastro</h2>
            <div id="scrollableFormContainer">
                <form method="post" action="empresas.php" id="recordForm">
                    <input type="hidden" name="action" value="insert" id="action" />
                    <input type="hidden" name="id" id="id" value="" />
                    <div class="form-group">
                        <label for="cnpj">CNPJ</label>
                        <input type="text" id="cnpj" name="cnpj" required />
                    </div>
                    <div class="form-group">
                        <label for="rsocial">Razão Social</label>
                        <input type="text" id="rsocial" name="rsocial" required />
                    </div>
                    <div class="form-group">
                        <label for="nfantasia">Nome Fantasia</label>
                        <input type="text" id="nfantasia" name="nfantasia" />
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" />
                    </div>
                    <div class="form-group">
                        <label for="rempresa">Responsável Empresa</label>
                        <input type="text" id="rempresa" name="rempresa" />
                    </div>
                    <div class="form-group">
                        <label for="raprendiz">Responsável Aprendiz</label>
                        <input type="text" id="raprendiz" name="raprendiz" />
                    </div>
                    <div class="form-group">
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" name="cep" />
                    </div>
                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade" />
                    </div>
                    <div class="form-group">
                        <label for="endereco">Endereço</label>
                        <input type="text" id="endereco" name="endereco" />
                    </div>
                      <div class="form-group">
                        <label for="telefoneemp">Telefone</label>
                        <input type="text" id="telefoneemp" name="telefoneemp" />
                    </div>
                    <div class="form-group">
                        <label for="codatividade">Código Atividade</label>
                        <input type="text" id="codatividade" name="codatividade" />
                    </div>
                    <button type="submit">Salvar</button>
                    <button type="button" onclick="closeForm()">Cancelar</button>
                </form>
            </div>
            <script>
                // Reset form when opening for new record
                function openForm() {
                    document.getElementById('popupForm').style.display = 'block';
                    document.getElementById('popupOverlay').style.display = 'block';
                    if(document.getElementById('action').value !== 'update' && document.getElementById('action').value !== '') {
                        document.getElementById('formTitle').textContent = 'Novo Cadastro';
                        document.getElementById('recordForm').reset();
                        document.getElementById('id').value = '';
                        document.getElementById('action').value = 'insert';
                        // Make fields editable
                        var inputs = document.querySelectorAll('#recordForm input');
                        inputs.forEach(function(input) {
                            input.readOnly = false;
                        });
                        // Show submit button
                        document.querySelector('#recordForm button[type="submit"]').style.display = 'inline-block';
                    }
                }
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>

Formulário empresa