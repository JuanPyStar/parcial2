<?php include __DIR__ . '/../layout/result.php'; ?>
<?php include __DIR__ . '/navigation.php'; ?>
    <article class="rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
        <h2 class="text-2xl font-semibold text-slate-900">Mi perfil</h2>
        <p class="mt-2 text-slate-600">Revisa tus datos personales registrados en el sistema.</p>
        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Datos personales</h3>
                <dl class="mt-4 space-y-4 text-slate-600">
                    <div>
                        <dt class="font-semibold text-slate-900">Nombre</dt>
                        <dd><?php echo htmlspecialchars($currentUser['nombre'] ?? ''); ?></dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-900">Correo</dt>
                        <dd><?php echo htmlspecialchars($currentUser['correo'] ?? ''); ?></dd>
                    </div>
                    <?php if ($currentUserRole === 'student'): ?>
                        <div>
                            <dt class="font-semibold text-slate-900">Documento</dt>
                            <dd><?php echo htmlspecialchars($currentUser['documento'] ?? ''); ?></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-900">Programa</dt>
                            <dd><?php echo htmlspecialchars($currentUser['programa'] ?? ''); ?></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-900">Semestre</dt>
                            <dd><?php echo htmlspecialchars((string)($currentUser['semestre'] ?? '')); ?></dd>
                        </div>
                    <?php endif; ?>
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
