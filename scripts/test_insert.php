<?php
require_once __DIR__ . '/../modelos/SolicitudModel.php';

try {
    $model = new SolicitudModel();
    $id = $model->crearCompleta(
        null,
        'Pendiente',
        'Test desc',
        1,
        1,
        124,
        1,
        1,
        null
    );
    echo "Success! ID: $id\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
