import { showLoader } from './loader.js';

document.addEventListener('DOMContentLoaded', () => {
    // const form = document.getElementById('crear-empleado-form');
    // const form = document.querySelector('form');
    const form = document.querySelector('#crear-empleado-form, #editar-empleado-form, #crear-user-form, #editar-user-form, #filtrosForm, #buscar-empleado-form');
    if (!form) return; // no estás en la vista de crear empleado

    form.addEventListener('submit', () => {
        showLoader();
    });
});


