<?php include __DIR__ . '/../layout/result.php'; ?>
<?php include __DIR__ . '/navigation.php'; ?>
    <section class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Solicitudes pendientes</p>
            <p class="mt-4 text-4xl font-bold text-amber-700"><?php echo $pendingCount; ?></p>
            <p class="mt-2 text-slate-600">Solicitudes que esperan respuesta.</p>
        </article>
        <article class="rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Solicitudes respondidas</p>
            <p class="mt-4 text-4xl font-bold text-emerald-700"><?php echo $respondedCount; ?></p>
            <p class="mt-2 text-slate-600">Casos cerrados en la plataforma.</p>
        </article>
        <article class="rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Total</p>
            <p class="mt-4 text-4xl font-bold text-slate-900"><?php echo count($allRequests); ?></p>
            <p class="mt-2 text-slate-600">Solicitudes registradas en el sistema.</p>
        </article>
    </section>

    <article class="rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
        <h2 class="text-2xl font-semibold text-slate-900">Administrador: <?php echo htmlspecialchars($currentUser['nombre'] ?? ''); ?></h2>
        <p class="mt-3 text-slate-600">Selecciona una opción del menú lateral para administrar solicitudes.</p>
    </article>
</section>
</div>
