<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';

abstract class BaseModel
{
    protected PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::pdo();
    }
}

