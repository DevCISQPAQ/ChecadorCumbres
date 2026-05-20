window.buscadorEmpleado = function (empleados) {

    return {

        buscarEmpleado: '',

        empleadoSeleccionado: null,

        empleados: empleados,

        get empleadosFiltrados() {

            return this.empleados.filter(emp => {

                let texto = `
                    ${emp.nombres}
                    ${emp.apellido_paterno}
                    ${emp.apellido_materno}
                    ${emp.n_empleado}
                `.toLowerCase();

                return texto.includes(
                    this.buscarEmpleado.toLowerCase()
                );

            });

        },

        seleccionarEmpleado(emp) {

            this.empleadoSeleccionado = emp;

            this.buscarEmpleado = `
                ${emp.nombres}
                ${emp.apellido_paterno}
                ${emp.apellido_materno}
            `;

        },

        get empleadoCompleto() {

            if (!this.empleadoSeleccionado) return '';

            return `
                ${this.empleadoSeleccionado.nombres}
                ${this.empleadoSeleccionado.apellido_paterno}
                ${this.empleadoSeleccionado.apellido_materno}
            `;

        }

    }

}