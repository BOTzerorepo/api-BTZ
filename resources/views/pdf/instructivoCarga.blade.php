<table width="100%" border="0" cellpadding="1" cellspacing="0" bordercolor="#999999" class="tablaContenido">
    <tr>
        <td width="50%" valign="top" class="tdContenido" style="border:none; text-align:center;">
            <img src="https://totaltrade.botzero.tech/images/ttl/TTchica.png" style="width:10rem;" alt="">
        </td>
    </tr>
</table>
<h3 style="text-align:center; margin-button: 2%;">Instructivo de Carga N° {{ $id_asign }}</h3>
<table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#999999" class="tablaContenido">
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Booking: </strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $booking }}</td>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Mercaderia:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $commodity }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Dador de Carga:</strong></td>
        <td colspan="2" bgcolor="#E9E9E9" class="tdContenido">{{ $shipper }}</td>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Peso Neto</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $net_weight }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Numero de Contenedor:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $cntr_number }}</td>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Precinto:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $cntr_seal }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Lugar de Carga:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $load_place }}</td>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Fecha de Carga:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $load_date }}</td>
    </tr>
    <tr>
        <td align="left" class="tdContenidoItem"><strong>Dirección:</strong></td>
        <td colspan="4" bgcolor="#E9E9E9" class="tdContenido"><a href="{{ $link_maps }}">
                {{ $address }} - {{ $city }}</small></a></td>
    </tr>
    <br>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Lugar de Aduana:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $custom_place }}</td>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Despachante:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $custom_agent }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Lugar de Descarga:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $unload_place }}</td>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Cut Off Físico:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $cut_off_fis }}</td>
    </tr>
</table>
<h4 style="text-align:left; margin-button: 2%; margin-top:2%"> Otros datos para el despacho:</h4>
<table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#999999" class="tablaContenido">
    <tr>
        <ul>
            <li>Vessel: {{ $vessel }} - {{ $voyage }} -{{ $oceans_line }}</li>
            <li>Destino Final: {{ $final_point }} </li>
        </ul>
    </tr>
</table>
<h4 style="text-align:left; margin-button: 2%; margin-top:2%">Datos para el transporte:</h4>
<table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#999999" class="tablaContenido">
    <tr>
        <ul>
            <li>Transporte: {{ $transport }}</li>
            <li>ATA: {{ $transport_agent }} </li>
            <li>Agencia de Ingreso: {{ $agent_port }} </li>
            <li>Observaciones: {{ $observation_load }} </li>
        </ul>
    </tr>
</table>
<table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#999999" class="tablaContenido">
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Tarifa:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $out_usd }}</td>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Modo de Pago:</strong></td>
        <td colspan="2" width="25%" bgcolor="#E9E9E9" class="tdContenido">{{ $modo_pago_out }}
            {{ $plazo_de_pago_out }}</td>
    </tr>
</table>
<p style="font-family: sans-serif; text-align:center;"> Adjuntar este instructivo a la factura. </p>
<br><br>
<hr style="color:black; width:80%;">
<h6> <small> Tecnología programada por </small><a href="https://rail.ar" target="_blank">RailCode</a><small> para <a href="https://botzero.tech" target="_blank">BOTZero :: Software de Logística.</a></small> </h6>

