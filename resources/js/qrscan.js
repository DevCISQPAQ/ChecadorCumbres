// qrscan.js
import { Html5Qrcode } from "html5-qrcode";
import { manejarAsistencia } from './utils.js';
import { showLoader, hideLoader } from './loader.js';

const qrRegionId = "reader";
export let html5QrCode = null;

if (document.getElementById(qrRegionId)) {
    html5QrCode = new Html5Qrcode(qrRegionId);

    function getResponsiveQrbox() {
        const width = window.innerWidth;
        let size = 250;
        if (width >= 768) size = 350;
        if (width >= 1024) size = 450;
        return { width: size, height: size };
    }

    const config = { fps: 10, qrbox: getResponsiveQrbox() };

    async function onScanSuccess(decodedText) {
        html5QrCode.pause(true);

        const elementos = {
            resultElement: document.getElementById("result"),
            pResult: document.getElementById("result").querySelector('p'),
            nombreElement: document.getElementById("nombre-empleado"),
            fotoElement: document.getElementById("foto-empleado")
        };

        showLoader(); // Mostrar loader antes de la llamada
        try {
            await manejarAsistencia(decodedText, elementos, {
                resumeQr: () => html5QrCode.resume()
            });
        } catch (error) {
            console.error("Error en manejo de asistencia con QR:", error);
        } finally {
            hideLoader(); // Ocultar loader después que termina
        }
    }

    Html5Qrcode.getCameras()
        .then(devices => {
            if (devices && devices.length) {
                let cameraId = devices[0].id;
                html5QrCode.start(cameraId, config, onScanSuccess);
            }
        })
        .catch(err => {
            console.error("No se pudo acceder a la cámara:", err);
        });
}
