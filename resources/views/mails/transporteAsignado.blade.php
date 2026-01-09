<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Asignación de transporte</title>

  <!-- Preheader (oculto) -->

  <style>
    body{margin:0;padding:0;background:#F5F5F5;-webkit-text-size-adjust:none;text-size-adjust:none}
    table{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%}
    img{border:0;display:block;max-width:100%;height:auto;margin:0 auto}
    a{text-decoration:none;color:inherit}
    .container{max-width:650px;margin:0 auto;background:#FFFFFF}
    .p-24{padding:24px}
    .p-16{padding:16px}
    .center{text-align:center}
    .h1{font-size:22px;line-height:1.3;color:#111827;margin:8px 0}
    .h2{font-size:18px;line-height:1.35;color:#374151;margin:0 0 12px}
    .lead{font-size:14px;line-height:1.6;color:#374151;margin:0 0 12px}
    .muted{color:#6b7280;font-size:12px}
    .hr{height:1px;background:#E5E7EB;border:0;margin:16px 0}
    .table{width:100%}
    .tdk{width:38%;vertical-align:top;padding:8px 10px;background:#FFFFFF}
    .tdv{width:62%;vertical-align:top;padding:8px 10px;background:#FFFFFF}
    .row-alt .tdk,.row-alt .tdv{background:#FAFAFA}
    @media (max-width:670px){
      .p-24{padding:16px}
      .h1{font-size:20px}
      .h2{font-size:16px}
    }
  </style>
</head>
<body style="background:#F5F5F5;margin:0;">
 
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
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
            <div class="tag">ASIGNACIÓN DE TRANSPORTE</div>
                       
          </td>
        </tr>
      </table>
    </td>
  </tr>

    <!-- Título Detalle -->
    <tr>
      <td>
        <table role="presentation" class="container" cellpadding="0" cellspacing="0">
          <tr>
            <td class="p-24 center">
              <p class="h2" style="margin:0;">Detalle de asignación:</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Detalle -->
    <tr>
      <td>
        <table role="presentation" class="container" cellpadding="0" cellspacing="0" style="background:#F0F0F0;">
          <tr>
            <td style="height:18px;background:#FFFFFF;"></td>
          </tr>
          <tr>
            <td style="padding:0 25px;background:#FFFFFF;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="width:25px;background:#FFFFFF;">&nbsp;</td>
                  <td style="padding:0 10px;width:600px;">
                    <!-- Contenedor / Booking -->
                    <table role="presentation" class="table" cellpadding="0" cellspacing="0" style="margin-top:20px;">
                      <tr>
                        <td class="tdk">Contenedor</td>
                        <td class="tdv" style="color:#3116a9;font-weight:700;">
                          @if($datos['confirmacion'] != 0)
                            {{ $datos['cntr_number'] }}
                          @else
                            SIN CONFIRMAR
                          @endif
                        </td>
                      </tr>
                      <tr class="row-alt">
                        <td class="tdk">Booking</td>
                        <td class="tdv">{{ $datos['booking'] }}</td>
                      </tr>
                    </table>

                    <hr class="hr"/>

                    <!-- Datos del transporte -->
                    <table role="presentation" class="table" cellpadding="0" cellspacing="0">
                      <tr>
                        <td class="tdk">Transporte</td>
                        <td class="tdv">{{ $datos['transport'] }}</td>
                      </tr>
                      <tr class="row-alt">
                        <td class="tdk">Bandera</td>
                        <td class="tdv">{{ $datos['transport_bandera'] }}</td>
                      </tr>
                      @if($datos['transport_agent'] == 'no' || $datos['transport_agent'] == null)
                        <tr>
                          <td class="tdk">ATA</td>
                          <td class="tdv">A CONFIRMAR</td>
                        </tr>
                      @else
                        <tr>
                          <td class="tdk">ATA</td>
                          <td class="tdv">{{ $datos['transport_agent'] }}</td>
                        </tr>
                        <tr class="row-alt">
                          <td class="tdk">CUIT ATA</td>
                          <td class="tdv">{{ $datos['cuit_ata'] }}</td>
                        </tr>
                      @endif
                    </table>

                    <hr class="hr"/>

                    <!-- Firma -->
                    <p class="center" style="margin:10px 0 24px;">
                      <span class="muted">Status Informado por:</span><br/>
                      <span style="font-size:15px;color:#2190e3;"><strong>{{ $datos['user'] }}</strong></span><br/>
                      <span class="muted">Created at: {{ $date }}</span>
                    </p>
                  </td>
                  <td style="width:25px;background:#FFFFFF;">&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="height:18px;background:#FFFFFF;"></td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td>
        <table role="presentation" class="container" cellpadding="0" cellspacing="0">
          <tr>
            <td class="p-16 center">
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
