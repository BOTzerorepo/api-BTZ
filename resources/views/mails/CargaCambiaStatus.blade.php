<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Nueva Impo Terrestre</title>
  <meta name="x-preheader" content="Nueva Impo Terrestre para {{ $datos['trader'] ?? 'cliente' }} — detalles y estado."/>

  <style>
    body{margin:0;padding:0;background:#F5F5F5;-webkit-text-size-adjust:none;text-size-adjust:none}
    table{border-collapse:collapse}
    img{border:0;display:block;margin:0 auto;max-width:100%;height:auto}
    a{text-decoration:none;color:inherit}
    .wrapper{width:100%;background:#F5F5F5}
    .container{width:100%;max-width:650px;margin:0 auto;background:#FFFFFF}
    .p-24{padding:24px}
    .p-16{padding:16px}
    .center{text-align:center}
    .h1{font-size:22px;line-height:1.3;color:#1f2937;margin:8px 0}
    .h2{font-size:18px;line-height:1.35;color:#374151;margin:0 0 8px}
    .lead{font-size:14px;line-height:1.6;color:#374151;margin:0 0 12px}
    .tag{display:inline-block;background:#fdfdfd;color:#2a688b;font-size:12px;padding:4px 8px;border-radius:4px}
    .muted{color:#6b7280;font-size:12px}
    .hr{height:1px;background:#E5E7EB;border:0;margin:16px 0}
    .table{width:100%}
    .tdk{width:38%;vertical-align:top;padding:8px 10px;background:#FFFFFF}
    .tdv{width:62%;vertical-align:top;padding:8px 10px;background:#FFFFFF}
    .row-alt .tdk,.row-alt .tdv{background:#FAFAFA}
    .footer{font-size:12px;color:#9CA3AF}
    @media (max-width:670px){.p-24{padding:16px}.h1{font-size:20px}.h2{font-size:16px}}
  </style>
</head>
<body>
  <!-- Preheader oculto -->
  <span style="display:none !important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;overflow:hidden;">
    {{ 'Nueva Impo Terrestre para ' . ($datos['trader'] ?? 'cliente') . ' — detalles y estado.' }}
  </span>

  <table class="wrapper" role="presentation" width="100%" cellpadding="0" cellspacing="0">
    <tr><td style="height:24px;"></td></tr>

    <!-- Header / Logo -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="center p-24">
              <!-- Si preferís el recurso local, podés usar: {{ asset('image/CorreoAutomaticoTrans.png') }} -->
              <img src="https://btz.ar/ttg_mails.svg" alt="BOTzero" width="240"/>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Hero unificado -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <!-- Branding: fondo acorde al branding -->
            <td class="center p-16" style="background:#e0e0e0;">
              <!-- Status principal (una sola .tag para no duplicar) -->
              

              @if ($datos['status'] == 'ASIGNADA')
                <div class="tag">Unidad Asignada</div>
                <h1 class="h1">La carga ya tiene asignación de unidad.</h1>

              @elseif ($datos['status'] == 'YENDO A CARGAR')
              <div class="tag">{{ $datos['status'] }}</div>
                <h1 class="h1">La unidad se está dirigiendo {{$datos['load_place']}}</h1>

              @elseif ($datos['status'] == 'CARGANDO')
              <div class="tag">{{ $datos['status'] }}</div>
                <h1 class="h1">La unidad se encuentra en {{$datos['load_place']}}</h1>

              @elseif ($datos['status'] == 'EN ADUANA')
                @if( $datos['custom_place_impo'] != null )
                <div class="tag">{{ $datos['status'] }}</div>
                <h1 class="h1">La unidad se encuentra en {{$datos['custom_place_impo']}}</h1>
                @elseif( $datos['custom_place'] != null )
                <div class="tag">{{ $datos['status'] }}</div>
                <h1 class="h1">La unidad se encuentra en {{$datos['custom_place']}}</h1>
                @else
                <h1 class="h1">No está infomarda la aduana</h1>
                @endif
              @elseif ($datos['status'] == 'YENDO A DESCARGAR')
                <div class="tag">Unidad Camino a Descarga</div>
                <h1 class="h1">La unidad está en camino a {{$datos['unload_place']}}</h1>
                <p class="lead center">Estaremos informando más detalle en el transcurso.</p>

              @elseif ($datos['status'] == 'STACKING')
                <div class="tag">Unidad en Puerto</div>
                <h1 class="h1">La unidad está en {{$datos['unload_place']}}</h1>

              @elseif ($datos['status'] == 'CON PROBLEMA')
                <div class="tag" style="color:#b91c1c;">Unidad con Problemas</div>
                <h1 class="h1" style="color:#ef6c00;">{{$datos['description']}}</h1>

              @elseif ($datos['status'] == 'SALIENDO CARGAR')
                <div class="tag">Saliendo del Lugar de Carga</div>
                <h1 class="h1">La unidad está saliendo de {{$datos['load_place']}}</h1>

              @elseif ($datos['status'] == 'TERMINADA')
              <div class="tag">{{ $datos['status'] }}</div>
                <h1 class="h1">Servicio Finalizado.</h1>
              @endif
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Intro / Detalle -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="p-24">
              <h2 class="h2" style="margin-top:0;">Detalle</h2>
              <table class="table" role="presentation" cellpadding="0" cellspacing="0">
                <tr><td class="tdk">Descripción</td><td class="tdv">{{ $datos['description'] ?? '-' }}</td></tr>
                <tr class="row-alt"><td class="tdk">Booking</td><td class="tdv">{{ $datos['booking'] ?? '-' }}</td></tr>
                <tr>
                  <td class="tdk">Contenedor</td>
                  <td class="tdv">
                    @if( $datos['confirmacion'] != 0) 
                      {{ $datos['cntr'] }}
                    @else
                      CNTR SIN CONFIRMAR
                    @endif
                  </td>
                </tr>
              </table>

              <hr class="hr"/>

              <!-- Datos del Transporte (opcional, como en otras plantillas) -->
              @if(!empty($datos['transport']) || !empty($datos['transport_agent']) || !empty($datos['driver']) || !empty($datos['documento']) || !empty($datos['truck']) || !empty($datos['truck_semi']))
              <h2 class="h2">Datos del Transporte</h2>
              <table class="table" role="presentation" cellpadding="0" cellspacing="0">
                @if(!empty($datos['transport']))
                  <tr><td class="tdk">Transporte</td><td class="tdv">{{ $datos['transport'] }}</td></tr>
                @endif
                @if(!empty($datos['transport_agent']))
                  <tr class="row-alt"><td class="tdk">ATA</td><td class="tdv">{{ $datos['transport_agent'] }}</td></tr>
                @endif
                @if(!empty($datos['driver']))
                  <tr><td class="tdk">Chofer</td><td class="tdv">{{ $datos['driver'] }}</td></tr>
                @endif
                @if(!empty($datos['documento']))
                  <tr class="row-alt"><td class="tdk">DNI</td><td class="tdv">{{ $datos['documento'] }}</td></tr>
                @endif
                @if(!empty($datos['truck']))
                  <tr><td class="tdk">Camión</td><td class="tdv">{{ $datos['truck'] }}</td></tr>
                @endif
                @if(!empty($datos['truck_semi']))
                  <tr class="row-alt"><td class="tdk">Semi</td><td class="tdv">{{ $datos['truck_semi'] }}</td></tr>
                @endif
              </table>

              <hr class="hr"/>
              @endif

              <!-- Pie de status (como en las otras) -->
              <p class="center muted" style="margin:8px 0 0;">
                Status informado por<br/>
                <span style="font-size:14px;color:#2190e3;"><strong>{{ $datos['user'] ?? '-' }}</strong></span><br/>
                <span class="muted">Created at: {{ $datos['date'] ?? '-' }}</span>
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Footer unificado -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="center p-16">
              <p class="muted" style="margin:0 0 8px;">
                Tecnología programada por <strong>RailCode</strong> para <strong>BOTzero</strong>: Software de Logística.
              </p>
              <a href="https://botzero.tech" target="_blank">
                <img src="https://btz.ar/logo_mails.png" alt="BOTzero" width="120"/>
              </a>
              <p class="footer" style="margin:8px 0 0;">Rail | BOTzero</p>
              <p class="footer" style="margin:4px 0 0;">Este es un mensaje automático. No responder a este correo.</p>
            </td>
          </tr>
          <tr><td style="height:24px;"></td></tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
