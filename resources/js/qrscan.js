import { Html5Qrcode } from "html5-qrcode";

const qrRegionId = "reader";

if (document.getElementById(qrRegionId)) {
    const html5QrCode = new Html5Qrcode(qrRegionId);

    // Función para ajustar el tamaño del cuadro QR según la ventana
    function getResponsiveQrbox() {
        const width = window.innerWidth;
        let size = 250; // tamaño por defecto para móviles
        if (width >= 768) size = 350; // tablets
        if (width >= 1024) size = 450; // desktop
        return { width: size, height: size };
    }

    const config = {
        fps: 10,
        qrbox: getResponsiveQrbox()
    };


    async function onScanSuccess(decodedText, decodedResult) {
        html5QrCode.pause(true); // Pausa temporalmente el escaneo

        const resultElement = document.getElementById("result");
        const pResult = resultElement.querySelector('p');
        const nombreElement = document.getElementById("nombre-empleado");
        const fotoElement = document.getElementById("foto-empleado");

        // Guarda los valores originales para poder restaurarlos después
        const textoOriginal = pResult.innerText;
        const nombreOriginal = nombreElement.innerText;
        const fotoOriginal = fotoElement.src;

        // resultElement.innerText = "Buscando empleado con ID: " + decodedText;

        try {
            const response = await fetch(`/empleado/${decodedText}/buscar`);
            if (!response.ok) {
                throw new Error("Empleado no encontrado");
            }

            const data = await response.json();

            // data.empleado y data.asistencia vienen del backend
            const empleado = data.empleado;
            const asistencia = data.asistencia;

            const saludo = obtenerSaludoPorHora();

            let saludoColor = saludo.includes('Bienvenido') ? 'text-green-600' :
                saludo.includes('Hasta pronto') ? 'text-yellow-600' :
                    'text-blue-600';


            nombreElement.innerHTML = `<span class="${saludoColor} font-bold">${saludo}</span><br>${empleado.nombres} ${empleado.apellido_paterno} ${empleado.apellido_materno}`;
            // nombreElement.innerHTML = `<span class="text-green-600 font-bold">${saludo}</span><br>${empleado.nombres} ${empleado.apellido_paterno} ${empleado.apellido_materno}`;

            if (empleado.foto) {
                fotoElement.src = `/img/empleados/${empleado.foto}`;
            } else {
                fotoElement.src = `/img/escudo-gris.png`; // Imagen por defecto
            }

            // resultElement.innerText = `Empleado ${empleado.nombres} encontrado`;
            // Mostrar mensaje de asistencia (entrada/salida/mensajes)
            pResult.innerText = asistencia.message;
            pResult.style.color = asistencia.success ? "white" : "white";
            resultElement.style.backgroundColor = asistencia.success ? "green" : "red";

        } catch (error) {
            pResult.innerText = error.message;
            resultElement.style.backgroundColor = "red";
            pResult.style.color = "white";
            nombreElement.innerText = "No identificado";
            fotoElement.src = `/img/escudo-gris.png`;
        } finally {
            setTimeout(() => {
                pResult.innerText = textoOriginal;
                pResult.style.color = "black";
                nombreElement.innerText = nombreOriginal;
                fotoElement.src = fotoOriginal;
                html5QrCode.resume(); // Reanuda escaneo después de 3 seg
            }, 2500);
        }
    }

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            let cameraId = devices[0].id;
            html5QrCode.start(cameraId, config, onScanSuccess);
        }
    }).catch(err => {
        console.error("No se pudo acceder a la cámara:", err);
    });
}


function obtenerSaludoPorHora() {
    const ahora = new Date();
    const hora = ahora.getHours();

    if (hora < 10) {
        return 'Bienvenido';
    } else if (hora >= 15) {
        return 'Hasta pronto';
    } else {
        return 'Hola';
    }
}



