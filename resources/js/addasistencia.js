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

            const saludo = obtenerSaludoPorHora();

            nombreElement.innerText = `${saludo}\n${empleado.nombres} ${empleado.apellido_paterno} ${empleado.apellido_materno}`;

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

    if (hora < 10) {
        return 'Bienvenido';
    } else if (hora >= 15) {
        return 'Hasta pronto';
    } else {
        return 'Hola';
    }
}