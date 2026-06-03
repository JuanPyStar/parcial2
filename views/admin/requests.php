<?php $adminPendingFilter = $adminPendingFilter ?? 'all'; ?>
<?php $adminPendingDate = $adminPendingDate ?? ''; ?>
<?php $adminPendingDocument = $adminPendingDocument ?? ''; ?>
<?php include __DIR__ . '/../layout/result.php'; ?>
<?php include __DIR__ . '/navigation.php'; ?>
    <article class="rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Solicitudes pendientes</h2>
                <p class="mt-2 text-slate-600">Revisa y responde las solicitudes que están en pendiente.</p>
            </div>
            <form method="get" action="index.php" class="grid gap-3 sm:grid-flow-col sm:auto-cols-max sm:items-center">
                <input type="hidden" name="controller" value="Solicitud">
                <input type="hidden" name="action" value="index">
                <input type="hidden" name="panel" value="admin_requests">
                <select name="admin_pending_filter" class="rounded-3xl border border-slate-300 bg-white px-4 py-3 text-slate-700">
                    <option value="all" <?php echo $adminPendingFilter === 'all' ? 'selected' : ''; ?>>Todos</option>
                    <option value="Pendiente" <?php echo $adminPendingFilter === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="Falta información" <?php echo $adminPendingFilter === 'Falta información' ? 'selected' : ''; ?>>Falta información</option>
                    <option value="En espera" <?php echo $adminPendingFilter === 'En espera' ? 'selected' : ''; ?>>En espera</option>
                </select>
                <input type="date" name="admin_pending_date" value="<?php echo htmlspecialchars($adminPendingDate); ?>" class="rounded-3xl border border-slate-300 bg-white px-4 py-3 text-slate-700" placeholder="Fecha">
                <input type="text" name="admin_pending_document" value="<?php echo htmlspecialchars($adminPendingDocument); ?>" class="rounded-3xl border border-slate-300 bg-white px-4 py-3 text-slate-700" placeholder="Documento estudiante">
                <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Filtrar</button>
            </form>
        </div>

        <?php if (empty($adminPendingRequests)): ?>
            <div class="mt-8 rounded-3xl bg-slate-50 p-6 text-slate-600 border border-slate-200">No hay solicitudes pendientes para responder.</div>
        <?php else: ?>
            <div class="mt-8 overflow-x-auto rounded-3xl border border-slate-200 bg-slate-50 shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
                    <thead class="bg-slate-100 text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">ID</th>
                            <th class="px-6 py-4 font-semibold">Estudiante</th>
                            <th class="px-6 py-4 font-semibold">Tipo</th>
                            <th class="px-6 py-4 font-semibold">Fecha</th>
                            <th class="px-6 py-4 font-semibold">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <?php foreach ($adminPendingRequests as $request): ?>
                            <tr>
                                <td class="px-6 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($request['id']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($students[$request['estudiante_id']]['nombre'] ?? '') . ' ' . htmlspecialchars($students[$request['estudiante_id']]['apellido'] ?? ''); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars(getLabel($requestTypes, $request['tipo_solicitud_id'])); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars(formatDate($request['fecha'])); ?></td>
                                <td class="px-6 py-4 space-x-2">
                                    <a href="index.php?controller=Solicitud&action=index&panel=admin_requests&respond=<?php echo htmlspecialchars($request['id']); ?>" class="rounded-full bg-slate-900 px-4 py-2 text-white text-sm hover:bg-slate-800 transition">Responder</a>
                                    <form method="post" action="index.php?controller=Solicitud&action=index" class="inline-block">
                                        <input type="hidden" name="action" value="delete_request">
                                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                                        <button type="submit" class="rounded-full bg-rose-100 px-4 py-2 text-rose-700 text-sm hover:bg-rose-200 transition">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (!empty($respondRequest)): ?>
            <article class="mt-8 rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">Responder solicitud #<?php echo htmlspecialchars($respondRequest['id']); ?></h3>
                <form method="post" action="index.php?controller=Solicitud&action=index" class="mt-6 space-y-4">
                    <input type="hidden" name="action" value="submit_response">
                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($respondRequest['id']); ?>">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Estado</span>
                        <select name="response_state" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                            <option value="">Selecciona un estado</option>
                            <option value="Aprobada">Aprobada</option>
                            <option value="Rechazada">Rechazada</option>
                            <option value="Falta información">Falta información</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Observación</span>
                        <textarea name="response_observation" rows="4" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required></textarea>
                    </label>
                    <button type="submit" class="rounded-2xl bg-emerald-600 px-5 py-3 text-white font-semibold hover:bg-emerald-700 transition">Guardar respuesta</button>
                </form>
            </article>
        <?php endif; ?>
    </article>
</section>
</div>
