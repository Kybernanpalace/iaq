<?php
    session_start();
    unset($_SESSION["usuario"]);
    unset($_SESSION["NOME"]);
    unset($_SESSION["TIPO"]);
    session_destroy();
    header("location: index.php");
    exit;