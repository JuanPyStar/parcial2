<?php $adminHistoryFilter = $adminHistoryFilter ?? 'all'; ?>
<?php $adminHistoryDate = $adminHistoryDate ?? ''; ?>
<?php $adminHistoryDocument = $adminHistoryDocument ?? ''; ?>
<?php include __DIR__ . '/../layout/result.php'; ?>
<?php include __DIR__ . '/navigation.php'; ?>
    <article class="rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Historial de solicitudes</h2>
                <p class="mt-2 text-slate-600">Revisa solicitudes que ya han sido respondidas.</p>
            </div>
            <form method="get" action="index.php" class="grid gap-3 sm:grid-flow-col sm:auto-cols-max sm:items-center">
                <input type="hidden" name="controller" value="Solicitud">
                <input type="hidden" name="action" value="index">
                <input type="hidden" name="panel" value="admin_reports">
                <select name="admin_history_filter" class="rounded-3xl border border-slate-300 bg-white px-4 py-3 text-slate-700">
                    <option value="all" <?php echo $adminHistoryFilter === 'all' ? 'selected' : ''; ?>>Todos</option>
                    <option value="Aprobada" <?php echo $adminHistoryFilter === 'Aprobada' ? 'selected' : ''; ?>>Aprobadas</option>
                    <option value="Rechazada" <?php echo $adminHistoryFilter === 'Rechazada' ? 'selected' : ''; ?>>Rechazadas</option>
                </select>
                <input type="date" name="admin_history_date" value="<?php echo htmlspecialchars($adminHistoryDate); ?>" class="rounded-3xl border border-slate-300 bg-white px-4 py-3 text-slate-700" placeholder="Fecha">
                <input type="text" name="admin_history_document" value="<?php echo htmlspecialchars($adminHistoryDocument); ?>" class="rounded-3xl border border-slate-300 bg-white px-4 py-3 text-slate-700" placeholder="Documento estudiante">
                <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Filtrar</button>
            </form>
        </div>

        <?php if (empty($adminRespondedRequests)): ?>
            <div class="mt-8 rounded-3xl bg-slate-50 p-6 text-slate-600 border border-slate-200">No hay solicitudes respondidas en el historial.</div>
        <?php else: ?>
            <div class="mt-8 overflow-x-auto rounded-3xl border border-slate-200 bg-slate-50 shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
                    <thead class="bg-slate-100 text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">ID</th>
                            <th class="px-6 py-4 font-semibold">Estudiante</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold">Fecha</th>
                            <th class="px-6 py-4 font-semibold">Observación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <?php foreach ($adminRespondedRequests as $request): ?>
                            <tr>
                                <td class="px-6 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($request['id']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($students[$request['estudiante_id']]['nombre'] ?? '') . ' ' . htmlspecialchars($students[$request['estudiante_id']]['apellido'] ?? ''); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($request['estado']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars(formatDate($request['fecha'])); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($request['observacion'] ?? 'Sin observación'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>
</section>
</div>
