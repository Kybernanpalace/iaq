<?php
session_start();
if(empty($_SESSION)){
    header("Location: index.php");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $terms = isset($_POST['contractTerms']) ? $conn->real_escape_string($_POST['contractTerms']) : '';

    if (empty($terms)) {
        echo "Os termos do contrato não podem estar vazios.";
        exit();
    }

    $logoFilename = null;
    if (isset($_FILES['companyLogo']) && $_FILES['companyLogo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $tmpName = $_FILES['companyLogo']['tmp_name'];
        $originalName = basename($_FILES['companyLogo']['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extension, $allowedExtensions)) {
            $newFilename = uniqid('logo_', true) . '.' . $extension;
            $destination = $uploadDir . $newFilename;

            if (move_uploaded_file($tmpName, $destination)) {
                $logoFilename = $conn->real_escape_string($newFilename);
            } else {
                echo "Erro ao mover o arquivo enviado.";
                exit();
            }
        } else {
            echo "Tipo de arquivo não permitido. Apenas imagens JPG, JPEG, PNG e GIF são aceitas.";
            exit();
        }
    }

    if (isset($_POST['save_terms'])) {
        if ($logoFilename) {
            $sql = "INSERT INTO contratos (terms, logo) VALUES ('$terms', '$logoFilename')";
        } else {
            $sql = "INSERT INTO contratos (terms) VALUES ('$terms')";
        }
    } elseif (isset($_POST['edit_terms'])) {
        if ($logoFilename) {
            $sql = "UPDATE contratos SET terms = '$terms', logo = '$logoFilename' WHERE id = 10";
        } else {
            $sql = "UPDATE contratos SET terms = '$terms' WHERE id = 10";
        }
    } else {
        echo "Ação inválida.";
        exit();
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: Contrato.php?success=1");
        exit();
    } else {
        echo "Erro ao salvar os termos do contrato: " . $conn->error;
    }
} else {
    header("Location: Contrato.php");
    exit();
}
?>
