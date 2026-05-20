<?php if ($currentUser): ?>
    <div class="grid gap-6 xl:grid-cols-[260px_1fr]">
        <aside class="rounded-3xl bg-white p-6 shadow-2xl border border-slate-200">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Menú de navegación</p>
            <nav class="mt-6 space-y-3">
                <a href="?panel=dashboard" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'dashboard' ? 'bg-slate-100 font-semibold' : ''; ?>">Resumen</a>
                <?php if ($currentUserRole === 'student'): ?>
                    <a href="?panel=new_request" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'new_request' ? 'bg-slate-100 font-semibold' : ''; ?>">Crear solicitud</a>
                    <a href="?panel=student_requests" class="flex items-center justify-between rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'student_requests' ? 'bg-slate-100 font-semibold' : ''; ?>">
                        <span>Mis solicitudes</span>
                        <?php if ($pendingCount > 0 || $respondedCount > 0): ?>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                <?php echo $pendingCount; ?> pendientes
                                <?php if ($respondedCount > 0): ?> / <?php echo $respondedCount; ?> respondidas<?php endif; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <?php if ($currentUserRole === 'admin'): ?>
                    <a href="?panel=admin_requests" class="flex items-center justify-between rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'admin_requests' ? 'bg-slate-100 font-semibold' : ''; ?>">
                        <span>Solicitudes pendientes</span>
                        <?php if ($pendingCount > 0): ?>
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-900"><?php echo $pendingCount; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="?panel=admin_reports" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'admin_reports' ? 'bg-slate-100 font-semibold' : ''; ?>">Historial</a>
                <?php endif; ?>
                <a href="?panel=help" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'help' ? 'bg-slate-100 font-semibold' : ''; ?>">Ayuda</a>
                <a href="?panel=profile" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'profile' ? 'bg-slate-100 font-semibold' : ''; ?>">Perfil</a>
            </nav>

            <form method="post" action="" class="mt-8">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-white font-semibold hover:bg-slate-800 transition">Cerrar sesión</button>
            </form>
        </aside>

        <section class="space-y-6">
            <?php include 'vistas/result.php'; ?>

            <?php if (!empty($errors)): ?>
                <section class="rounded-3xl bg-rose-50 border border-rose-200 p-6 text-rose-700 shadow-sm">
                    <p class="font-semibold">Hubo un problema al guardar los cambios:</p>
                    <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-rose-800">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if ($selectedPanel === 'dashboard' && $currentUserRole === 'admin' && $pendingCount > 0): ?>
                <section class="rounded-3xl bg-amber-50 border border-amber-200 p-6 text-amber-900 shadow-sm">
                    <p class="font-semibold">Tienes <?php echo $pendingCount; ?> solicitud<?php echo $pendingCount === 1 ? '' : 'es'; ?> pendientes por responder.</p>
                    <p class="mt-2 text-slate-700">Revisa el panel de <a href="?panel=admin_requests" class="font-semibold underline">Solicitudes pendientes</a>.</p>
                </section>
            <?php endif; ?>

            <?php if ($selectedPanel === 'dashboard' && $currentUserRole === 'student' && $respondedCount > 0): ?>
                <section class="rounded-3xl bg-emerald-50 border border-emerald-200 p-6 text-emerald-900 shadow-sm">
                    <p class="font-semibold">Tienes <?php echo $respondedCount; ?> solicitud<?php echo $respondedCount === 1 ? '' : 'es'; ?> respondida<?php echo $respondedCount === 1 ? '' : 's'; ?>.</p>
                    <p class="mt-2 text-slate-700">Revisa los detalles en <a href="?panel=student_requests" class="font-semibold underline">Mis solicitudes</a>.</p>
                </section>
            <?php endif; ?>

            <?php if ($selectedPanel === 'dashboard'): ?>
                <section class="rounded-3xl bg-white p-8 shadow-2xl border border-slate-200">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Resumen rápido</p>
                            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Panel de control</h2>
                            <p class="mt-3 text-slate-600">Aquí ves un resumen de las solicitudes y el estado actual del sistema.</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-3xl bg-slate-50 p-5 text-center shadow-sm">
                                <p class="text-sm text-slate-500">Solicitudes totales</p>
                                <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo count($allRequests); ?></p>
                            </div>
                            <div class="rounded-3xl bg-slate-50 p-5 text-center shadow-sm">
                                <p class="text-sm text-slate-500">Pendientes</p>
                                <p class="mt-3 text-3xl font-semibold text-amber-900"><?php echo $pendingCount; ?></p>
                            </div>
                            <div class="rounded-3xl bg-slate-50 p-5 text-center shadow-sm">
                                <p class="text-sm text-slate-500">Respondidas</p>
                                <p class="mt-3 text-3xl font-semibold text-emerald-900"><?php echo $respondedCount; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-6 lg:grid-cols-3">
                        <article class="rounded-3xl bg-slate-50 p-6 shadow-sm">
                            <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Perfil</p>
                            <h3 class="mt-3 text-xl font-semibold text-slate-900"><?php echo htmlspecialchars($currentUser['nombre']); ?></h3>
                            <p class="mt-2 text-slate-600"><?php echo htmlspecialchars($currentUserRole === 'student' ? $currentUser['programa'] : $currentUser['rol']); ?></p>
                        </article>
                        <article class="rounded-3xl bg-slate-50 p-6 shadow-sm">
                            <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Actividad</p>
                            <h3 class="mt-3 text-xl font-semibold text-slate-900"><?php echo $currentUserRole === 'student' ? 'Solicitudes registradas' : 'Solicitudes pendientes'; ?></h3>
                            <p class="mt-2 text-slate-600"><?php echo $currentUserRole === 'student' ? $studentRequestCount : $pendingCount; ?></p>
                        </article>
                        <article class="rounded-3xl bg-slate-50 p-6 shadow-sm">
                            <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Interacción</p>
                            <h3 class="mt-3 text-xl font-semibold text-slate-900">Navega con facilidad</h3>
                            <p class="mt-2 text-slate-600">Usa el menú lateral para crear solicitudes, revisar reportes y responder trámites.</p>
                        </article>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($selectedPanel === 'new_request' && $currentUserRole === 'student'): ?>
                <section class="rounded-3xl bg-white p-8 shadow-2xl border border-slate-200">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Formulario</p>
                            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Registrar nueva solicitud</h2>
                            <p class="mt-3 text-slate-600">Llena los campos para simular un trámite académico.</p>
                        </div>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="mt-6 rounded-3xl bg-rose-50 border border-rose-200 p-5 text-rose-700">
                            <ul class="space-y-2">
                                <?php foreach ($errors as $error): ?>
                                    <li>• <?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="" enctype="multipart/form-data" class="mt-8 space-y-6">
                        <input type="hidden" name="action" value="submit_request">
                        <input type="hidden" name="panel" value="new_request">

                        <div class="grid gap-6 lg:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Tipo de solicitud</span>
                                <select name="request_type" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none">
                                    <option value="">Selecciona un tipo</option>
                                    <?php foreach ($requestTypes as $id => $label): ?>
                                        <option value="<?php echo $id; ?>" <?php echo (isset($_POST['request_type']) && intval($_POST['request_type']) === $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Programa académico</span>
                                <select name="program" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none">
                                    <option value="">Selecciona un programa</option>
                                    <?php foreach ($programs as $id => $label): ?>
                                        <option value="<?php echo $id; ?>" <?php echo (isset($_POST['program']) && intval($_POST['program']) === $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-3">
                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Sede</span>
                                <select name="campus" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none">
                                    <option value="">Selecciona una sede</option>
                                    <?php foreach ($campuses as $id => $label): ?>
                                        <option value="<?php echo $id; ?>" <?php echo (isset($_POST['campus']) && intval($_POST['campus']) === $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Jornada</span>
                                <select name="shift" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none">
                                    <option value="">Selecciona una jornada</option>
                                    <?php foreach ($shifts as $id => $label): ?>
                                        <option value="<?php echo $id; ?>" <?php echo (isset($_POST['shift']) && intval($_POST['shift']) === $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Fecha del trámite</span>
                                <input type="text" readonly value="<?php echo date('d/m/Y'); ?>" class="mt-2 w-full rounded-3xl border border-slate-300 bg-slate-100 px-4 py-3 text-slate-700" />
                            </label>
                        </div>

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Descripción</span>
                            <textarea name="description" rows="5" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" placeholder="Describe el motivo de la solicitud..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </label>

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Documento adjunto (opcional)</span>
                            <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.png" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none">
                            <p class="mt-1 text-xs text-slate-500">Formatos permitidos: PDF, DOC, DOCX, JPG, PNG. Tamaño máximo: 5MB.</p>
                        </label>

                        <div class="grid gap-4 lg:grid-cols-2">
                            <button type="submit" class="rounded-3xl bg-gradient-to-r from-sky-600 to-indigo-600 px-6 py-4 text-white font-semibold shadow-lg hover:from-sky-700 hover:to-indigo-700 transition">Enviar solicitud</button>
                            <a href="?panel=new_request" class="inline-flex items-center justify-center rounded-3xl border border-slate-300 bg-white px-6 py-4 text-slate-900 font-semibold hover:bg-slate-50 transition">Limpiar formulario</a>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($selectedPanel === 'student_requests' && $currentUserRole === 'student'): ?>
                <section class="rounded-3xl bg-white p-8 shadow-2xl border border-slate-200">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Mis solicitudes</p>
                            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Historial de trámites</h2>
                            <p class="mt-3 text-slate-600">Aquí puedes consultar tus solicitudes registradas y sus estados.</p>
                        </div>
                    </div>
                    <form method="get" action="" class="mt-6 flex flex-wrap items-center gap-4">
                        <input type="hidden" name="panel" value="student_requests">
                        <label class="min-w-[220px]">
                            <span class="sr-only">Filtrar por estado</span>
                            <select name="student_filter" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-sky-500 focus:outline-none">
                                <?php foreach ($studentStatusOptions as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $studentFilter === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="submit" class="rounded-3xl bg-slate-900 px-6 py-3 text-white font-semibold hover:bg-slate-800 transition">Filtrar</button>
                        <a href="?panel=student_requests" class="inline-flex items-center rounded-3xl border border-slate-300 bg-white px-6 py-3 text-slate-900 font-semibold hover:bg-slate-50 transition">Mostrar todo</a>
                    </form>

                    <?php if ($pendingCount > 0 || $respondedCount > 0): ?>
                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-3xl bg-slate-50 p-5 text-center shadow-sm">
                                <p class="text-sm text-slate-500">Pendientes</p>
                                <p class="mt-3 text-3xl font-semibold text-amber-900"><?php echo $pendingCount; ?></p>
                            </div>
                            <div class="rounded-3xl bg-slate-50 p-5 text-center shadow-sm">
                                <p class="text-sm text-slate-500">Respondidas</p>
                                <p class="mt-3 text-3xl font-semibold text-emerald-900"><?php echo $respondedCount; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($replyRequest)): ?>
                        <div class="mt-8 rounded-3xl bg-slate-50 p-6 border border-slate-200 shadow-sm">
                            <h3 class="text-2xl font-semibold text-slate-900">Responder al administrador de la solicitud #<?php echo $replyRequest['id']; ?></h3>
                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div class="rounded-3xl bg-white p-5 border border-slate-200">
                                    <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Resumen de la solicitud</p>
                                    <dl class="mt-4 space-y-3 text-sm text-slate-700">
                                        <div>
                                            <dt class="font-semibold text-slate-900">Tipo</dt>
                                            <dd><?php echo htmlspecialchars(getLabel($requestTypes, $replyRequest['tipo_solicitud_id'])); ?></dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-slate-900">Fecha</dt>
                                            <dd><?php echo htmlspecialchars(formatDate($replyRequest['fecha'])); ?></dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-slate-900">Estado actual</dt>
                                            <dd><?php echo htmlspecialchars($replyRequest['estado']); ?></dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-slate-900">Descripción</dt>
                                            <dd><?php echo nl2br(htmlspecialchars($replyRequest['descripcion'])); ?></dd>
                                        </div>
                                    </dl>
                                </div>
                                <div class="rounded-3xl bg-white p-5 border border-slate-200">
                                    <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Observación del admin</p>
                                    <p class="mt-4 text-sm leading-relaxed text-slate-700"><?php echo nl2br(htmlspecialchars($replyRequest['observacion'] ?: 'No hay observaciones del admin aún.')); ?></p>
                                </div>
                            </div>
                            <form method="post" action="" class="mt-8 space-y-5">
                                <input type="hidden" name="action" value="submit_student_reply">
                                <input type="hidden" name="panel" value="student_requests">
                                <input type="hidden" name="student_filter" value="<?php echo htmlspecialchars($studentFilter); ?>">
                                <label class="block">
                                    <span class="text-sm font-semibold text-slate-700">Tu respuesta</span>
                                    <textarea name="student_response" rows="5" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" placeholder="Escribe tu respuesta para el administrador..."></textarea>
                                </label>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <button type="submit" class="rounded-3xl bg-gradient-to-r from-sky-600 to-indigo-600 px-6 py-4 text-white font-semibold hover:from-sky-700 hover:to-indigo-700 transition">Enviar respuesta</button>
                                    <a href="?panel=student_requests<?php echo $studentFilter !== 'all' ? '&student_filter=' . urlencode($studentFilter) : ''; ?>" class="inline-flex items-center justify-center rounded-3xl border border-slate-300 bg-white px-6 py-4 text-slate-900 font-semibold hover:bg-slate-50 transition">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="mt-8 overflow-x-auto rounded-3xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-left">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Tipo</th>
                                    <th class="px-6 py-4">Fecha</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4">Documento</th>
                                    <th class="px-6 py-4">Observación</th>
                                    <th class="px-6 py-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <?php if (empty($studentRequests)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-6 text-center text-slate-500">Aún no tienes solicitudes registradas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($studentRequests as $request): ?>
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 font-semibold text-slate-900"><?php echo $request['id']; ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars(getLabel($requestTypes, $request['tipo_solicitud_id'])); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars(formatDate($request['fecha'])); ?></td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold <?php echo badgeClass($request['estado']); ?>"><?php echo htmlspecialchars($request['estado']); ?></span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if (!empty($request['documento'])): ?>
                                                    <a href="uploads/<?php echo htmlspecialchars($request['documento']); ?>" target="_blank" class="text-sky-600 hover:text-sky-800">Ver documento</a>
                                                <?php else: ?>
                                                    No adjunto
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($request['observacion'] ?: 'Sin observación'); ?></td>
                                            <td class="px-6 py-4">
                                                <?php if (in_array($request['estado'], ['Observada', 'Falta información', 'En espera'], true)): ?>
                                                    <a href="?panel=student_requests&amp;reply_request_id=<?php echo $request['id']; ?><?php echo $studentFilter !== 'all' ? '&amp;student_filter=' . urlencode($studentFilter) : ''; ?>" class="inline-flex rounded-2xl bg-slate-900 px-4 py-2 text-white text-sm font-semibold hover:bg-slate-800 transition">Responder</a>
                                                <?php else: ?>
                                                    —
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($selectedPanel === 'admin_requests' && $currentUserRole === 'admin'): ?>
                <section class="rounded-3xl bg-white p-8 shadow-2xl border border-slate-200">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Pendientes</p>
                            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Solicitudes por atender</h2>
                            <p class="mt-3 text-slate-600">Revisa y responde las solicitudes en estado pendiente.</p>
                        </div>
                    </div>
                    <form method="get" action="" class="mt-6 flex flex-wrap items-center gap-4">
                        <input type="hidden" name="panel" value="admin_requests">
                        <label class="min-w-[220px]">
                            <span class="sr-only">Filtrar solicitudes pendientes</span>
                            <select name="admin_pending_filter" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-sky-500 focus:outline-none">
                                <?php foreach ($adminPendingStatusOptions as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $adminPendingFilter === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="submit" class="rounded-3xl bg-slate-900 px-6 py-3 text-white font-semibold hover:bg-slate-800 transition">Filtrar</button>
                        <a href="?panel=admin_requests" class="inline-flex items-center rounded-3xl border border-slate-300 bg-white px-6 py-3 text-slate-900 font-semibold hover:bg-slate-50 transition">Mostrar todo</a>
                    </form>

                    <?php if ($pendingCount > 0): ?>
                        <div class="mt-6 rounded-3xl bg-amber-50 border border-amber-200 p-5 text-amber-900">
                            <p class="font-semibold">Hay <?php echo $pendingCount; ?> solicitud<?php echo $pendingCount === 1 ? '' : 'es'; ?> pendiente<?php echo $pendingCount === 1 ? '' : 's'; ?> por responder.</p>
                            <p class="mt-2 text-slate-700">Atiende estas solicitudes para que el estudiante reciba respuesta.</p>
                        </div>
                    <?php endif; ?>
                    <?php if ($respondedCount > 0): ?>
                        <div class="mt-6 rounded-3xl bg-emerald-50 border border-emerald-200 p-5 text-emerald-900">
                            <p class="font-semibold">Ya has respondido <?php echo $respondedCount; ?> solicitud<?php echo $respondedCount === 1 ? '' : 'es'; ?>.</p>
                            <p class="mt-2 text-slate-700">Puedes revisar el historial de respuestas en la sección de Historial.</p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($respondRequest)): ?>
                        <?php $student = getStudent($students, $respondRequest['estudiante_id']); ?>
                        <div class="mt-8 rounded-3xl bg-slate-50 p-6 border border-slate-200 shadow-sm">
                            <h3 class="text-2xl font-semibold text-slate-900">Responder solicitud #<?php echo $respondRequest['id']; ?></h3>
                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div class="rounded-3xl bg-white p-5 border border-slate-200">
                                    <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Solicitud</p>
                                    <dl class="mt-4 space-y-3 text-sm text-slate-700">
                                        <div>
                                            <dt class="font-semibold text-slate-900">Estudiante</dt>
                                            <dd><?php echo htmlspecialchars(trim(($student['nombre'] ?? '') . ' ' . ($student['apellido'] ?? ''))); ?></dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-slate-900">Tipo</dt>
                                            <dd><?php echo htmlspecialchars(getLabel($requestTypes, $respondRequest['tipo_solicitud_id'])); ?></dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-slate-900">Programa</dt>
                                            <dd><?php echo htmlspecialchars(getLabel($programs, $respondRequest['programa_id'])); ?></dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-slate-900">Fecha registrada</dt>
                                            <dd><?php echo htmlspecialchars(formatDate($respondRequest['fecha'])); ?></dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-slate-900">Estado actual</dt>
                                            <dd><?php echo htmlspecialchars($respondRequest['estado'] === 'Pendiente' ? 'Sin responder' : $respondRequest['estado']); ?></dd>
                                        </div>
                                    </dl>
                                </div>
                                <div class="rounded-3xl bg-white p-5 border border-slate-200">
                                    <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Detalles</p>
                                    <div class="mt-4 space-y-4 text-sm text-slate-700">
                                        <div>
                                            <dt class="font-semibold text-slate-900">Descripción</dt>
                                            <p class="mt-1 leading-relaxed"><?php echo nl2br(htmlspecialchars($respondRequest['descripcion'])); ?></p>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-slate-900">Documento</dt>
                                            <p class="mt-1">
                                                <?php if (!empty($respondRequest['documento'])): ?>
                                                    <a href="uploads/<?php echo htmlspecialchars($respondRequest['documento']); ?>" target="_blank" class="font-semibold text-sky-600 hover:text-sky-800">Ver documento adjunto</a>
                                                <?php else: ?>
                                                    No hay documento adjunto.
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <?php if (!empty($respondRequest['observacion'])): ?>
                                            <div>
                                                <dt class="font-semibold text-slate-900">Observación previa</dt>
                                                <p class="mt-1 leading-relaxed"><?php echo nl2br(htmlspecialchars($respondRequest['observacion'])); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <form method="post" action="" class="mt-8 space-y-5">
                                <input type="hidden" name="action" value="submit_response">
                                <input type="hidden" name="panel" value="admin_requests">
                                <input type="hidden" name="admin_pending_filter" value="<?php echo htmlspecialchars($adminPendingFilter); ?>">
                                <input type="hidden" name="request_id" value="<?php echo $respondRequest['id']; ?>">

                                <label class="block">
                                    <span class="text-sm font-semibold text-slate-700">Estado final</span>
                                    <select name="response_state" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none">
                                        <option value="">Selecciona un estado</option>
                                        <option value="Aprobada">Aprobada</option>
                                        <option value="Rechazada">Rechazada</option>
                                        <option value="Observada">Observada</option>
                                        <option value="Falta información">Falta información</option>
                                        <option value="En espera">En espera</option>
                                    </select>
                                </label>

                                <label class="block">
                                    <span class="text-sm font-semibold text-slate-700">Observación</span>
                                    <textarea name="response_observation" rows="4" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none" placeholder="Escribe la observación del administrador..."></textarea>
                                </label>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <button type="submit" class="rounded-3xl bg-gradient-to-r from-sky-600 to-indigo-600 px-6 py-4 text-white font-semibold hover:from-sky-700 hover:to-indigo-700 transition">Guardar respuesta</button>
                                    <a href="?panel=admin_requests<?php echo $adminPendingFilter !== 'all' ? '&admin_pending_filter=' . urlencode($adminPendingFilter) : ''; ?>" class="inline-flex items-center justify-center rounded-3xl border border-slate-300 bg-white px-6 py-4 text-slate-900 font-semibold hover:bg-slate-50 transition">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="mt-8 overflow-x-auto rounded-3xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm text-left">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Estudiante</th>
                                    <th class="px-6 py-4">Tipo</th>
                                    <th class="px-6 py-4">Programa</th>
                                    <th class="px-6 py-4">Fecha</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4">Documento</th>
                                    <th class="px-6 py-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <?php if (empty($adminPendingRequests)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-6 text-center text-slate-500">No hay solicitudes pendientes en este momento.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($adminPendingRequests as $request): ?>
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 font-semibold text-slate-900"><?php echo $request['id']; ?></td>
                                            <?php $student = getStudent($students, $request['estudiante_id']); ?>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars(trim(($student['nombre'] ?? '') . ' ' . ($student['apellido'] ?? ''))); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars(getLabel($requestTypes, $request['tipo_solicitud_id'])); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars(getLabel($programs, $request['programa_id'])); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars(formatDate($request['fecha'])); ?></td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold <?php echo badgeClass($request['estado']); ?>"><?php echo htmlspecialchars($request['estado'] === 'Pendiente' ? 'Sin responder' : $request['estado']); ?></span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if (!empty($request['documento'])): ?>
                                                    <a href="uploads/<?php echo htmlspecialchars($request['documento']); ?>" target="_blank" class="text-sky-600 hover:text-sky-800">Ver documento</a>
                                                <?php else: ?>
                                                    No adjunto
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="?panel=admin_requests&amp;respond=<?php echo $request['id']; ?><?php echo $adminPendingFilter !== 'all' ? '&amp;admin_pending_filter=' . urlencode($adminPendingFilter) : ''; ?>" class="inline-flex rounded-2xl bg-sky-600 px-4 py-2 text-white text-sm font-semibold hover:bg-sky-700 transition">Responder</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </section>
            <?php endif; ?>
            <?php endif; ?>

            <?php if ($selectedPanel === 'admin_reports' && $currentUserRole === 'admin'): ?>
                <section class="rounded-3xl bg-white p-8 shadow-2xl border border-slate-200">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Historial</p>
                            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Solicitudes respondidas</h2>
                            <p class="mt-3 text-slate-600">Consulta el historial de trámites procesados.</p>
                        </div>
                    </div>
                    <form method="get" action="" class="mt-6 flex flex-wrap items-center gap-4">
                        <input type="hidden" name="panel" value="admin_reports">
                        <label class="min-w-[220px]">
                            <span class="sr-only">Filtrar historial por estado</span>
                            <select name="admin_history_filter" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-sky-500 focus:outline-none">
                                <option value="" <?php echo $adminHistoryFilter === '' ? 'selected' : ''; ?>>Selecciona un estado</option>
                                <?php foreach ($adminHistoryStatusOptions as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $adminHistoryFilter === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="submit" class="rounded-3xl bg-slate-900 px-6 py-3 text-white font-semibold hover:bg-slate-800 transition">Filtrar</button>
                        <a href="?panel=admin_reports" class="inline-flex items-center rounded-3xl border border-slate-300 bg-white px-6 py-3 text-slate-900 font-semibold hover:bg-slate-50 transition">Mostrar todo</a>
                    </form>

                    <?php if (!empty($editRequest)): ?>
                        <section class="mt-8 rounded-3xl bg-slate-50 p-6 border border-slate-200 shadow-sm">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Edición rápida</p>
                                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">Editar solicitud #<?php echo $editRequest['id']; ?></h3>
                                    <p class="mt-2 text-slate-600">Actualiza estado u observación antes de guardar cambios.</p>
                                </div>
                            </div>
                            <form method="post" action="" class="mt-6 space-y-4">
                                <input type="hidden" name="action" value="update_request">
                                <input type="hidden" name="panel" value="admin_reports">
                                <input type="hidden" name="admin_history_filter" value="<?php echo htmlspecialchars($adminHistoryFilter); ?>">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <label class="block">
                                        <span class="text-sm font-semibold text-slate-700">Estado</span>
                                        <select name="request_estado" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none">
                                            <option value="Respondida" <?php echo $editRequest['estado'] === 'Respondida' ? 'selected' : ''; ?>>Respondida</option>
                                            <option value="Observada" <?php echo $editRequest['estado'] === 'Observada' ? 'selected' : ''; ?>>Observada</option>
                                        </select>
                                    </label>
                                    <label class="block">
                                        <span class="text-sm font-semibold text-slate-700">Observación</span>
                                        <textarea name="request_observacion" rows="3" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 focus:border-sky-500 focus:outline-none"><?php echo htmlspecialchars($editRequest['observacion']); ?></textarea>
                                    </label>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <button type="submit" class="rounded-3xl bg-slate-900 px-6 py-3 text-white font-semibold hover:bg-slate-800 transition">Guardar cambios</button>
                                    <a href="?panel=admin_reports" class="inline-flex items-center justify-center rounded-3xl border border-slate-300 bg-white px-6 py-3 text-slate-900 font-semibold hover:bg-slate-50 transition">Cancelar</a>
                                </div>
                            </form>
                        </section>
                    <?php endif; ?>

                    <div class="mt-8 overflow-x-auto rounded-3xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-left">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Estudiante</th>
                                    <th class="px-6 py-4">Tipo</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4">Observación</th>
                                    <th class="px-6 py-4">Fecha respuesta</th>
                                    <th class="px-6 py-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <?php if (empty($adminRespondedRequests)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-6 text-center text-slate-500">Aún no hay solicitudes respondidas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($adminRespondedRequests as $request): ?>
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 font-semibold text-slate-900"><?php echo $request['id']; ?></td>
                                            <?php $student = getStudent($students, $request['estudiante_id']); ?>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars(trim(($student['nombre'] ?? '') . ' ' . ($student['apellido'] ?? ''))); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars(getLabel($requestTypes, $request['tipo_solicitud_id'])); ?></td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold <?php echo badgeClass($request['estado']); ?>"><?php echo htmlspecialchars($request['estado']); ?></span>
                                            </td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($request['observacion']); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars(formatDate($request['respuesta_fecha'])); ?></td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-2">
                                                    <a href="?panel=admin_reports&edit_request_id=<?php echo $request['id']; ?><?php echo $adminHistoryFilter !== 'all' ? '&admin_history_filter=' . urlencode($adminHistoryFilter) : ''; ?>" class="inline-flex items-center rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">Editar</a>
                                                    <form method="post" action="" class="inline-block" onsubmit="return confirm('¿Eliminar esta solicitud?');">
                                                        <input type="hidden" name="action" value="delete_request">
                                                        <input type="hidden" name="panel" value="admin_reports">
                                                        <input type="hidden" name="admin_history_filter" value="<?php echo htmlspecialchars($adminHistoryFilter); ?>">
                                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                        <button type="submit" class="inline-flex items-center rounded-2xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700 transition">Eliminar</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($selectedPanel === 'help'): ?>
                <section class="rounded-3xl bg-white p-8 shadow-2xl border border-slate-200">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Ayuda</p>
                            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Guía de uso</h2>
                        </div>
                    </div>

                    <?php if ($currentUserRole === 'student'): ?>
                        <div class="mt-8 grid gap-6 lg:grid-cols-1">
                            <article class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                                <h3 class="text-xl font-semibold text-slate-900">Hola estudiante</h3>
                                <p class="mt-4 text-slate-600">Sigue estos pasos para crear y revisar tus solicitudes.</p>
                                <ul class="mt-4 space-y-2 text-slate-600">
                                    <li>Entra a "Crear solicitud" para iniciar un trámite.</li>
                                    <li>Selecciona el tipo de solicitud, programa, sede y jornada.</li>
                                    <li>Describe con claridad por qué necesitas el trámite.</li>
                                    <li>Adjunta un documento solo si es necesario.</li>
                                    <li>Presiona "Enviar solicitud" cuando hayas terminado.</li>
                                    <li>Revisa el estado en "Mis solicitudes" para saber qué sigue.</li>
                                </ul>
                            </article>
                        </div>
                    <?php elseif ($currentUserRole === 'admin'): ?>
                        <div class="mt-8 grid gap-6 lg:grid-cols-1">
                            <article class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                                <h3 class="text-xl font-semibold text-slate-900">Hola administrador</h3>
                                <p class="mt-4 text-slate-600">Esto es lo que debes hacer para gestionar las solicitudes.</p>
                                <ul class="mt-4 space-y-2 text-slate-600">
                                    <li>Ve a "Solicitudes pendientes" para ver los trámites nuevos.</li>
                                    <li>Abre una solicitud y lee la descripción con atención.</li>
                                    <li>Revisa el archivo adjunto si existe.</li>
                                    <li>Elige el estado correcto y agrega una observación clara.</li>
                                    <li>Cuando respondes, el estudiante verá la actualización.</li>
                                    <li>Usa "Historial" para consultar solicitudes ya atendidas.</li>
                                </ul>
                            </article>
                        </div>
                    <?php else: ?>
                        <div class="mt-8 rounded-3xl bg-slate-50 p-6 border border-slate-200">
                            <p class="text-slate-600">Inicia sesión como estudiante o administrador para ver la ayuda correspondiente.</p>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <?php if ($selectedPanel === 'profile'): ?>
                <section class="rounded-3xl bg-white p-8 shadow-2xl border border-slate-200">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Perfil</p>
                            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Información del usuario</h2>
                            <p class="mt-3 text-slate-600">Detalles de la sesión y datos del perfil activo.</p>
                        </div>
                    </div>
                    <div class="mt-8 grid gap-6 lg:grid-cols-2">
                        <article class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                            <h3 class="text-xl font-semibold text-slate-900">Datos de acceso</h3>
                            <dl class="mt-4 space-y-4 text-slate-600">
                                <div>
                                    <dt class="font-semibold text-slate-900">Nombre</dt>
                                    <dd><?php echo htmlspecialchars($currentUser['nombre']); ?></dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-slate-900">Correo</dt>
                                    <dd><?php echo htmlspecialchars($currentUser['correo']); ?></dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-slate-900">Rol</dt>
                                    <dd><?php echo htmlspecialchars($currentUserRole === 'student' ? 'Estudiante' : 'Administrador'); ?></dd>
                                </div>
                            </dl>
                        </article>
                        <article class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                            <h3 class="text-xl font-semibold text-slate-900">Detalles adicionales</h3>
                            <dl class="mt-4 space-y-4 text-slate-600">
                                <?php if ($currentUserRole === 'student'): ?>
                                    <div>
                                        <dt class="font-semibold text-slate-900">Documento</dt>
                                        <dd><?php echo htmlspecialchars($currentUser['documento']); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold text-slate-900">Teléfono</dt>
                                        <dd><?php echo htmlspecialchars($currentUser['telefono']); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold text-slate-900">Programa</dt>
                                        <dd><?php echo htmlspecialchars($currentUser['programa']); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold text-slate-900">Semestre</dt>
                                        <dd><?php echo htmlspecialchars($currentUser['semestre']); ?></dd>
                                    </div>
                                <?php else: ?>
                                    <div>
                                        <dt class="font-semibold text-slate-900">Rol institucional</dt>
                                        <dd><?php echo htmlspecialchars($currentUser['rol']); ?></dd>
                                    </div>
                                <?php endif; ?>
                            </dl>
                        </article>
                    </div>
                </section>
            <?php endif; ?>
        </section>
    </div>
<?php endif; ?>
