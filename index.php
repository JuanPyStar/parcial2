<?php declare(strict_types=1);
session_start();

// Front Controller routing
$controllerName = $_GET['controller'] ?? 'Auth';
$action = $_GET['action'] ?? 'index';

$controllerClass = $controllerName . 'Controller';
$controllerFile = __DIR__ . '/controllers/' . $controllerClass . '.php';
if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo 'Controlador no encontrado';
    exit;
}

require_once $controllerFile;

if (!class_exists($controllerClass)) {
    http_response_code(500);
    echo 'Clase de controlador inválida';   
    exit;
}

$ctrl = new $controllerClass();
if (method_exists($ctrl, 'dispatch')) {
    $ctrl->dispatch($action);
} else {
    http_response_code(500);
    echo 'Acción no disponible';
}
?>
