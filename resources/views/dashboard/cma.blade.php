<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Timeline CMA</title>
    <style>
        body { font-family: sans-serif; background:#fafafa; padding:20px; }
        h1 { margin-bottom:25px; }
        .group-title { margin-top:40px; font-size:22px; }
        .card { background:white; padding:15px; margin-bottom:25px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,.1);}
        .header { font-weight:bold; margin-bottom:10px; }
        table { width:100%; border-collapse: collapse; font-size:14px; }
        th, td { padding:8px; border-bottom:1px solid #e6e6e6; }
        .type-event { color:#2563eb; font-weight:bold; }
        .type-coord { color:#16a34a; font-weight:bold; }
        .coords { font-size:12px; color:#555; }
    </style>
</head>
<body>

<h1>📦 Timeline Logístico por Contenedor</h1>

<h2 class="group-title">🚚 EN PROCESO</h2>
@foreach($inProcess as $container => $rows)
<div class="card">
    <div class="header">
        {{ $container }} — {{ $rows->first()->carrier_booking_reference ?? '' }}
    </div>

    <table>
        <tr><th>Fecha</th><th>Tipo</th><th>Detalle</th></tr>

        @foreach($rows as $r)
        <tr>
            <td>{{ $r->ts }}</td>

            <td>
                @if($r->type === 'coord')
                    <span class="type-coord">COOR</span>
                @else
                    <span class="type-event">EVENT</span>
                @endif
            </td>

            <td>
                @if($r->type === 'coord')
                    <div class="coords">Posición: {{ $r->lat }}, {{ $r->longitude }}</div>
                @else
                    {{ $r->event_type }} / {{ $r->transport_event_type_code }} / {{ $r->equipment_event_type_code }}
                @endif
            </td>
        </tr>
        @endforeach

    </table>
</div>
@endforeach


<h2 class="group-title">✅ TERMINADAS</h2>
@foreach($finished as $container => $rows)
<div class="card">
    <div class="header">
        {{ $container }} — {{ $rows->first()->carrier_booking_reference ?? '' }}
    </div>

    <table>
        <tr><th>Fecha</th><th>Tipo</th><th>Detalle</th></tr>

        @foreach($rows as $r)
        <tr>
            <td>{{ $r->ts }}</td>

            <td>
                @if($r->type === 'coord')
                    <span class="type-coord">COOR</span>
                @else
                    <span class="type-event">EVENT</span>
                @endif
            </td>

            <td>
                @if($r->type === 'coord')
                    <div class="coords">Posición: {{ $r->lat }}, {{ $r->longitude }}</div>
                @else
                    {{ $r->event_type }} / {{ $r->transport_event_type_code }} / {{ $r->equipment_event_type_code }}
                @endif
            </td>
        </tr>
        @endforeach

    </table>
</div>
@endforeach

</body>
</html>
