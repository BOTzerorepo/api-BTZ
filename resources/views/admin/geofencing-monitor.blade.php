<!DOCTYPE html>
<html>
<head>
    <title>Monitor Geofencing</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
        tr:hover { background: #f2f2f2; }
        .enter { color: green; font-weight: bold; }
        .exit { color: red; font-weight: bold; }
    </style>
    <meta http-equiv="refresh" content="60">
</head>
<body>
<h2>Monitor de Geofencing (Actualiza cada 60s)</h2>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Contenedor</th>
            <th>Patente</th>
            <th>Zona</th>
            <th>Evento</th>
            <th>Duración (minutos)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($events as $e)
        <tr>
            <td>{{ $e->created_at }}</td>
            <td>{{ $e->cntr_number }}</td>
            <td>{{ $e->truck_plate }}</td>
            <td>{{ $e->zone_type }}</td>
            <td class="{{ strtolower($e->event_type) }}">{{ $e->event_type }}</td>
            <td>{{ $e->duration_minutes ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
