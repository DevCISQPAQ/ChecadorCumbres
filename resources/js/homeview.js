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

        return {
            width: size,
            height: size
        };
    }

    const config = {
        fps: 10,
        qrbox: getResponsiveQrbox()
    };

    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById("result").innerText = "Resultado: " + decodedText;
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
// Opcional: Ajustar si la ventana cambia de tamaño
// window.addEventListener('resize', () => {
//     location.reload(); // recarga para reiniciar con nuevo tamaño
// });

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