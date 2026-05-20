<?php
session_start();

require_once __DIR__ . '/logic.php';

if ($currentUser) {
    header('Location: ../index.php');
    exit;
}

include __DIR__ . '/header.php';
?>

<section class="rounded-3xl bg-white p-8 shadow-2xl border border-slate-200">
    <?php if (!empty($errors)): ?>
        <div class="mb-6 rounded-3xl bg-rose-50 border border-rose-200 p-5 text-rose-700">
            <p class="font-semibold">Revisa lo siguiente:</p>
            <ul class="mt-3 space-y-2">
                <?php foreach ($errors as $error): ?>
                    <li>- <?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($result) && is_array($result)): ?>
        <div class="mb-6 rounded-3xl border p-5 <?php echo $result['type'] === 'info' ? 'bg-sky-50 border-sky-200 text-sky-800' : 'bg-slate-50 border-slate-200 text-slate-800'; ?>">
            <p class="font-semibold"><?php echo htmlspecialchars((string)($result['message'] ?? '')); ?></p>
        </div>
    <?php endif; ?>

    <div class="grid gap-10 lg:grid-cols-2">
        <div>
            <span class="inline-flex items-center rounded-full bg-sky-100 px-4 py-1 text-sm font-medium text-sky-700">REGISTRO</span>
            <h2 class="mt-6 text-3xl font-semibold text-slate-900">Crea tu cuenta de estudiante</h2>
            <p class="mt-3 text-slate-600">Completa tus datos para crear la cuenta. Al finalizar, iniciarás sesión automáticamente.</p>

            <a href="../index.php" class="mt-6 inline-flex items-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-slate-900 font-semibold shadow hover:bg-slate-50 transition">
                Volver al login
            </a>
        </div>

        <div class="rounded-3xl bg-white border border-slate-200 p-6">
            <h3 class="text-xl font-semibold text-slate-900">Crear cuenta</h3>
            <form method="post" action="" class="mt-5 space-y-4">
                <input type="hidden" name="action" value="register_student">

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Nombre</span>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Apellido</span>
                        <input type="text" name="apellido" value="<?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" required>
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Documento</span>
                        <input type="text" name="documento" value="<?php echo htmlspecialchars($_POST['documento'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Teléfono</span>
                        <input type="text" name="telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none">
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Correo</span>
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" required>
                </label>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Programa</span>
                        <select name="programa_id" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" required>
                            <option value="">Selecciona</option>
                            <?php foreach ($programs as $id => $label): ?>
                                <option value="<?php echo (int)$id; ?>" <?php echo ((string)($id) === (string)($_POST['programa_id'] ?? '') ? 'selected' : ''); ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Semestre</span>
                        <input type="number" name="semestre" min="1" max="12" value="<?php echo htmlspecialchars($_POST['semestre'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" required>
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Contraseña</span>
                        <input type="password" name="password" minlength="6" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Confirmar</span>
                        <input type="password" name="password2" minlength="6" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" required>
                    </label>
                </div>

                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold shadow-lg hover:bg-slate-800 transition">
                    Crear cuenta estudiante
                </button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>

