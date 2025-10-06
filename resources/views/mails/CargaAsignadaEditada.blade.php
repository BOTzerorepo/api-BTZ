<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Editada la asignación de unidad</title>
  <meta name="x-preheader" content="Editamos la asignación para {{ $datos['booking'] }}"/>

  <style>
    body{margin:0;padding:0;background:#F5F5F5;-webkit-text-size-adjust:none;text-size-adjust:none}
    table{border-collapse:collapse}
    img{border:0;display:block;margin:0 auto;max-width:100%;height:auto}
    a{text-decoration:none;color:inherit}

    /* Layout base unificado */
    .wrapper{width:100%;background:#F5F5F5}
    .container{width:100%;max-width:650px;margin:0 auto;background:#FFFFFF}
    .p-24{padding:24px}
    .p-16{padding:16px}
    .center{text-align:center}

    /* Tipografía / colores */
    .h1{font-size:22px;line-height:1.3;color:#052d3d;margin:8px 0}
    .h2{font-size:18px;line-height:1.35;color:#052d3d;margin:0 0 8px}
    .lead{font-size:14px;line-height:1.6;color:#374151;margin:0 0 12px}
    .muted{color:#6b7280;font-size:12px}
    .tag{display:inline-block;background:#FFF4E5;color:#B45309;font-size:12px;padding:4px 8px;border-radius:4px}

    /* Separadores / tablas */
    .hr{height:1px;background:#E5E7EB;border:0;margin:16px 0}
    .table{width:100%}
    .kv{width:100%}
    .kv td{vertical-align:top;padding:10px}
    .kv .k{width:42%;background:#FFFFFF}
    .kv .v{width:58%;background:#FFFFFF}
    .kv .alt .k,.kv .alt .v{background:#FAFAFA}

    /* Badges */
    .badge{display:inline-block;padding:8px 12px;border-radius:8px;background:#E6F4EA;color:#1D944E;font-weight:700}
    .badge-danger{background:#FDECEC;color:#B91C1C}

    @media (max-width:670px){
      .p-24{padding:16px}
      .h1{font-size:20px}
      .h2{font-size:16px}
      .kv .k,.kv .v{display:block;width:100%}
    }
  </style>
</head>
<body>
  <!-- Preheader oculto -->
  <span style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;overflow:hidden;">
    Editamos la asignación para {{ $datos['booking'] }}
  </span>

  <table class="wrapper" role="presentation" width="100%" cellpadding="0" cellspacing="0">
    <tr><td style="height:24px;"></td></tr>

    <!-- Header / Logo -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="center p-24">
              <img src="https://btz.ar/ttg_mails.svg" alt="BOTZero" width="240"/>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Hero unificado (tono de actualización) -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="center p-16" style="background:#FDEAD7;">
              <img src="{{ asset('image/AlertasMails/status.png') }}" alt="Estado" width="72" style="margin-bottom:12px;"/>
              <div class="tag">Actualización operativa</div>
              <h1 class="h1" style="margin-top:6px;">EDITADA LA ASIGNACIÓN DE UNIDAD</h1>
              <h2 class="h2">
                Editamos la asignación de unidad para:
                <br/>
                @if( $datos['confirmacion'] != 0) 
                  <strong>Carga {{ $datos['cntr'] }} para el {{ $datos['booking'] }}</strong>
                @else
                  <strong>Carga CNTR SIN CONFIRMAR para el {{ $datos['booking'] }}</strong>
                @endif
              </h2>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Resumen / meta -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="p-24">
              <p class="muted" style="margin:0 0 4px;">Status informado por <strong>{{ $datos['user']}}</strong></p>
              <p class="muted" style="margin:0;">Creado el {{ $date}}</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Detalle de nueva asignación -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="p-24">
              <h2 class="h2" style="margin-top:0;">Los detalles de la nueva asignación son:</h2>
              <hr class="hr"/>

              <!-- CNTR / Encabezado -->
              <p class="center" style="margin:0 0 12px;">
                @if( $datos['confirmacion'] != 0)
                  <span class="badge">{{ $datos['cntr_number'] }}</span>
                @else
                  <span class="badge badge-danger">SIN CONFIRMAR</span>
                @endif
              </p>

              <!-- Lista clave/valor -->
              <table class="kv" role="presentation" cellpadding="0" cellspacing="0" style="margin-top:8px;">
                <tr>
                  <td class="k">Transporte:</td>
                  <td class="v">{{$datos['transport']}}</td>
                </tr>
                <tr class="alt">
                  <td class="k">ATA:</td>
                  <td class="v">{{$datos['transport_agent']}}</td>
                </tr>
                <tr>
                  <td class="k">Chofer:</td>
                  <td class="v">{{$datos['driver']}}</td>
                </tr>
                <tr class="alt">
                  <td class="k">Tractor:</td>
                  <td class="v">{{$datos['truck']}}</td>
                </tr>
                <tr>
                  <td class="k">Semi:</td>
                  <td class="v">{{$datos['truck_semi']}}</td>
                </tr>
              </table>

              <hr class="hr"/>
              <p class="muted center" style="margin:0;">
                Status informado por <strong>{{ $datos['user']}}</strong> — Creado el {{ $date}}
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
                Tecnología programada por <strong>RailCode</strong> para <strong>BOTZero</strong>: Software de Logística.
              </p>
              <a href="https://botzero.tech" target="_blank">
                <img src="https://btz.ar/logo_mails.png" alt="BOTZero" width="120"/>
              </a>
              <p class="muted" style="margin:8px 0 0;">Este es un mensaje automático. No responder a este correo.</p>
            </td>
          </tr>
          <tr><td style="height:24px;"></td></tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
