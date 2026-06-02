<div class="grid gap-6 xl:grid-cols-[280px_1fr]">
    <aside class="rounded-3xl bg-white p-6 shadow-2xl border border-slate-200">
        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Panel de estudiante</p>
        <nav class="mt-6 space-y-3">
            <a href="index.php?controller=Solicitud&action=index&panel=dashboard" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'dashboard' ? 'bg-slate-100 font-semibold' : ''; ?>">Resumen</a>
            <a href="index.php?controller=Solicitud&action=index&panel=new_request" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'new_request' ? 'bg-slate-100 font-semibold' : ''; ?>">Crear solicitud</a>
            <a href="index.php?controller=Solicitud&action=index&panel=student_requests" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'student_requests' ? 'bg-slate-100 font-semibold' : ''; ?>">Mis solicitudes</a>
            <a href="index.php?controller=Solicitud&action=index&panel=profile" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'profile' ? 'bg-slate-100 font-semibold' : ''; ?>">Perfil</a>
            <a href="index.php?controller=Solicitud&action=index&panel=help" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'help' ? 'bg-slate-100 font-semibold' : ''; ?>">Ayuda</a>
        </nav>
        <form method="post" action="index.php?controller=Auth" class="mt-8">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-white font-semibold hover:bg-slate-800 transition">Cerrar sesión</button>
        </form>
    </aside>
    <section class="space-y-6">
