<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Instructivo de carga</title>
  <!-- Preheader para Gmail/Apple Mail -->
  <meta name="x-preheader" content="Nuevo instructivo de carga y detalles de la asignación"/>

  <style>
    /* Estilos mínimos y seguros */
    body{margin:0;padding:0;background:#F5F5F5;}
    table{border-collapse:collapse;}
    img{border:0;display:block;margin:0 auto;max-width:100%;height:auto;} /* imágenes centradas */
    a{text-decoration:none;color:inherit;}
    .wrapper{width:100%;background:#F5F5F5;}
    .container{width:100%;max-width:650px;margin:0 auto;background:#FFFFFF;}
    .p-24{padding:24px;}
    .p-16{padding:16px;}
    .center{text-align:center;}
    .h1{font-size:20px;line-height:1.3;color:#1f2937;margin:8px 0;}
    .lead{font-size:14px;line-height:1.6;color:#374151;margin:0 0 12px;}
    .kv{font-size:14px;color:#111827;margin:6px 0;}
    .kv span{color:#374151;}
    .tag{display:inline-block;background:#E5F2FA;color:#0F5E82;font-size:12px;padding:4px 8px;border-radius:4px;}
    .muted{color:#6b7280;font-size:12px;}
    .hr{height:1px;background:#E5E7EB;border:0;margin:16px 0;}
    .footer{font-size:12px;color:#9CA3AF;}
    @media (max-width:670px){.p-24{padding:16px}.h1{font-size:18px}}
  </style>
</head>
<body>
  <table class="wrapper" role="presentation" width="100%" cellpadding="0" cellspacing="0">
    <tr><td style="height:24px;"></td></tr>

    <!-- Header / Logo -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="center p-24">
              <img src="https://btz.ar/ttg_mails.svg" alt="BOTzero" width="240"/>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Título -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="center p-16">
              <div class="tag">NUEVO INSTRUCTIVO DE CARGA</div>
              <h1 class="h1">Detalle de la asignación</h1>
              <p class="muted" style="margin:0;">Creado el {{ $datos['date'] ?? '-' }}</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Resumen principal -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="p-24">
              <p class="lead center" style="margin-top:0;">
                @if (!empty($datos['confirmacion']) && $datos['confirmacion'] != 0)
                  CNTR: <strong>{{ $datos['cntr_number'] ?? '-' }}</strong>
                @else
                  <strong>SIN CONFIRMAR</strong>
                @endif — {{ $datos['cntr_type'] ?? '-' }}
              </p>

              <hr class="hr"/>

              <p class="kv"><strong>ID de Asignación:</strong> <span>{{ $datos['id_asign'] ?? '-' }}</span></p>
              <p class="kv"><strong>Booking:</strong> <span>{{ $datos['booking'] ?? '-' }}</span></p>
              <p class="kv"><strong>Commodity:</strong> <span>{{ $datos['commodity'] ?? '-' }}</span></p>
              <p class="kv"><strong>Lugar de carga:</strong> <span>{{ $datos['load_place'] ?? '-' }}</span></p>
              <p class="kv"><strong>Día de carga:</strong> <span>{{ $datos['load_date'] ?? '-' }}</span></p>
              <p class="kv"><strong>Shipper:</strong> <span>{{ $datos['shipper'] ?? '-' }}</span></p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Footer / Marca -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="center p-16">
              <p class="muted" style="margin:0 0 8px;">
                Tecnología programada por <strong>Rail|</strong> para <strong>{{ $datos['cliente'] ?? 'nuestros clientes' }}</strong>
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
