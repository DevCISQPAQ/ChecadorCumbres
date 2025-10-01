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

            if (asistencia.confirmar_salida) {
                const confirmar = await mostrarModalConfirmacion(asistencia.message);
                if (confirmar) {
                    // Llamar API para registrar salida
                    const respSalida = await fetch(`/asistencia/${asistencia.asistencia_id}/salida`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({})
                    });

                    if (!respSalida.ok) {
                        const errSalida = await respSalida.json();
                        throw new Error(errSalida.message || 'Error al marcar salida');
                    }

                    const dataSalida = await respSalida.json();

                    pResult.innerText = dataSalida.message;
                    resultElement.style.backgroundColor = "green";
                    pResult.style.color = "white";

                    // Actualizar nombre, foto y saludo con nueva función
                    actualizarEmpleadoConSaludo(empleado, nombreElement, fotoElement);

                } else {
                    // Usuario cancela confirmación
                    pResult.innerText = 'Salida no marcada.';
                    resultElement.style.backgroundColor = "orange";
                    pResult.style.color = "black";

                    // También actualizar nombre y foto aunque no marque salida
                    // actualizarEmpleadoConSaludo(empleado, nombreElement, fotoElement);
                }

            } else {
                // Mostrar info normal (entrada, salida, etc)
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



