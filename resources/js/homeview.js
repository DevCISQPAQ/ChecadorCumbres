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
        const nombreElement = document.getElementById("nombre-empleado");
        const fotoElement = document.getElementById("foto-empleado");

        // Guarda los valores originales para poder restaurarlos después
        const textoOriginal = resultElement.innerText;
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


            // Mostrar datos del empleado
            nombreElement.innerText = `${empleado.nombres} ${empleado.apellido_paterno} ${empleado.apellido_materno}`;

            if (empleado.foto) {
                fotoElement.src = `/img/empleados/${empleado.foto}`;
            } else {
                fotoElement.src = `/img/escudo-gris.png`; // Imagen por defecto
            }

            // resultElement.innerText = `Empleado ${empleado.nombres} encontrado`;
            // Mostrar mensaje de asistencia (entrada/salida/mensajes)
            resultElement.innerText = asistencia.message;
            resultElement.style.color = asistencia.success ? "green" : "red";

        } catch (error) {
            resultElement.innerText = error.message;
            resultElement.style.color = "red";
            nombreElement.innerText = "No identificado";
            fotoElement.src = `/img/escudo-gris.png`;
        } finally {
            setTimeout(() => {
                resultElement.innerText = textoOriginal;
                 resultElement.style.color = "black";
                nombreElement.innerText = nombreOriginal;
                fotoElement.src = fotoOriginal;
                html5QrCode.resume(); // Reanuda escaneo después de 3 seg
            }, 3000);
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



function updateDateTime() {
    const now = new Date();

    // Opciones para fecha completa
    const dateOptions = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    // Opciones solo para hora
    const timeOptions = {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false  // Nota: es hour12, no hour24
    };

    // Formatear fecha y hora
    const formattedDate = now.toLocaleDateString('es-ES', dateOptions);
    const formattedTime = now.toLocaleTimeString('es-ES', timeOptions);

    const datetimeEl = document.getElementById('datetime');
    const timeonlyEl = document.getElementById('timeonly');

    if (datetimeEl) {
        datetimeEl.innerText = formattedDate;
    }

    if (timeonlyEl) {
        timeonlyEl.innerText = formattedTime;
    }
}

// Actualiza la fecha y hora cada segundo
setInterval(updateDateTime, 1000);
updateDateTime();
// Actualiza la fecha y hora cada segundo