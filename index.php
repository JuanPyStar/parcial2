<?php
session_start();

if (isset($_GET['api']) && $_GET['api'] === '1') {
    require_once __DIR__ . '/controladores/ClienteControlador.php';
    (new ClienteControlador())->manejar();
    exit;
}

include 'vistas/logic.php';
include 'vistas/header.php';
include 'vistas/login.php';
include 'vistas/app.php';
include 'vistas/footer.php';
?>
