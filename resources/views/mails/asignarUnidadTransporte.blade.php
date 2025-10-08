<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Asignación de unidad provisoria</title>
  <meta name="x-preheader" content="Asignación de unidad provisoria — Detalles para {{ $datos['booking'] }}"/>

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
    .tdk{width:42%;vertical-align:top;padding:8px 10px;background:#FFFFFF}
    .tdv{width:58%;vertical-align:top;padding:8px 10px;background:#FFFFFF}
    .row-alt .tdk,.row-alt .tdv{background:#FAFAFA}
    .footer{font-size:12px;color:#9CA3AF}
    @media (max-width:670px){.p-24{padding:16px}.h1{font-size:20px}.h2{font-size:16px}}
  </style>
</head>
<body>
  <!-- Preheader oculto -->
  <span style="display:none !important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;overflow:hidden;">
    Asignación de unidad provisoria — Detalles para {{ $datos['booking'] }}
  </span>

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

    <!-- Hero unificado -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
             <td class="center p-16" style="background:#e0e0e0;">
              <div class="tag">Asignación de unidad provisoria</div>
             
              <h2 class="h2" style="margin-top:6px;">
                @if( $datos['confirmacion'] != 0) 
                  <strong>Carga {{ $datos['cntr_number'] }} para el {{ $datos['booking'] }}</strong>
                @else
                  <strong>Carga CNTR SIN CONFIRMAR para el {{ $datos['booking'] }}</strong>
                @endif
              </h2>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Detalle: Datos Provisorios -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="p-24">
              <h2 class="h2" style="margin-top:0;">Datos Provisorios</h2>
              <hr class="hr"/>
              <table class="table" role="presentation" cellpadding="0" cellspacing="0">
                <tr>         <td class="tdk">Transporte</td>                 <td class="tdv">{{ $datos['transport'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">Domicilio</td>           <td class="tdv">{{ $datos['direccion'] }}</td></tr>
                <tr>         <td class="tdk">RUT | CUIT</td>                  <td class="tdv">{{ $datos['cuit'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">PAUT</td>                 <td class="tdv">{{ $datos['paut'] }}</td></tr>
                <tr>         <td class="tdk">Permiso Internacional</td>       <td class="tdv">{{ $datos['permiso_int'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">Vto. Permiso Internacional</td><td class="tdv">{{ $datos['vto_permiso_int'] }}</td></tr>
                <tr>         <td class="tdk"><strong>CRT</strong></td>        <td class="tdv"><strong>{{ $datos['crt'] }}</strong></td></tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Detalle: Datos para MIC Provisorios -->
    <tr>
      <td>
        <table role="presentation" class="container" width="100%">
          <tr>
            <td class="p-24">
              <h2 class="h2" style="margin-top:0;">Datos para MIC Provisorios</h2>
              <hr class="hr"/>
              <table class="table" role="presentation" cellpadding="0" cellspacing="0">
                <tr>         <td class="tdk">Transporte</td>                  <td class="tdv">{{ $datos['fletero_razon_social'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">Domicilio</td>            <td class="tdv">{{ $datos['fletero_domicilio'] }}</td></tr>
                <tr>         <td class="tdk">CUIT</td>                         <td class="tdv">{{ $datos['fletero_cuit'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">PAUT</td>                  <td class="tdv">{{ $datos['fletero_paut'] }}</td></tr>
                <tr>         <td class="tdk">Permiso Internacional</td>        <td class="tdv">{{ $datos['fletero_permiso'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">Vencimiento Perm. Internacional</td><td class="tdv">{{ $datos['fletero_vto_permiso'] }}</td></tr>

                <tr>         <td class="tdk">Chofer</td>                       <td class="tdv">{{ $datos['driver'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">DNI</td>                   <td class="tdv">{{ $datos['documento'] }}</td></tr>

                <tr>         <td class="tdk">Tractor</td>                      <td class="tdv">{{ $datos['truck'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">Modelo</td>                <td class="tdv">{{ $datos['truck_modelo'] }}</td></tr>
                <tr>         <td class="tdk">Año</td>                          <td class="tdv">{{ $datos['truck_year'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">Chasis</td>                <td class="tdv">{{ $datos['truck_chasis'] }}</td></tr>
                <tr>         <td class="tdk">Póliza</td>                       <td class="tdv">{{ $datos['truck_poliza'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">Vencimiento póliza</td>    <td class="tdv">{{ $datos['truck_vto_poliza'] }}</td></tr>

                <tr>         <td class="tdk">Semi</td>                         <td class="tdv">{{ $datos['truck_semi'] }}</td></tr>
                <tr class="row-alt"><td class="tdk">Póliza del semi</td>       <td class="tdv">{{ $datos['truck_semi_poliza'] }}</td></tr>
                <tr>         <td class="tdk">Vto. póliza del semi</td>         <td class="tdv">{{ $datos['truck_semi_vto_poliza'] }}</td></tr>
                <tr>         <td class="tdk">Genset</td>         <td class="tdv">{{ $datos['truck_semi_genset'] }}</td></tr>

              </table>

              <hr class="hr"/>
              <p class="muted" style="margin:0;text-align:center;">
                Status informado por <strong>{{ $datos['user'] }}</strong> — Creado el {{ $date }}
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
