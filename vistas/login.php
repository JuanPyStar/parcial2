<?php if (!$currentUser): ?>
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

        <div class="grid gap-10 lg:grid-cols-2">
            <div>
                <span class="inline-flex items-center rounded-full bg-sky-100 px-4 py-1 text-sm font-medium text-sky-700">LOGIN</span>
                <h2 class="mt-6 text-3xl font-semibold text-slate-900">Ingresa con tu cuenta</h2>
                <p class="mt-3 text-slate-600">Ingresa tu correo y contraseña. Si no tienes cuenta, puedes registrarte.</p>


            </div>

            <div class="space-y-6">
                <div id="login-card" class="rounded-3xl bg-slate-50 p-6 shadow-inner border border-slate-200">
                    <h3 class="text-xl font-semibold text-slate-900">Iniciar sesión</h3>
                    <form method="post" action="" class="mt-5 space-y-4">
                        <input type="hidden" name="action" value="login">

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Correo</span>
                            <input type="email" name="correo" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" placeholder="correo@dominio.com" required>
                        </label>

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Contraseña</span>
                            <input type="password" name="password" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" placeholder="••••••••" required>
                        </label>

                        <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-sky-600 to-indigo-600 px-5 py-3 text-white font-semibold shadow-lg hover:from-sky-700 hover:to-indigo-700 transition">
                            Entrar
                        </button>

                        <a href="vistas/register.php" target="_blank" rel="noopener" class="block text-center w-full rounded-2xl border border-slate-300 bg-white px-5 py-3 text-slate-900 font-semibold shadow hover:bg-slate-50 transition">
                            Crear cuenta
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
