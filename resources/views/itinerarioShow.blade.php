<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Carga y Puntos de Interés</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Detalle de Carga y Puntos de Interés</h1>
        <div class="row" id="detalle-container">
            <!-- Aquí se mostrarán los detalles -->
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" ></script>

   <script>
        $(document).ready(function() {
            $.ajax({
                url: 'http://127.0.0.1:8000/api/itinerarios/3', // Reemplaza 'TU_ENDPOINT' con la URL de tu endpoint que devuelve los datos JSON
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                   
                    // Construir el HTML con los datos recibidos
                    var html = '';

                    // Detalles generales
                    

                    var datosGenerales = data.datos_generales;
                        console.log("Datos generales:");
                        console.log(datosGenerales);

                    html += '<div class="col">';
                    html += '<div class="card">';
                    html += '<div class="card-header">Datos Generales</div>';
                    html += '<div class="card-body">';
                    html += '<p class="card-text">Unidad asignada: ' + datosGenerales.unidad_asignada + '</p>';
                    html += '<p class="card-text">Descripción de carga: ' + datosGenerales.ldc_desc + '</p>';
                    html += '<p class="card-text">Latitud de carga: ' + datosGenerales.ldc_lat + '</p>';
                    html += '<p class="card-text">Longitud de carga: ' + datosGenerales.ldc_lng + '</p>';
                    html += '<p class="card-text">Rango de carga: ' + datosGenerales.ldc_rango + '</p>';
                    html += '<p class="card-text">Descripción de descarga: ' + datosGenerales.ldu_desc + '</p>';
                    html += '<p class="card-text">Latitud de descarga: ' + datosGenerales.ldu_lat + '</p>';
                    html += '<p class="card-text">Longitud de descarga: ' + datosGenerales.ldu_lng + '</p>';
                    html += '<p class="card-text">Rango de descarga: ' + datosGenerales.ldu_rango + '</p>';
                    html += '</div></div></div>';

                    // Detalles
                    var detalles = data.detalles;
                    html += '<div class="col">';
                    detalles.forEach(function(detalle) {
                        html += '<div class="card">';
                        html += '<div class="card-header">' + detalle.tipo + '</div>';
                        html += '<div class="card-body">';
                        html += '<p class="card-text">Descripción: ' + detalle.descripcion + '</p>';
                        html += '<p class="card-text">Latitud: ' + detalle.latitud + '</p>';
                        html += '<p class="card-text">Longitud: ' + detalle.longitud + '</p>';
                        html += '<p class="card-text">Rango: ' + detalle.rango + '</p>';
                        if (detalle.accion_mail !== undefined) {
                            html += '<p class="card-text">Acción Mail: ' + detalle.accion_mail + '</p>';
                        }
                        if (detalle.accion_status !== undefined) {
                            html += '<p class="card-text">Acción Status: ' + detalle.accion_status + '</p>';
                        }
                        if (detalle.accion_notificacion !== undefined) {
                            html += '<p class="card-text">Acción Notificación: ' + detalle.accion_notificacion + '</p>';
                        }
                        html += '</div></div>';
                    });
                    html += '</div>';

                    // Insertar el HTML generado en el contenedor
                    $('#detalle-container').html(html);
                },
                error: function(xhr, status, error) {
                    // Manejar el error
                    console.error(error);
                }
            });
        });
    </script>
</body>
</html>
