<?php declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/helpers.php';

class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $currentUser = $_SESSION['current_user'] ?? null;
        $currentUserRole = $_SESSION['user_role'] ?? null;

        $header = __DIR__ . '/../views/layout/header.php';
        $footer = __DIR__ . '/../views/layout/footer.php';

        if (file_exists($header)) {
            include $header;
        }

        include __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($footer)) {
            include $footer;
        }
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
