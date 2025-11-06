<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Nueva Impo Terrestre</title>

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
             <td class="center p-16" style="background:#e0e0e0;">
              <div class="tag">Nueva Impo Terrestre</div>
              <h1 class="h1">Cliente / Trader</h1>
              <h2 class="h2"><strong>{{ $datos['trader'] ?? '-' }}</strong></h2>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Intro -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="p-24">
              <p class="lead">
                Equipo, muy buenos días. Considerar nueva carga para el cliente
                <strong>{{ $datos['trader'] ?? '-' }}</strong>. Datos a confirmar ya fueron solicitados al cliente; en breve volvemos con los mismos.
              </p>
              <p class="muted" style="margin:0;">Creado el {{ $datos['date'] ?? '-' }} — Status informado por <strong>{{ $datos['user'] ?? '-' }}</strong></p>
              <hr class="hr"/>

              <!-- Detalle -->
              <h2 class="h2" style="margin-top:0;">Detalle</h2>
              <table class="table" role="presentation" cellpadding="0" cellspacing="0">
                <tr><td class="tdk">Operación Nº</td><td class="tdv">{{ $datos['operacion'] ?? '-' }}</td></tr>
                <tr class="row-alt"><td class="tdk">Referencia</td><td class="tdv">{{ $datos['booking'] ?? '-' }}</td></tr>
                <tr><td class="tdk">Trader</td><td class="tdv">{{ $datos['trader'] ?? '-' }}</td></tr>
                <tr class="row-alt"><td class="tdk">Importador / Consignee</td><td class="tdv">{{ $datos['importador'] ?? '-' }}</td></tr>
                <tr><td class="tdk">Cantidad y Tipo de CNTR</td><td class="tdv">{{ $datos['cantidad'] ?? '-' }} x {{ $datos['cntr_type'] ?? '-' }}</td></tr>
                <tr class="row-alt"><td class="tdk">Mercadería</td><td class="tdv">{{ $datos['commodity'] ?? '-' }}</td></tr>
                <tr><td class="tdk">Senasa</td><td class="tdv">{{ $datos['senasa'] ?? '-' }} — {{ $datos['senasa_string'] ?? '-' }}</td></tr>
                <tr class="row-alt">
                  <td class="tdk">Tara</td>
                  <td class="tdv">
                    @if (($datos['tara'] ?? null) === 'tf')
                      TARA FISCAL — {{ $datos['tara_string'] ?? '-' }}
                    @else
                      {{ $datos['tara'] ?? '-' }} — {{ $datos['tara_string'] ?? '-' }}
                    @endif
                  </td>
                </tr>
                @if ($datos['cma_t_o'] != null)
                <tr> 
                  <td class="tdk">T/0:</td>
                  <td class="tdv">
                      {{ $datos['cma_t_o']}}
                  </td>
                </tr>
                    @endif
                <tr><td class="tdk">Lugar de Carga</td><td class="tdv">{{ $datos['loadPlace'] ?? '-' }}</td></tr>
                <tr class="row-alt"><td class="tdk">Día de Carga</td><td class="tdv">{{ $datos['loadDate'] ?? '-' }}</td></tr>
                <tr><td class="tdk">Lugar de Aduana Expo</td><td class="tdv">{{ $datos['customPlace'] ?? '-' }}</td></tr>
                <tr class="row-alt"><td class="tdk">Despachante Expo</td><td class="tdv">{{ $datos['customAgent'] ?? '-' }}</td></tr>
                <tr><td class="tdk">Lugar de Aduana Impo</td><td class="tdv">{{ $datos['customPlaceImpo'] ?? '-' }}</td></tr>
                <tr class="row-alt"><td class="tdk">Despachante Impo</td><td class="tdv">{{ $datos['customAgentImpo'] ?? '-' }}</td></tr>
                <tr><td class="tdk">Lugar de Entrega</td><td class="tdv">{{ $datos['loadPort'] ?? '-' }}</td></tr>
                <tr class="row-alt"><td class="tdk">Cut Off físico</td><td class="tdv">{{ $datos['cutOffFisico'] ?? '-' }}</td></tr>
                <tr><td class="tdk">Observaciones</td><td class="tdv">{{ $datos['obeservaciones'] ?? '-' }}</td></tr>
              </table>

              <hr class="hr"/>

              <!-- CTA opcional -->
              @if (!empty($datos['cta_url']))
              <table role="presentation" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="background:#2E7D32;border-radius:6px;">
                    <a href="{{ $datos['cta_url'] }}"
                       style="display:inline-block;padding:12px 18px;font-size:14px;color:#ffffff;">
                      {{ $datos['cta_text'] ?? 'Ver carga en el sistema' }}
                    </a>
                  </td>
                </tr>
              </table>
              @endif
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
