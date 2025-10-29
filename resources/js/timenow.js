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
        hour12: false  
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