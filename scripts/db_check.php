<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';

function println(string $line): void
{
    echo $line . PHP_EOL;
}

try {
    $pdo = Database::pdo();
    $db = $pdo->query('SELECT DATABASE() d')->fetch()['d'] ?? '(none)';
    println("DB: {$db}");

    $st = $pdo->prepare('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND COLUMN_NAME = ?');
    $st->execute([$db, 'contraseña']);
    $tables = array_map(fn($r) => $r['TABLE_NAME'], $st->fetchAll());

    println('Tables with contraseña: ' . (empty($tables) ? '(none)' : implode(', ', $tables)));

    $st2 = $pdo->query('SHOW TABLES');
    $allTables = array_map(fn($r) => array_values($r)[0], $st2->fetchAll());
    println('All tables: ' . implode(', ', $allTables));
} catch (Throwable $e) {
    println('ERROR: ' . $e->getMessage());
    exit(1);
}

