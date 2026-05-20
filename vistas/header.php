<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Sistema de Gestión de Solicitudes Académicas</title>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <header class="bg-gradient-to-r from-slate-900 to-slate-700 text-white shadow-xl">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="uppercase tracking-[0.3em] text-slate-300 text-sm">Interfaz académica</p>
                    <h1 class="mt-2 text-4xl font-extrabold tracking-tight">Gestión de Solicitudes Académicas</h1>
                    <p class="mt-3 max-w-2xl text-slate-200">Sistema reportes academicos</p>
                </div>
                <?php if ($currentUser): ?>
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-4 text-slate-100 shadow-xl backdrop-blur">
                        <p class="text-sm text-slate-200">Sesión iniciada</p>
                        <p class="mt-2 text-lg font-semibold"><?php echo htmlspecialchars(trim(($currentUser['nombre'] ?? '') . ' ' . ($currentUser['apellido'] ?? ''))); ?></p>
                        <p class="text-sm text-slate-200"><?php echo htmlspecialchars($currentUserRole === 'student' ? 'Estudiante' : 'Administrador'); ?></p>
                        <?php if ($currentUserRole === 'student'): ?>
                            <p class="text-sm text-slate-200">Programa: <?php echo htmlspecialchars($currentUser['programa']); ?></p>
                        <?php else: ?>
                            <p class="text-sm text-slate-200">Rol: <?php echo htmlspecialchars($currentUser['rol']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-4 py-10">
        <div class="space-y-8">
