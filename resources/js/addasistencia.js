document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnNumEmp');
    if (!btn) return; // Si no está el botón, no hacemos nada

    btn.addEventListener('click', async () => {
        const input = document.getElementById('numEmpleadoInput');
        const empleadoId = input.value.trim();

        const resultElement = document.getElementById("result");
        const pResult = resultElement.querySelector('p');
        const nombreElement = document.getElementById("nombre-empleado");
        const fotoElement = document.getElementById("foto-empleado");

        // Guarda los valores originales para poder restaurarlos después
        const textoOriginal = pResult.innerText;
        const nombreOriginal = nombreElement.innerText;
        const fotoOriginal = fotoElement.src;

        if (!empleadoId) {
            pResult.innerText = 'Por favor ingresa un número de empleado válido.';
            setTimeout(() => {
                pResult.innerText = textoOriginal;  // Restaura después de 3 segundos
            }, 2500);
            return;
        }

        try {
            const response = await fetch(`/empleado/${empleadoId}/buscar`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest', // opcional pero útil en Laravel
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // si usas POST cambia el método y añade token
                }
            });

            // Verificamos si la respuesta fue exitosa
            if (!response.ok) {
                // Por ejemplo si es 404, 422, 500, etc.
                const errorData = await response.json();
                throw new Error(errorData.error || 'Error desconocido');
            }

            const data = await response.json();
            const empleado = data.empleado;
            const asistencia = data.asistencia;

            // actualizarEmpleado(empleado);

            if (asistencia.confirmar_salida) {
                // Pedir confirmación al usuario
                const confirmar = await mostrarModalConfirmacion(asistencia.message);
                if (confirmar) {
                    // Si confirma, llamar API para registrar salida
                    const respSalida = await fetch(`/asistencia/${asistencia.asistencia_id}/salida`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({}) // si necesitas enviar datos extra
                    });

                    if (!respSalida.ok) {
                        const errSalida = await respSalida.json();
                        throw new Error(errSalida.message || 'Error al marcar salida');
                    }

                    const dataSalida = await respSalida.json();
                    pResult.innerText = dataSalida.message;
                    resultElement.style.backgroundColor = "green";
                    pResult.style.color = "white";

                    // Actualizar nombre y foto también si quieres
                    actualizarEmpleadoConSaludo(empleado, nombreElement, fotoElement);
                } else {
                    // Si cancela
                    pResult.innerText = 'Salida no marcada.';
                    resultElement.style.backgroundColor = "orange";
                    pResult.style.color = "black";

                    // actualizarEmpleado(empleado);
                }
            } else {
                // Mostrar mensaje normal (entrada, salida, error, etc)
                // const saludo = obtenerSaludoPorHora();
                // let saludoColor = saludo.includes('Bienvenido') ? 'text-green-600' :
                //     saludo.includes('Hasta pronto') ? 'text-yellow-600' :
                //         'text-blue-600';

                // nombreElement.innerHTML = `<span class="${saludoColor} font-bold">${saludo}</span><br>${empleado.nombres} ${empleado.apellido_paterno} ${empleado.apellido_materno}`;

                // if (empleado.foto) {
                //     fotoElement.src = `/img/empleados/${empleado.foto}`;
                // } else {
                //     fotoElement.src = `/img/escudo-gris.png`;
                // }

                pResult.innerText = asistencia.message;
                pResult.style.color = asistencia.success ? "white" : "white";
                resultElement.style.backgroundColor = asistencia.success ? "green" : "red";

                actualizarEmpleadoConSaludo(empleado, nombreElement, fotoElement);
            }

        } catch (error) {
            pResult.innerText = error.message;
            resultElement.style.backgroundColor = "red";
            pResult.style.color = "white";
            pResult.innerText = "No identificado";
            fotoElement.src = `/img/escudo-gris.png`;
        } finally {
            setTimeout(() => {
                pResult.innerText = textoOriginal;
                pResult.style.color = "red";
                nombreElement.innerText = nombreOriginal;
                resultElement.style.backgroundColor = "white";
                input.value = '';
                fotoElement.src = fotoOriginal;
            }, 2500);
        }
    });
});



function obtenerSaludoPorHora() {
    const ahora = new Date();
    const hora = ahora.getHours();

    if (hora >= 6 && hora < 12) {
        return 'Buenos días';
    } else if (hora >= 12 && hora < 20) {
        return 'Buenas tardes';
    } else {
        return 'Buenas noches';
    }
}

function actualizarEmpleadoConSaludo(empleado, nombreElement, fotoElement) {
    const saludo = obtenerSaludoPorHora();
    let saludoColor = saludo === 'Buenos días' ? 'text-green-600' :
        saludo === 'Buenas tardes' ? 'text-yellow-600' :
            'text-blue-600';

    nombreElement.innerHTML = `<span class="${saludoColor} font-bold">${saludo}</span><br>${empleado.nombres} ${empleado.apellido_paterno} ${empleado.apellido_materno}`;

    fotoElement.src = empleado.foto ? `/img/empleados/${empleado.foto}` : `/img/escudo-gris.png`;
}

function mostrarModalConfirmacion(mensaje) {
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
