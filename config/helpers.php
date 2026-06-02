<?php declare(strict_types=1);

function formatMoney($value)
{
    return '$' . number_format($value, 0, ',', '.');
}

function formatDate($date)
{
    $timestamp = strtotime($date);
    return $timestamp ? date('d/m/Y', $timestamp) : $date;
}

function getLabel(array $catalog, $id)
{
    return isset($catalog[$id]) ? $catalog[$id] : 'N/D';
}

function getStudent(array $students, $id)
{
    return $students[$id] ?? ['nombre' => 'N/D', 'apellido' => ''];
}

function getAdmin(array $administrators, $id)
{
    return $administrators[$id] ?? ['nombre' => 'N/D'];
}

function badgeClass($status)
{
    return match ($status) {
        'Pendiente', 'Sin responder' => 'bg-amber-100 text-amber-800',
        'Falta información', 'En espera' => 'bg-amber-100 text-amber-800',
        'Aprobada' => 'bg-emerald-100 text-emerald-800',
        'Rechazada' => 'bg-red-100 text-red-800',
        'Observada' => 'bg-sky-100 text-sky-800',
        default => 'bg-slate-100 text-slate-700',
    };
}

function filterRequestsByStatus(array $requests, string $status): array
{
    if ($status === '' || $status === 'all') {
        return $requests;
    }

    return array_values(array_filter($requests, function (array $request) use ($status) {
        return isset($request['estado']) && $request['estado'] === $status;
    }));
}

function mapDbSolicitudToUi(array $row): array
{
    return [
        'id' => (int)($row['id_solicitud'] ?? 0),
        'fecha' => (string)($row['fecha'] ?? ''),
        'estado' => (string)($row['estado'] ?? 'Pendiente'),
        'tipo_solicitud_id' => (int)($row['id_tipo_solicitud'] ?? 0),
        'descripcion' => (string)($row['descripcion'] ?? ''),
        'estudiante_id' => (int)($row['id_estudiante'] ?? 0),
        'programa_id' => isset($row['programa_id']) ? (int)$row['programa_id'] : 0,
        'sede_id' => isset($row['sede_id']) ? (int)$row['sede_id'] : 0,
        'jornada_id' => isset($row['jornada_id']) ? (int)$row['jornada_id'] : 0,
        'observacion' => (string)($row['observacion'] ?? ''),
        'admin_id' => array_key_exists('admin_id', $row) && $row['admin_id'] !== null ? (int)$row['admin_id'] : null,
        'respuesta_fecha' => (string)($row['respuesta_fecha'] ?? ''),
        'documento' => (string)($row['documento'] ?? ''),
    ];
}
