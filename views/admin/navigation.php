<div class="grid gap-6 xl:grid-cols-[280px_1fr]">
    <aside class="rounded-3xl bg-white p-6 shadow-2xl border border-slate-200">
        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Panel de administrador</p>
        <nav class="mt-6 space-y-3">
            <a href="index.php?controller=Solicitud&action=index&panel=dashboard" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'dashboard' ? 'bg-slate-100 font-semibold' : ''; ?>">Resumen</a>
            <a href="index.php?controller=Solicitud&action=index&panel=admin_requests" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'admin_requests' ? 'bg-slate-100 font-semibold' : ''; ?>">Solicitudes pendientes</a>
            <a href="index.php?controller=Solicitud&action=index&panel=admin_reports" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'admin_reports' ? 'bg-slate-100 font-semibold' : ''; ?>">Historial</a>
            <a href="index.php?controller=Solicitud&action=index&panel=profile" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'profile' ? 'bg-slate-100 font-semibold' : ''; ?>">Perfil</a>
            <a href="index.php?controller=Auth&action=register" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'register' ? 'bg-slate-100 font-semibold' : ''; ?>">Crear cuenta</a>
            <a href="index.php?controller=Solicitud&action=index&panel=help" class="block rounded-2xl px-4 py-3 text-slate-900 hover:bg-slate-100 transition <?php echo $selectedPanel === 'help' ? 'bg-slate-100 font-semibold' : ''; ?>">Ayuda</a>
        </nav>
        <form method="post" action="index.php?controller=Auth" class="mt-8">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-white font-semibold hover:bg-slate-800 transition">Cerrar sesión</button>
        </form>
    </aside>
    <section class="space-y-6">
