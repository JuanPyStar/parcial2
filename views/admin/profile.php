<?php include __DIR__ . '/../layout/result.php'; ?>
<?php include __DIR__ . '/navigation.php'; ?>
    <article class="rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
        <h2 class="text-2xl font-semibold text-slate-900">Mi perfil</h2>
        <p class="mt-2 text-slate-600">Revisa tus datos de administrador.</p>
        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Datos de administrador</h3>
                <dl class="mt-4 space-y-4 text-slate-600">
                    <div>
                        <dt class="font-semibold text-slate-900">Nombre</dt>
                        <dd><?php echo htmlspecialchars($currentUser['nombre'] ?? ''); ?></dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-900">Correo</dt>
                        <dd><?php echo htmlspecialchars($currentUser['correo'] ?? ''); ?></dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-900">Rol</dt>
                        <dd><?php echo htmlspecialchars($currentUser['rol'] ?? ''); ?></dd>
                    </div>
                </dl>
            </div>
            <div class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Acceso</h3>
                <p class="mt-4 text-slate-600">Para cambios de cuenta, contacta al soporte.</p>
            </div>
        </div>
    </article>
</section>
</div>
