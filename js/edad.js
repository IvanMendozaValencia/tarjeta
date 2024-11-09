const fecha_nacimiento = document.getElementById("fecha_nacimiento");
const edad = document.getElementById("edad");

const calcularEdad = (fecha_nacimiento) => {
    const fechaActual = new Date();
    const anoActual = parseInt(fechaActual.getFullYear());
    const mesActual = parseInt(fechaActual.getMonth()) + 1;
    const diaActual = parseInt(fechaActual.getDate());

    const anoNacimiento = parseInt(String(fecha_nacimiento).substring(0, 4));
    const mesNacimiento = parseInt(String(fecha_nacimiento).substring(5, 7));
    const diaNacimiento = parseInt(String(fecha_nacimiento).substring(8, 10));

    let edad = anoActual - anoNacimiento;
    if (mesActual < mesNacimiento) {
        edad--;
    } else if (mesActual === mesNacimiento) {
        if (diaActual < diaNacimiento) {
            edad--;
        }
    }
    return edad;
};

window.addEventListener('load', function () {

    fecha_nacimiento.addEventListener('change', function () {
        if (this.value) {
            edad.innerText = `${calcularEdad(this.value)}`;
        }
    });

});