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

export function mostrarModalConfirmacion(mensaje) {
    return new Promise((resolve) => {
        const modal = document.getElementById('modalConfirmSalida');
        const mensajeElem = document.getElementById('mensajeConfirmSalida');
        const btnConfirmar = document.getElementById('btnConfirmarSalida');
        const btnCancelar = document.getElementById('btnCancelarSalida');

        mensajeElem.innerText = mensaje;
        modal.classList.remove('hidden');

        function limpiarEventos() {
            btnConfirmar.removeEventListener('click', onConfirmar);
            btnCancelar.removeEventListener('click', onCancelar);
        }

        function onConfirmar() {
            limpiarEventos();
            modal.classList.add('hidden');
            resolve(true);
        }

        function onCancelar() {
            limpiarEventos();
            modal.classList.add('hidden');
            resolve(false);
        }

        btnConfirmar.addEventListener('click', onConfirmar);
        btnCancelar.addEventListener('click', onCancelar);
    });
}


// Maneja lógica común para registrar asistencia y mostrar resultados
export async function manejarAsistencia(empleadoId, elementos, options = {}) {
    const { resultElement, pResult, nombreElement, fotoElement } = elementos;
    const textoOriginal = pResult.innerText;
    const nombreOriginal = nombreElement.innerText;
    const fotoOriginal = fotoElement.src;

    try {
        const response = await fetch(`/empleado/${empleadoId}/buscar`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Error desconocido');
        }

        const data = await response.json();
        const empleado = data.empleado;
        const asistencia = data.asistencia;

        if (asistencia.confirmar_salida) {
            const confirmar = await mostrarModalConfirmacion(asistencia.message);
            if (confirmar) {
                const respSalida = await fetch(`/asistencia/${asistencia.asistencia_id}/salida`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({}),
                });

                if (!respSalida.ok) {
                    const errSalida = await respSalida.json();
                    throw new Error(errSalida.message || 'Error al marcar salida');
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
        setTimeout(() => {
            pResult.innerText = textoOriginal;
            pResult.style.color = "black";
            nombreElement.innerText = nombreOriginal;
            resultElement.style.backgroundColor = "white";
            fotoElement.src = fotoOriginal;

            if (options.resumeQr) options.resumeQr();   // Para el qr escáner, reanudar escaneo
            if (options.clearInput) options.clearInput(); // Para input, limpiar campo
        }, 2500);
    }
}
