<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Itinerario</title>
</head>
<body>
    <h1>Formulario de Itinerario</h1>
    <form action="/itinerarios" method="post">
        @csrf
        <label for="unidad_asignada">Unidad Asignada:</label><br>
        <input type="text" id="unidad_asignada" name="unidad_asignada"><br><br>
        
        <label for="carga_id">ID de Carga:</label><br>
        <input type="text" id="carga_id" name="carga_id"><br><br>
        
        <label for="descarga_id">ID de Descarga:</label><br>
        <input type="text" id="descarga_id" name="descarga_id"><br><br>
        
        <label for="trip_id">Trip ID:</label><br>
        <input type="text" id="trip_id" name="trip_id"><br><br>
        
        <label for="user">Usuario:</label><br>
        <input type="text" id="user" name="user"><br><br>
        
        <!-- Puntos de Interés -->
        <h2>Puntos de Interés</h2>
        <div id="puntos-de-interes">
            <!-- Aquí se agregarán los campos para los puntos de interés mediante JavaScript -->
        </div>
        <button type="button" onclick="agregarPuntoDeInteres()">Agregar Punto de Interés</button><br><br>

        <button type="submit">Guardar Itinerario</button>
    </form>

    <script>
        let puntoDeInteresCount = 0;

        function agregarPuntoDeInteres() {
            puntoDeInteresCount++;
            const container = document.getElementById('puntos-de-interes');

            const div = document.createElement('div');
            div.innerHTML = `
                <label for="descripcion${puntoDeInteresCount}">Descripción:</label><br>
                <input type="text" id="descripcion${puntoDeInteresCount}" name="puntos_de_interes[${puntoDeInteresCount}][descripcion]"><br><br>
                
                <label for="latitud${puntoDeInteresCount}">Latitud:</label><br>
                <input type="text" id="latitud${puntoDeInteresCount}" name="puntos_de_interes[${puntoDeInteresCount}][latitud]"><br><br>
                
                <label for="longitud${puntoDeInteresCount}">Longitud:</label><br>
                <input type="text" id="longitud${puntoDeInteresCount}" name="puntos_de_interes[${puntoDeInteresCount}][longitud]"><br><br>
                
                <label for="rango${puntoDeInteresCount}">Rango:</label><br>
                <input type="text" id="rango${puntoDeInteresCount}" name="puntos_de_interes[${puntoDeInteresCount}][rango]"><br><br>
                
                <label for="accion_mail${puntoDeInteresCount}">Acción Mail:</label><br>
                <input type="text" id="accion_mail${puntoDeInteresCount}" name="puntos_de_interes[${puntoDeInteresCount}][accion_mail]"><br><br>
                
                <label for="accion_notificacion${puntoDeInteresCount}">Acción Notificación:</label><br>
                <input type="text" id="accion_notificacion${puntoDeInteresCount}" name="puntos_de_interes[${puntoDeInteresCount}][accion_notificacion]"><br><br>
                
                <label for="accion_status${puntoDeInteresCount}">Acción Status:</label><br>
                <input type="text" id="accion_status${puntoDeInteresCount}" name="puntos_de_interes[${puntoDeInteresCount}][accion_status]"><br><br>
                
                <label for="user${puntoDeInteresCount}">Usuario:</label><br>
                <input type="text" id="user${puntoDeInteresCount}" name="puntos_de_interes[${puntoDeInteresCount}][user]"><br><br>
            `;
            container.appendChild(div);
        }
    </script>
</body>
</html>
