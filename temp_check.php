<?php
require 'config/bootstrap.php';
require 'modelos/SolicitudModel.php';
try {
    $m = new SolicitudModel();
    echo "OK\n";
    $rows = $m->listar();
    echo "count=" . count($rows) . "\n";
    if (count($rows)) {
        var_dump(array_slice($rows,0,2));
    }
} catch (Throwable $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
}
