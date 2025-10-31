<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Panel de Geoeventos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
  <h1 class="h4 mb-3">Eventos geográficos (ENTER/EXIT)</h1>

  <div class="mb-3">
    <label class="form-label">Trip ID</label>
    <input id="tripId" class="form-control" placeholder="(opcional) ej: 1234">
  </div>

  <button class="btn btn-primary mb-3" onclick="loadData()">Actualizar</button>

  <h2 class="h6 mt-4">Estado actual por POI</h2>
  <table class="table table-sm" id="tblActive">
    <thead><tr><th>Trip</th><th>POI</th><th>Tipo</th><th>Orden</th><th>Estado</th></tr></thead>
    <tbody></tbody>
  </table>

  <h2 class="h6 mt-4">Últimos eventos</h2>
  <table class="table table-sm" id="tblActions">
    <thead><tr><th>Fecha</th><th>Trip</th><th>CNTR</th><th>Domain</th><th>Action</th><th>Punto</th><th>Dist (m)</th></tr></thead>
    <tbody></tbody>
  </table>

<script>
async function loadData(){
  const tripId = document.getElementById('tripId').value.trim();
  const qs = tripId ? `?trip_id=${encodeURIComponent(tripId)}` : '';

  // Estado activo
  const active = await fetch(`/api/geo/active${qs}`).then(r=>r.json());
  const tbA = document.querySelector('#tblActive tbody'); tbA.innerHTML='';
  active.forEach(r=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${r.trip_id}</td><td>${r.description}</td><td>${r.type}</td><td>${r.order}</td><td>${stateLabel(r.state)}</td>`;
    tbA.appendChild(tr);
  });

  // Acciones
  const actions = await fetch(`/api/geo/actions${qs}`).then(r=>r.json());
  const tb = document.querySelector('#tblActions tbody'); tb.innerHTML='';
  actions.forEach(r=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${r.created_at ?? ''}</td><td>${r.trip_id}</td><td>${r.cntr_number}</td><td>${r.domain}</td><td>${r.action_type}</td><td>${(r.meta && r.meta.poi_desc) ? r.meta.poi_desc : ''}</td><td>${r.distance_m?.toFixed?.(0) ?? ''}</td>`;
    tb.appendChild(tr);
  });
}

function stateLabel(s){
  return ({0:'Pendiente',1:'Dentro',2:'Completado/Fuera'})[s] ?? s;
}

loadData();
</script>
</body>
</html>
