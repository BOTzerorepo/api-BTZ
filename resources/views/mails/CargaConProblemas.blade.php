<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Carga con problema</title>

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

    /* Branding */
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
    {{ 'Carga con problema — Booking ' . ($datos['booking'] ?? '-') . '.' }}
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

    <!-- Hero unificado (branding) -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="center p-16" style="background:#e0e0e0;">
              <div class="tag" style="color:#b91c1c;">CARGA CON PROBLEMA</div>
              <h1 class="h1" style="color:#dc2626;margin-top:6px;">Se reportó un inconveniente con la carga</h1>

            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Detalle -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="p-24">
              <h2 class="h2" style="margin-top:0;">Detalle</h2>

              <!-- Encabezado CNTR / Booking -->
              <table class="table" role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
                <tr>
                  <td class="tdk">Contenedor</td>
                  <td class="tdv" style="color:#b91c1c;font-weight:700;">
                    @if( $datos['confirmacion'] != 0)
                      {{ $datos['cntr'] }}
                    @else
                      SIN CONFIRMAR
                    @endif
                  </td>
                </tr>
                <tr class="row-alt">
                  <td class="tdk">Booking</td>
                  <td class="tdv">{{ $datos['booking'] ?? '-' }}</td>
                </tr>
              </table>

              <!-- Descripción del problema -->
              @if(!empty($datos['description']))
                <div class="p-16" style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;margin:8px 0;">
                  <strong style="display:block;color:#991b1b;margin-bottom:4px;">Descripción del problema</strong>
                  <div style="color:#7f1d1d;font-size:14px;line-height:1.5;">
                    {{ $datos['description'] }}
                  </div>
                </div>
              @endif

              </table>

              <hr class="hr"/>

              <!-- Pie de status -->
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
