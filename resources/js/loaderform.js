import { showLoader } from './loader.js';

document.addEventListener('DOMContentLoaded', () => {

    const forms = document.querySelectorAll(
        '#crear-empleado-form, ' +
        '#editar-empleado-form, ' +
        '#crear-user-form, ' +
        '#editar-user-form, ' +
        '#filtrosForm, ' +
        '#buscar-empleado-form, ' +
        '#guardar-vacaciones, ' +
        '#guardar-festivo'
    );

    if (!forms.length) return;

    forms.forEach(form => {

        form.addEventListener('submit', () => {
            showLoader();
        });

    });

});