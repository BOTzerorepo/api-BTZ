<table width="100%" border="0" cellpadding="1" cellspacing="0" bordercolor="#999999" class="tablaContenido">
    <tr>
        <td width="50%" valign="top" class="tdContenido" style="border:none; text-align:left;">
            @if ($sandbox === 0)
                <img src="https://totaltrade.botzero.tech/images/ttl/TTchica.png" style="width:10rem;height 8rem;" alt="">
            @else
                <img src="https://demo.botzero.tech/images/whiteLabel.png" style="width:10rem;height 8rem;" alt="">
            @endif
        </td>
        <td width="50%" valign="top" class="tdContenido" style="border:none; text-align:right;">
            <img src="{{ $img }}" style="width:10rem; height 8rem;" alt="">
        </td>
    </tr>
</table>
<h3 style="text-align:left; margin-bottom: 0; font-family: sans-serif; text-transform: uppercase;" >Orden de Trabajo N° {{ $id_asign }}</h3>
<h5 style="text-align:left;margin: 0; font-family: sans-serif;text-transform: uppercase;">PUESTA FOB</h5>
<table width="100%" style="border: none;font-family: sans-serif; text-transform: uppercase;" cellspacing="0" bordercolor="#999999" class="tablaContenido">
    <tr>
        <td align="left" class="tdContenidoItem" ><strong>Facturar: {{ $title }} - CUIT: {{$cuit}}</strong></td>
    </tr>   
</table>
<table style="font-family: sans-serif;text-transform: uppercase; font-size: small;" width="100%" border="0"  cellspacing="0" bordercolor="#999999" class="tablaContenido">
    <tr>
        <td colspan="5" align="center" width="25%" bgcolor="#a9c8e4" style="border: none;" class="tdContenido">DATOS DE REFERENCIA</td>
    </tr>
    <tr>
        <td align="left" colspan="1" ><strong>Operación N°: </strong></td>
        <td colspan="4" class="tdContenido">{{ $ref_customer }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Cant y Tipo CNTR: </strong></td>
        <td colspan="4" width="25%" class="tdContenido">1 X {{$cntr_type}}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Peso por CNTR: </strong></td>
        <td colspan="4" width="25%" class="tdContenido">{{$net_weight}}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Mercaderia:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $commodity }}</td>
    </tr>
    @if($ex_alto != null)
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Extramedida Alto:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $ex_alto }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Extramedida Ancho:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $ex_ancho }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Extramedida Largo:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $ex_largo }}</td>
    </tr>
    @endif
    @if($obs_imo != null)
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>IMO Observaciones:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $obs_imo }}</td>
    </tr>
    @endif
    @if($rf_tem != null)
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Refrigerada Temperatura:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $rf_tem }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Refrigerada Humedad:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $rf_humedad }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Refrigerada Ventilación:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $rf_venti }}</td>
    </tr>
    @endif
    <tr>
        <td colspan="5" align="center" width="25%" bgcolor="#a9c8e4" style="border: none;" class="tdContenido">DESCRIPCIÓN DE LA OPERACION:</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Depósito de Retiro:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $retiro_place }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>FECHA DE CARGA:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $load_date }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Shipper:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $shipper }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Lugar de Carga:</strong></td>
        <td colspan="4"  class="tdContenido"><a href="{{ $link_maps }}">
            {{$load_place}} [ {{ $address }} - {{ $city }} ]</small></a></td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Lugar de Aduana:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $custom_place }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Despachante:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $custom_agent }} [ {{ $custom_agent_mail }} - {{ $custom_agent_phone }}]</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Tara:</strong></td>
        <td colspan="4" class="tdContenido">
            @if ($tara === 'tf')
                TARA FISCAL - {{ $tara_string }}
            @else
                {{ $tara }} - {{ $tara_string }}
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="5" align="center" width="25%" bgcolor="#a9c8e4" style="border: none;" class="tdContenido">INGRESO A PUERTO:</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Armador:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $oceans_line}}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Vessel - Voy:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $vessel}} - {{ $voyage}}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Stacking</strong></td>
        <td colspan="4"  class="tdContenido">{{ $cut_off_fis }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Puerto de Embarque</strong></td>
        <td colspan="4"  class="tdContenido">{{ $unload_place }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Destino Final</strong></td>
        <td colspan="4"  class="tdContenido">{{ $final_point }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>Agencia de Ingreso</strong></td>
        <td colspan="4"  class="tdContenido">{{ $agent_port }}</td>
    </tr>
    <tr>
        <td colspan="5" align="center" width="25%" bgcolor="#a9c8e4" style="border: none;" class="tdContenido">PROVEEDOR TERRESTRE:</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>TRANSPORTE:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $transport }} [ {{ $transport_agent }} ]</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>CONTENEDOR N°:</strong></td>
        @if($confirmacion == 1 ) 
        <td colspan="4"  class="tdContenido">
            {{ $cntr_number }}
        </td>
        @else
        <td colspan="4"  class="tdContenido">
           SIN CONFIRMAR
        </td>
        @endif
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>PRECINTO:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $cntr_seal }}</td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>VALOR POR CNTR:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $out_usd }} - {{ $observation_out }} </td>
    </tr>
    <tr>
        <td align="left" width="25%" class="tdContenidoItem"><strong>OBSERVACIONES:</strong></td>
        <td colspan="4"  class="tdContenido">{{ $observation_customer }} <br> {{ $observation_load}}</td>
    </tr>
</table>
<hr>
<p style="text-align:left;  color:red; font-family: sans-serif;text-transform: uppercase;">
    ENVIAR FACTURA DENTRO DE LOS 7 DÍAS DE HABER FINALIZADO EL SERVICIO. 
    <br>LA FACTURA NO SE CANCELARÁ SI NO SE ADJUNTA ESTA INSTRUCIÓN + MIC/DTA Y CRT + INTERCHANGE. 
</p>
<hr>
<p style="text-align:left; margin-button:0; font-family: sans-serif;text-transform: uppercase;">
Oblicación contar con SATELITAL de unidad y con elementos de seguridad:
<br> CASCO - ZAPATOS DE SEGURIDAD Y CHALECO REFLACTARIO.
</p>
<hr>
<p style="text-align:left; font-family: sans-serif;"> {!! $observaciones_agencia !!}</p>
<h6    style="text-align: center; font-family: sans-serif;"> <small> Tecnología programada por </small><a href="https://rail.ar" target="_blank">RailCode</a><small> para <a href="https://botzero.tech" target="_blank">BOTZero :: Software de Logística.</a></small> </h6>

