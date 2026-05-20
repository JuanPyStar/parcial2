<?php

declare(strict_types=1);

abstract class BaseControlador
{
    protected function ok(mixed $data = null): array
    {
        return ['ok' => true, 'data' => $data];
    }

    protected function fail(string $message, mixed $detail = null): array
    {
        return ['ok' => false, 'message' => $message, 'detail' => $detail];
    }
}

