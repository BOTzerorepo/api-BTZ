<h2>Viajes activos</h2>
<table border="1" cellpadding="6">
<tr>
  <th>Contenedor</th>
  <th>Booking</th>
  <th>Último evento</th>
  <th></th>
</tr>
@foreach($rows as $r)
<tr>
  <td>{{ $r->equipment_reference }}</td>
  <td>{{ $r->carrier_booking_reference }}</td>
  <td>{{ $r->last_ts }}</td>
  <td><a href="/viajes/{{ $r->equipment_reference }}">Ver detalle</a></td>
</tr>
@endforeach
</table>
