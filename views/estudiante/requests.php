<?php include __DIR__ . '/../layout/result.php'; ?>
<?php include __DIR__ . '/navigation.php'; ?>
    <article class="rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Mis solicitudes</h2>
                <p class="mt-2 text-slate-600">Revisa el estado de tus solicitudes y responde si es necesario.</p>
            </div>
            <a href="index.php?controller=Solicitud&action=index&panel=new_request" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Nueva solicitud</a>
        </div>

        <?php if (empty($studentRequests)): ?>
            <div class="mt-8 rounded-3xl bg-slate-50 p-6 text-slate-600 border border-slate-200">No hay solicitudes registradas aún.</div>
        <?php else: ?>
            <div class="mt-8 overflow-x-auto rounded-3xl border border-slate-200 bg-slate-50 shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
                    <thead class="bg-slate-100 text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">ID</th>
                            <th class="px-6 py-4 font-semibold">Tipo</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold">Fecha</th>
                            <th class="px-6 py-4 font-semibold">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <?php foreach ($studentRequests as $request): ?>
                            <tr>
                                <td class="px-6 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($request['id']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars(getLabel($requestTypes, $request['tipo_solicitud_id'])); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($request['estado']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars(formatDate($request['fecha'])); ?></td>
                                <td class="px-6 py-4 space-x-2">
                                    <?php $canView = !empty($request['observacion']) || !in_array($request['estado'], ['Pendiente'], true); ?>
                                    <?php $canReply = in_array($request['estado'], ['Falta información','En espera'], true); ?>
                                    <?php if ($canView): ?>
                                        <a href="index.php?controller=Solicitud&action=index&panel=student_requests&view_request_id=<?php echo $request['id']; ?>" class="inline-flex rounded-full bg-emerald-100 px-3 py-2 text-emerald-700 text-sm hover:bg-emerald-200 transition">Ver</a>
                                    <?php endif; ?>
                                    <?php if ($canReply): ?>
                                        <a href="index.php?controller=Solicitud&action=index&panel=student_requests&reply_request_id=<?php echo $request['id']; ?>" class="rounded-full bg-slate-900 px-4 py-2 text-white text-sm hover:bg-slate-800 transition">Responder</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (!empty($viewRequest)): ?>
            <article class="mt-8 rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">Solicitud #<?php echo htmlspecialchars($viewRequest['id']); ?></h3>
                <div class="mt-4 grid gap-6 md:grid-cols-2 text-slate-700">
                    <div class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                        <h4 class="text-lg font-semibold text-slate-900">Información</h4>
                        <dl class="mt-4 space-y-3 text-slate-600">
                            <div><dt class="font-semibold text-slate-900">Tipo</dt><dd><?php echo htmlspecialchars(getLabel($requestTypes, $viewRequest['tipo_solicitud_id'])); ?></dd></div>
                            <div><dt class="font-semibold text-slate-900">Estado</dt><dd><?php echo htmlspecialchars($viewRequest['estado']); ?></dd></div>
                            <div><dt class="font-semibold text-slate-900">Fecha</dt><dd><?php echo htmlspecialchars(formatDate($viewRequest['fecha'])); ?></dd></div>
                            <div><dt class="font-semibold text-slate-900">Documento</dt><dd><?php echo $viewRequest['documento'] ? '<a href="uploads/' . htmlspecialchars(rawurlencode($viewRequest['documento'])) . '" class="text-slate-900 underline" target="_blank">Ver archivo</a>' : 'No aplica'; ?></dd></div>
                        </dl>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                        <h4 class="text-lg font-semibold text-slate-900">Detalles</h4>
                        <dl class="mt-4 space-y-3 text-slate-600">
                            <div><dt class="font-semibold text-slate-900">Descripción</dt><dd><?php echo nl2br(htmlspecialchars($viewRequest['descripcion'])); ?></dd></div>
                            <div><dt class="font-semibold text-slate-900">Observación</dt><dd><?php echo nl2br(htmlspecialchars($viewRequest['observacion'] ?? 'Sin observación')); ?></dd></div>
                        </dl>
                    </div>
                </div>
            </article>
        <?php endif; ?>

        <?php if (!empty($replyRequest)): ?>
            <article class="mt-8 rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">Responder solicitud #<?php echo htmlspecialchars($replyRequest['id']); ?></h3>
                <form method="post" action="index.php?controller=Solicitud&action=index" enctype="multipart/form-data" class="mt-6 space-y-4">
                    <input type="hidden" name="action" value="submit_student_reply">
                    <input type="hidden" name="panel" value="student_requests">
                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($replyRequest['id']); ?>">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Respuesta</span>
                        <textarea name="student_response" rows="4" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required></textarea>
                    </label>
                    <?php if ($replyRequest['estado'] === 'Falta información'): ?>
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Documento adicional</span>
                            <input type="file" name="response_document" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
                            <p class="mt-2 text-sm text-slate-500">Puedes adjuntar un archivo si necesitas corregir la solicitud.</p>
                        </label>
                    <?php endif; ?>
                    <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Enviar respuesta</button>
                </form>
            </article>
        <?php endif; ?>
    </article>
</section>
</div>
