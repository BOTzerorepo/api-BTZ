<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitud de Seguro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="col-sm-10 mx-auto">
            <div class="card mb-3 mt-5">
                <img src="https://botzero.ar/public/image/server.svg" class="card-img-top p-5" alt="...">
                <div class="card-body">
                    <h1 class="card-title text-center text-dark">Solicitud de Seguro de Carga</h1>
                    <p class="card-text text-center text-secondary">Completar los datos del siguiente formulario para
                        solicitar seguro.</p>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-sm-8 mx-auto mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Código Customer:</label>
                                <input type="text" class="form-control" id="customer" placeholder="AAAA001" {{-- required --}}>
                            <div id="divCustomer" style="display: none;">
                            <span class="form-text text-danger">
                                    El campo debe ser completado.
                                </span>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 mx-auto mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Beneficiario:</label>
                                <input type="text" class="form-control" id="beneficiario" placeholder="Empresa SA" {{-- required --}}>
                                <div id="divBeneficiario" style="display: none;">
                                    <span class="form-text text-danger">
                                            El campo debe ser completado.
                                        </span>
                                    </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 mx-auto mb-3">
                                <label for="exampleFormControlInput1" class="form-label">CUIT / RUT:</label>
                                <input type="number" class="form-control" id="tax_id" placeholder="30710000000" {{-- required --}}>
                                <span id="passwordHelpInline" class="form-text">
                                    Solo números, todo junto
                                </span>
                                <div id="divTaxId" style="display: none;">
                                    <span class="form-text text-danger" >
                                            El campo debe ser completado.
                                        </span>
                                    </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 mx-auto mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Commodity:</label>
                                <input type="text" class="form-control" id="commodity" placeholder="Vino en Pallet" {{-- required --}}>
                                <div id="divCommodity" style="display: none">
                                    <span class="form-text text-danger" >
                                            El campo debe ser completado.
                                        </span>
                                    </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 mx-auto mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Suma Asegurada:</label>
                                <input type="text" class="form-control" id="suma_asegurada" placeholder="78000" {{-- required --}}>
                                <div id="divSumaAsegurada" style="display: none">
                                    <span class="form-text text-danger" >
                                            El campo debe ser completado.
                                        </span>
                                    </div>
                                <span id="passwordHelpInline" class="form-text">
                                    Número sin comas ni puntos.
                                </span>
                            </div>
                        </div>
                        <hr class="mx-auto" style="width:70%;">
                        <div class="col-sm-8 mx-auto mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Transporte:</label>
                            <input type="text" class="form-control" id="transport"
                                placeholder="Transporte Virgen del Valle SA" {{-- required --}}>
                                <div id="divTransport"  style="display: none">
                                    <span class="form-text text-danger">
                                            El campo debe ser completado.
                                        </span>
                                    </div>
                        </div>
                        <div class="col-sm-8 mx-auto mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Chofer:</label>
                            <input type="text" class="form-control" id="driver" placeholder="Juan Perez" {{-- required --}}>
                            <div id="divDriver" style="display: none">
                                <span class="form-text text-danger" >
                                        El campo debe ser completado.
                                    </span>
                                </div>
                        </div>
                        <div class="col-sm-8 mx-auto mb-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="exampleFormControlInput1" class="form-label">DNI / RUT:</label>
                                    <input type="text" class="form-control" id="truck_document"
                                        placeholder="34999000" {{-- required --}}>
                                        <div id="divTruckDocument"  style="display: none">
                                            <span class="form-text text-danger">
                                                    El campo debe ser completado.
                                                </span>
                                            </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="exampleFormControlInput1" class="form-label">Celular:</label>
                                    <input type="text" class="form-control" id="driver_phone"
                                        placeholder="5492612000111" {{-- required --}}>
                                        <div id="divDriverPhone" style="display: none">
                                            <span class="form-text text-danger" >
                                                    El campo debe ser completado.
                                                </span>
                                            </div>
                                    <span id="passwordHelpInline" class="form-text">
                                        Números, todo junto. Indicar el codigo de país y área.
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8 mx-auto mb-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="exampleFormControlInput1" class="form-label">Patente Tractor:</label>
                                    <input type="text" class="form-control" id="truck_domain"
                                        placeholder="AA123WJ - AAA123" {{-- required --}}>
                                        <div id="divTruckDomain" style="display: none">
                                            <span class="form-text text-danger" >
                                                    El campo debe ser completado.
                                                </span>
                                            </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="exampleFormControlInput1" class="form-label">Patente Remolque:</label>
                                    <input type="text" class="form-control" id="truck_trailer"
                                        placeholder="AA123WJ - AAA123" {{-- required --}}>
                                        <div id="divTruckTrailer" style="display: none">
                                            <span class="form-text text-danger" >
                                                    El campo debe ser completado.
                                                </span>
                                            </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 mx-auto mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Satelital:</label>
                                <input type="text" class="form-control" id="truck_device" placeholder="Pressa" {{-- required --}}>
                                <div id="divTruckDevice" style="display: none">
                                    <span class="form-text text-danger" >
                                            El campo debe ser completado.
                                        </span>
                                    </div>
                                <span id="passwordHelpInline" class="form-text" style="display: none">
                                    En el caso de no contar con Satalital, colocar "no".
                                </span>
                            </div>
                        </div>
                        <hr class="mx-auto" style="width:70%;">
                        <div class="col-sm-8 mx-auto mb-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="exampleFormControlInput1" class="form-label">Origen:</label>
                                    <input type="text" class="form-control" id="load_place"
                                        placeholder="Mendoza, Argentina" {{-- required --}}>
                                        <div id="divLoadPlace" style="display: none">
                                            <span class="form-text text-danger" >
                                                    El campo debe ser completado.
                                                </span>
                                            </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="exampleFormControlInput1" class="form-label">Destino:</label>
                                    <input type="text" class="form-control" id="download_place"
                                        placeholder="Rio de Janeiro, Brazil" {{-- required --}}>
                                        <div id="divDownloadPlace" style="display: none">
                                            <span class="form-text text-danger" >
                                                    El campo debe ser completado.
                                                </span>
                                            </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8 mx-auto mb-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="exampleFormControlInput1" class="form-label">Fecha de Carga:</label>
                                    <input type="date" class="form-control" id="load_date" {{-- required --}}>
                                    <div id="divLoadDate" style="display: none">
                                        <span class="form-text text-danger" >
                                                El campo debe ser completado.
                                            </span>
                                        </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="exampleFormControlInput1" class="form-label">Documento de
                                        Referencia:</label>
                                    <input type="text" class="form-control" id="reference_doc"
                                        placeholder="FA0000-000001 | 9898766788 " {{-- required --}}>
                                        <div id="divReferenceDoc" style="display: none">
                                            <span class="form-text text-danger">
                                                    El campo debe ser completado.
                                                </span>
                                            </div>
                                </div>
                            </div>
                        </div>
                        <hr class="mx-auto" style="width:70%;">
                        <div class="row">
                            <div class="col-sm-8 mx-auto mb-3">
                                <label for="exampleFormControlInput1" class="form-label">E-mail donde recibir
                                    póliza:</label>
                                <input type="text" class="form-control" id="email" placeholder="juan@perez.com" {{-- required --}}>
                                <div id="divEmail"  style="display: none">
                                    <span class="form-text text-danger">
                                            El campo debe ser completado.
                                        </span>
                                    </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 mx-auto mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">Observaciones:</label>
                                <textarea class="form-control" id="observation_customer" rows="3" placeholder="Observaciones"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 mx-auto mb-3 text-center">
                                <button type="button" id="btn" class="btn btn-primary mx-auto"
                                    onclick="enviarSeguro()">Enviar Solicitud</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function enviarSeguro() {

            
            document.getElementById("btn").disabled = true;
            
            var sx = 1; 
            var sy = 1; 
            var sz = 1; 
            var sa = 1; 
            var sb = 1; 
            var sc = 1; 
            var sd = 1; 
            var se = 1;
            var sf = 1;
            var sg = 1;
            var sh = 1;
            var si = 1;
            var sj = 1;
            var sk = 1;
            var sl = 1;
            var sm = 1;
            var sn = 1;            
            var customer = document.getElementById("customer").value;
            if(customer.length == 0){
                var x = document.getElementById("divCustomer");
                if (x.style.display === "none") {
                    x.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sx = 0;
                } else {
                    x.style.display = "none";
                }
            }
            var beneficiario = document.getElementById("beneficiario").value;
            if(beneficiario.length == 0){
                var y = document.getElementById("divBeneficiario");
                if (y.style.display === "none") {
                    y.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sy = 0;

                } else {
                    y.style.display = "none";
                }
            }
            var tax_id = document.getElementById("tax_id").value;
            if(tax_id.length == 0){
                var z = document.getElementById("divTaxId");
                if (z.style.display === "none") {
                    z.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sz = 0;

                } else {
                    z.style.display = "none";
                    
                }
            }
            var commodity = document.getElementById("commodity").value;
            if(commodity.length == 0){
                var a = document.getElementById("divCommodity");
                if (a.style.display === "none") {
                    a.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sa = 0;

                } else {
                    a.style.display = "none";
                }
            }
            var suma_asegurada = document.getElementById("suma_asegurada").value;
            if(suma_asegurada.length == 0){
                var b = document.getElementById("divSumaAsegurada");
                if (b.style.display === "none") {
                    b.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sb = 0;

                } else {
                    b.style.display = "none";
                }
            }
            var driver = document.getElementById("driver").value;
            if(driver.length == 0){
                var c = document.getElementById("divDriver");
                if (c.style.display === "none") {
                    c.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sc = 0;

                } else {
                    c.style.display = "none";
                }
            }
            var transport = document.getElementById("transport").value;
            if(transport.length == 0){
                var d = document.getElementById("divTransport");
                if (d.style.display === "none") {
                    d.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sd = 0;

                } else {
                    d.style.display = "none";
                }
            }
            var truck_domain = document.getElementById("truck_domain").value;
            if(truck_domain.length == 0){
                var e = document.getElementById("divTruckDomain");
                if (e.style.display === "none") {
                    e.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var se = 0;

                } else {
                    e.style.display = "none";
                }
            }
            var truck_trailer = document.getElementById("truck_trailer").value;
            if(truck_trailer.length == 0){
                var f = document.getElementById("divTruckTrailer");
                if (f.style.display === "none") {
                    f.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sf = 0;

                } else {
                    f.style.display = "none";
                }
            }
            var truck_device = document.getElementById("truck_device").value;
            if(truck_device.length == 0){
                var g = document.getElementById("divTruckDevice");
                if (g.style.display === "none") {
                    g.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sg = 0;

                } else {
                    g.style.display = "none";
                }
            }
            var truck_document = document.getElementById("truck_document").value;
            if(truck_document.length == 0){
                var h = document.getElementById("divTruckDocument");
                if (h.style.display === "none") {
                    h.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sh = 0;

                } else {
                    h.style.display = "none";
                }
            }
            var driver_phone = document.getElementById("driver_phone").value;
            if(driver_phone.length == 0){
                var i = document.getElementById("divDriverPhone");
                if (i.style.display === "none") {
                    i.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var si= 0;

                } else {
                    i.style.display = "none";
                }
            }
            var load_place = document.getElementById("load_place").value;
            if(load_place.length == 0){
                var j = document.getElementById("divLoadPlace");
                if (j.style.display === "none") {
                    j.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sj = 0;

                } else {
                    j.style.display = "none";
                }
            }
            var load_date = document.getElementById("load_date").value;
            if(load_date.length == 0){
                var k = document.getElementById("divLoadDate");
                if (k.style.display === "none") {
                    k.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sk = 0;

                } else {
                    k.style.display = "none";
                }
            }
            var download_place = document.getElementById("download_place").value;
            if(download_place.length == 0){
                var l = document.getElementById("divDownloadPlace");
                if (l.style.display === "none") {
                    l.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sl = 0;

                } else {
                    l.style.display = "none";
                }
            }
            var reference_doc = document.getElementById("reference_doc").value;
            if(reference_doc.length == 0){
                var m = document.getElementById("divReferenceDoc");
                if (m.style.display === "none") {
                    m.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sm = 0;

                } else {
                    m.style.display = "none";
                }
            }
            var email = document.getElementById("email").value;
            if(email.length == 0){
                var n = document.getElementById("divEmail");
                if (n.style.display === "none") {
                    n.style.display = "block";
                    document.getElementById("btn").disabled = false;
                    var sn = 0;

                } else {
                    n.style.display = "none";
                }
            }
            sumOK = sx + sy + sz + sa + sb + sc + sd + se+ sf+ sg+ sh+ si+ sj+ sk+ sl+ sm+ sn;
            console.log(sumOK)

            var observation_customer = document.getElementById("observation_customer").value;

            if( sumOK < 17 ){
                swal("Hay campos Incompletos", "Por favor revise y vuelva a enviar el formulario", "warning")
            }else{
            
            var formdata = new FormData();
            
            formdata.append("customer", customer);
            formdata.append("beneficiario", beneficiario);
            formdata.append("tax_id", tax_id);
            formdata.append("commodity", commodity);
            formdata.append("suma_asegurada", suma_asegurada);
            formdata.append("truck_transport", transport);
            formdata.append("truck_domain", truck_domain);
            formdata.append("truck_trailer", truck_trailer);
            formdata.append("truck_device", truck_device);
            formdata.append("load_place", load_place);
            formdata.append("download_place", download_place);
            formdata.append("reference_doc", reference_doc);
            formdata.append("load_date", load_date);
            formdata.append("truck_driver", driver);
            formdata.append("truck_document", truck_document);
            formdata.append("driver_phone", driver_phone);
            formdata.append("email", email);
            formdata.append("observation_customer", observation_customer);

            var requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'follow'
            };

            fetch("https://botzero.ar/api/seguro", requestOptions)
                .then(response => response.text())
                .then(reuslt => {
                    swal("Formulario enviado correctamente", "En istantes revisamos su solicitud y enviaremos novedades por correo!", "success")
                    
                        .then(() => {
                            window.location.reload();
                        })
                })
                .catch(err => {
                    if (err) {
                        swal("ERROR!", "No se envió el Formurmulario Correctamente!", "error");
                    } else {
                        swal.stopLoading();
                        swal.close();
                    }
                }); 

            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous">
    </script>
</body>

</html>
