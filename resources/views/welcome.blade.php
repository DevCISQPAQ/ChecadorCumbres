<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checador Cumbres</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="shortcut icon" type="image/svg" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link rel="shortcut icon" sizes="192x192" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>

<body>
    <header>
        <div class="banner">
            <img src="{{ asset('img/escuworblan.png') }}" alt="Logo" class="h-auto w-64">
        </div>
    </header>

    <main>
        <div class="py-10">
            <h1 class="titleHome">Registra tu asistencia</h1>
        </div>
    </main>

    <section>
        <div class="flex gap-x-3 pt-7">
            <div class="border-4 border-[#FF6C37] ml-4 p-4 w-1/2 ">
                <div id="reader"></div>
                <p id="result" class="text-center mt-4 font-bold text-lg text-gray-700">Esperando escaneo...</p>
            </div>
            <div class="border-4 border-[#7A00BF] mr-4 p-4 w-1/2">
                Div 2
            </div>
        </div>
    </section>



    <!-- <script>
        const html5QrCode = new Html5Qrcode("reader");

        function onScanSuccess(decodedText, decodedResult) {
            console.log("✅ QR detectado:", decodedText); // <--- Aquí se imprime en consola
            document.getElementById("result").innerText = "QR: " + decodedText;
            html5QrCode.stop(); // Detenemos la cámara después del escaneo
        }

        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                let cameraId = devices[0].id;
                html5QrCode.start(
                    cameraId, {
                        fps: 10,
                        qrbox: 250
                    },
                    onScanSuccess,
                    errorMessage => {
                        // Mostramos errores silenciosos de escaneo
                        // console.warn("No se detectó QR:", errorMessage);
                    }
                );
            } else {
                alert("No se encontró ninguna cámara.");
            }
        }).catch(err => {
            console.error("Error al acceder a la cámara:", err);
        });
    </script> -->


    <script>
        const qrRegionId = "reader";
        const html5QrCode = new Html5Qrcode(qrRegionId);

        // Configura el tamaño del recuadro del QR
        const config = {
            fps: 10,
            qrbox: {
                width: 450,
                height: 450
            } // tamaño del cuadro de escaneo
        };

        function onScanSuccess(decodedText, decodedResult) {
            // Mostrar el resultado
            document.getElementById("result").innerText = "Resultado: " + decodedText;

            // Opcional: detener la cámara después de leer
            // html5QrCode.stop().then(() => {
            //     console.log("Cámara detenida");
            // }).catch(err => {
            //     console.error("Error al detener cámara:", err);
            // });
        }

        // Iniciar escaneo
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                let cameraId = devices[0].id;
                html5QrCode.start(cameraId, config, onScanSuccess);
            }
        }).catch(err => {
            console.error("No se pudo acceder a la cámara:", err);
        });
    </script>

</body>

</html>