import { html5QrCode } from './qrscan.js';  // importa la instancia del scanner
import { showLoader, hideLoader } from './loader.js';

export function obtenerSaludoPorHora() {
    const ahora = new Date();
    const hora = ahora.getHours();

    if (hora >= 6 && hora < 12) return 'Buenos días';
    if (hora >= 12 && hora < 20) return 'Buenas tardes';
    return 'Buenas noches';
}

export function actualizarEmpleadoConSaludo(empleado, nombreElement, fotoElement) {
    const saludo = obtenerSaludoPorHora();
    let saludoColor = saludo === 'Buenos días' ? 'text-green-600' :
        saludo === 'Buenas tardes' ? 'text-yellow-600' :
            'text-blue-600';

    nombreElement.innerHTML = `<span class="${saludoColor} font-bold">${saludo}</span><br>${empleado.nombres} ${empleado.apellido_paterno} ${empleado.apellido_materno}`;
    fotoElement.src = empleado.foto ? `/img/empleados/${empleado.foto}` : `/img/escudo-gris.png`;
}

//



//
export function mostrarModalConfirmacion(mensaje) {
    return new Promise((resolve) => {
        hideLoader();
        const modal = document.getElementById('modalConfirmSalida');
        const mensajeElem = document.getElementById('mensajeConfirmSalida');
        const btnConfirmar = document.getElementById('btnConfirmarSalida');
        const btnCancelar = document.getElementById('btnCancelarSalida');

        mensajeElem.innerText = mensaje;
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        function limpiarEventos() {
            btnConfirmar.removeEventListener('click', onConfirmar);
            btnCancelar.removeEventListener('click', onCancelar);
        }


        function finalizar(confirmacion) {
            limpiarEventos();
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            resolve(confirmacion);

        }


        function onConfirmar() {
            finalizar(true);
            showLoader();
        }

        function onCancelar() {
            finalizar(false);
        }

        btnConfirmar.addEventListener('click', onConfirmar);
        btnCancelar.addEventListener('click', onCancelar);
    });
}

///

export async function actualizarCsrfToken() {

    const response = await fetch('/csrf-token', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        cache: 'no-store',
    });

    if (!response.ok) {
        throw new Error('No se pudo actualizar el token CSRF.');
    }

    const data = await response.json();

    if (!data.token) {
        throw new Error('Laravel no devolvió un token CSRF.');
    }

    const meta = document.querySelector('meta[name="csrf-token"]');

    if (meta) {
        meta.setAttribute('content', data.token);
    }

    return data.token;
}

function obtenerTokenCsrfActual() {

    const meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) {
        throw new Error('No se encontró el token CSRF.');
    }
    return meta.getAttribute('content');
}

setInterval(async () => {
    try {
        await actualizarCsrfToken();
        console.log('Token CSRF renovado correctamente.');
    } catch (error) {
        console.error(
            'No se pudo renovar el token CSRF:',
            error
        );
    }
}, 30 * 60 * 1000);


async function postConCsrf(url, opciones = {}) {

    let token = obtenerTokenCsrfActual();
    let response = await fetch(url, {
        ...opciones,

        headers: {
            ...opciones.headers,
            'X-CSRF-TOKEN': token,
        },

        credentials: 'same-origin',
    });

    if (response.status === 419) {
        console.warn(
            'Token CSRF expirado. Renovando token y reintentando...'
        );

        try {

            token = await actualizarCsrfToken();

        } catch (error) {

            console.error(
                'No se pudo renovar el token CSRF:',
                error
            );

            throw new Error(
                'La sesión expiró. No se pudo renovar automáticamente.'
            );
        }
        response = await fetch(url, {
            ...opciones,
            headers: {
                ...opciones.headers,
                'X-CSRF-TOKEN': token,
            },
            credentials: 'same-origin',
        });
    }
    return response;
}

//

// Maneja lógica común para registrar asistencia y mostrar resultados
export async function manejarAsistencia(empleadoId, elementos, options = {}) {
    showLoader();
    const { resultElement, pResult, nombreElement, fotoElement } = elementos;
    const textoOriginal = pResult.innerText;
    const nombreOriginal = nombreElement.innerText;
    const fotoOriginal = fotoElement.src;

    try {
        const response = await fetch(`/empleados/${empleadoId}/buscar`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                //     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            credentials: 'same-origin',
            cache: 'no-store',
        });

        if (!response.ok) {

            throw new Error(
                `Error HTTP ${response.status}`
            );
        }


        const data = await response.json();
        if (data.success === false) {
            throw new Error(data.error || 'Empleado no encontrado.');
        }

        const empleado = data.empleado ?? null;
        const asistencia = data.asistencia ?? null;

        if (!asistencia) {
            throw new Error('Ya no puedes registrar mas checadas hoy');
        }

        if (asistencia.tipo === 'salida_temprana') {
            if (options.pauseQr) options.pauseQr();
            const confirmar = await mostrarModalConfirmacion(asistencia.message);
            if (confirmar) {
                // const respSalida = await fetch(`/asistencia/${empleadoId}/salida`, {
                const respSalida = await postConCsrf(`/asistencia/${empleadoId}/salida`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                // if (!respSalida.ok) {
                //     const errSalida = await respSalida.json();
                //     throw new Error(errSalida.message || 'Error al marcar salida');
                // }
                if (!respSalida.ok) {
                    let errSalida = null;
                    try {
                        errSalida =
                            await respSalida.json();
                    } catch (e) {
                        // La respuesta no era JSON
                    }
                    throw new Error(
                        errSalida?.message ||
                        `Error al marcar salida (${respSalida.status})`
                    );
                }

                const dataSalida = await respSalida.json();
                pResult.innerText = dataSalida.message;
                resultElement.style.backgroundColor = "green";
                pResult.style.color = "white";
                actualizarEmpleadoConSaludo(empleado, nombreElement, fotoElement);
            } else {
                pResult.innerText = 'Salida no marcada.';
                resultElement.style.backgroundColor = "orange";
                pResult.style.color = "black";
            }
            if (options.resumeQr) options.resumeQr();   // Para el qr escáner, reanudar escaneo
        } else {
            actualizarEmpleadoConSaludo(empleado, nombreElement, fotoElement);
            pResult.innerText = asistencia.message;
            pResult.style.color = asistencia.success ? "white" : "white";
            resultElement.style.backgroundColor = asistencia.success ? "green" : "red";
        }

    } catch (error) {
        pResult.innerText = error.message;
        resultElement.style.backgroundColor = "red";
        pResult.style.color = "white";
        nombreElement.innerText = "No identificado";
        fotoElement.src = `/img/escudo-gris.png`;
    } finally {
        hideLoader();
        setTimeout(() => {
            pResult.innerText = textoOriginal;
            pResult.style.color = "black";
            nombreElement.innerText = nombreOriginal;
            resultElement.style.backgroundColor = "white";
            fotoElement.src = fotoOriginal;

            try {
                if (options.resumeQr) options.resumeQr();
            } catch (e) {
                // console.warn('No se pudo reanudar el QR: ', e.message);
            }
            if (options.clearInput) options.clearInput(); // Para input, limpiar campo
        }, 2500);
    }
}
