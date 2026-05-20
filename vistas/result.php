<?php if (!empty($result)): ?>
    <?php
        $resultClasses = 'rounded-3xl p-6 shadow-2xl border ';
        if ($result['type'] === 'student_request' || $result['type'] === 'admin_response' || $result['type'] === 'info') {
            $resultClasses .= 'bg-emerald-50 border-emerald-200 text-emerald-900';
        } else {
            $resultClasses .= 'bg-white border-slate-200 text-slate-900';
        }
    ?>
    <section class="<?php echo $resultClasses; ?>">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Notificación</p>
                <h2 class="mt-2 text-2xl font-semibold"><?php echo htmlspecialchars($result['message']); ?></h2>
            </div>
            <div class="rounded-3xl bg-slate-50 px-4 py-3 text-slate-700">
                <span class="text-sm font-semibold">Última acción</span>
            </div>
        </div>

        <?php if (!empty($result['request'])): ?>
            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Solicitud</h3>
                    <dl class="mt-4 space-y-4 text-slate-600">
                        <div>
                            <dt class="font-semibold text-slate-900">ID</dt>
                            <dd><?php echo $result['request']['id']; ?></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-900">Tipo</dt>
                            <dd><?php echo htmlspecialchars(getLabel($requestTypes, $result['request']['tipo_solicitud_id'])); ?></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-900">Programa</dt>
                            <dd><?php echo htmlspecialchars(getLabel($programs, $result['request']['programa_id'])); ?></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-900">Fecha</dt>
                            <dd><?php echo htmlspecialchars(formatDate($result['request']['fecha'])); ?></dd>
                        </div>
                    </dl>
                </div>
                <div class="rounded-3xl bg-slate-50 p-6 border border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Estado</h3>
                    <dl class="mt-4 space-y-4 text-slate-600">
                        <div>
                            <dt class="font-semibold text-slate-900">Estatus</dt>
                            <dd><?php echo htmlspecialchars($result['request']['estado']); ?></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-900">Sede</dt>
                            <dd><?php echo htmlspecialchars(getLabel($campuses, $result['request']['sede_id'])); ?></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-900">Jornada</dt>
                            <dd><?php echo htmlspecialchars(getLabel($shifts, $result['request']['jornada_id'])); ?></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-900">Observación</dt>
                            <dd><?php echo htmlspecialchars($result['request']['observacion'] ?: '-'); ?></dd>
                        </div>
                    </dl>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
