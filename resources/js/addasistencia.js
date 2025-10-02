import { manejarAsistencia } from './utils.js';
import { html5QrCode } from './qrscan.js';

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnNumEmp');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        const input = document.getElementById('numEmpleadoInput');
        const empleadoId = input.value.trim();


        if (!empleadoId) {
            const resultElement = document.getElementById("result");
            const pResult = resultElement.querySelector('p');
            const textoOriginal = pResult.innerText;
            pResult.innerText = 'Por favor ingresa un número de empleado válido.';
            setTimeout(() => {
                pResult.innerText = textoOriginal;
            }, 2500);
            return;
        }

        const elementos = {
            resultElement: document.getElementById("result"),
            pResult: document.getElementById("result").querySelector('p'),
            nombreElement: document.getElementById("nombre-empleado"),
            fotoElement: document.getElementById("foto-empleado")
        };

        await manejarAsistencia(empleadoId, elementos, {
            clearInput: () => input.value = '',
            pauseQr: () => html5QrCode?.pause(true),
            resumeQr: () => html5QrCode?.resume()
        });
    });
});

