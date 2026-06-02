<?php include __DIR__ . '/../layout/result.php'; ?>
<section class="rounded-3xl bg-white p-8 shadow-2xl border border-slate-200">
    <?php if (!empty($errors)): ?>
        <div class="mb-6 rounded-3xl bg-rose-50 border border-rose-200 p-5 text-rose-700">
            <p class="font-semibold">Revisa los campos:</p>
            <ul class="mt-3 space-y-2">
                <?php foreach ($errors as $error): ?>
                    <li>- <?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid gap-10 lg:grid-cols-2">
        <div>
            <span class="inline-flex items-center rounded-full bg-amber-100 px-4 py-1 text-sm font-medium text-amber-700">REGISTRO</span>
            <h2 class="mt-6 text-3xl font-semibold text-slate-900">Crea tu cuenta</h2>
            <p class="mt-3 text-slate-600">Regístrate como estudiante o administrador para usar el sistema.</p>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl bg-slate-50 p-6 shadow-inner border border-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">Registro de estudiante</h3>
                <form method="post" action="index.php?controller=Auth" class="mt-5 space-y-4">
                    <input type="hidden" name="action" value="register_student">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Nombre</span>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($old['nombre'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Apellido</span>
                        <input type="text" name="apellido" value="<?php echo htmlspecialchars($old['apellido'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Documento</span>
                        <input type="text" name="documento" value="<?php echo htmlspecialchars($old['documento'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Correo</span>
                        <input type="email" name="correo" value="<?php echo htmlspecialchars($old['correo'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Programa</span>
                        <input type="text" name="programa" value="<?php echo htmlspecialchars($old['programa'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Semestre</span>
                        <input type="number" name="semestre" value="<?php echo htmlspecialchars($old['semestre'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" min="1" required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Contraseña</span>
                        <input type="password" name="password" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Repite la contraseña</span>
                        <input type="password" name="password2" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                    </label>
                    <button type="submit" class="w-full rounded-2xl bg-amber-600 px-5 py-3 text-white font-semibold hover:bg-amber-700 transition">Registrar estudiante</button>
                    <a href="index.php?controller=Auth&action=login" class="block text-center w-full rounded-2xl border border-slate-300 bg-white px-5 py-3 text-slate-900 font-semibold shadow hover:bg-slate-50 transition">Volver al login</a>
                </form>
            </div>
        </div>
    </div>
</section>
