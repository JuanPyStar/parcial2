<?php include __DIR__ . '/../layout/result.php'; ?>
<?php include __DIR__ . '/navigation.php'; ?>
    <?php if (!empty($errors)): ?>
        <section class="mt-6 rounded-3xl bg-rose-50 p-6 shadow-sm border border-rose-200 text-rose-900">
            <h3 class="text-lg font-semibold">Corrige los siguientes errores:</h3>
            <ul class="mt-4 list-disc pl-6">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>
    <article class="rounded-3xl bg-white p-6 shadow-xl border border-slate-200">
        <h2 class="text-2xl font-semibold text-slate-900">Crear nueva solicitud</h2>
        <p class="mt-2 text-slate-600">Completa el formulario para registrar tu solicitud académica.</p>
        <form method="post" action="index.php?controller=Solicitud&action=index" enctype="multipart/form-data" class="mt-6 space-y-6">
            <input type="hidden" name="action" value="submit_request">
            <input type="hidden" name="panel" value="new_request">
            <div class="grid gap-6 lg:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Tipo de solicitud</span>
                    <select name="request_type" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                        <option value="">Selecciona una opción</option>
                        <?php foreach ($requestTypes as $id => $label): ?>
                            <option value="<?php echo $id; ?>" <?php echo ((int)($old['request_type'] ?? 0) === $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Programa</span>
                    <select name="program" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                        <option value="">Selecciona un programa</option>
                        <?php foreach ($programs as $id => $label): ?>
                            <option value="<?php echo $id; ?>" <?php echo ((int)($old['program'] ?? 0) === $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Sede</span>
                    <select name="campus" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                        <option value="">Selecciona una sede</option>
                        <?php foreach ($campuses as $id => $label): ?>
                            <option value="<?php echo $id; ?>" <?php echo ((int)($old['campus'] ?? 0) === $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Jornada</span>
                    <select name="shift" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required>
                        <option value="">Selecciona una jornada</option>
                        <?php foreach ($shifts as $id => $label): ?>
                            <option value="<?php echo $id; ?>" <?php echo ((int)($old['shift'] ?? 0) === $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Descripción</span>
                <textarea name="description" rows="5" class="mt-2 w-full rounded-3xl border border-slate-300 bg-white px-4 py-3" required><?php echo htmlspecialchars($old['description'] ?? ''); ?></textarea>
            </label>
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Documento adjunto</span>
                <input type="file" name="document" class="mt-2 w-full text-slate-700" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            </label>
            <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Enviar solicitud</button>
        </form>
    </article>
</section>
</div>
