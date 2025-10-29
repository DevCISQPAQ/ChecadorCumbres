import { manejarAsistencia } from './utils.js';
import { html5QrCode } from './qrscan.js';
import { showLoader, hideLoader } from './loader.js';

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnNumEmp');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        btn.disabled = true;
        showLoader();  // Mostrar loader al inicio

        const input = document.getElementById('numEmpleadoInput');
        const empleadoId = input.value.trim();


        if (!empleadoId) {
            const resultElement = document.getElementById("result");
            const pResult = resultElement.querySelector('p');
            const textoOriginal = pResult.innerText;
            pResult.innerText = 'Por favor ingresa un número de empleado válido.';
            hideLoader(); // Oculta loader si hay error
            setTimeout(() => {
                pResult.innerText = textoOriginal;
                btn.disabled = false;
            }, 2500);
            return;
        }

        const elementos = {
            resultElement: document.getElementById("result"),
            pResult: document.getElementById("result").querySelector('p'),
            nombreElement: document.getElementById("nombre-empleado"),
            fotoElement: document.getElementById("foto-empleado")
        };

        try {
            await manejarAsistencia(empleadoId, elementos, {
                clearInput: () => input.value = '',
                pauseQr: () => html5QrCode?.pause(true),
                resumeQr: () => html5QrCode?.resume()
            });
        } catch (error) {
            console.error('Error al agregar asistencia:', error);
        } finally {
            hideLoader(); // Ocultar loader si hay error
            setTimeout(() => {
                btn.disabled = false;
            }, 2500);
        }
    });
});


