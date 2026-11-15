export default function init() {
    const filtrarTarifas = (selectTarifas, tipo) => {
        const opciones = Array.from(selectTarifas.querySelectorAll('option[data-tipo]'));
        let primeraValida = '';

        opciones.forEach((opcion) => {
            const coincide = String(opcion.dataset.tipo) === String(tipo);
            opcion.hidden = !coincide;
            opcion.disabled = !coincide;

            if (coincide && !primeraValida) {
                primeraValida = opcion.value;
            }
        });

        selectTarifas.value = primeraValida;
    };

    const vehiculo = document.getElementById('contratoVehiculo');
    const tarifa = document.getElementById('contratoTarifa');
    const ayuda = document.getElementById('contratoAyuda');

    vehiculo?.addEventListener('change', () => {
        const opcion = vehiculo.selectedOptions[0];
        const tipo = opcion?.dataset.tipo;

        if (!tipo) {
            return;
        }

        filtrarTarifas(tarifa, tipo);
        ayuda.textContent = 'Tarifas disponibles para el tipo de vehículo seleccionado.';
    });

    // Editar
    const formEditar = document.getElementById('formEditarContrato');
    const editarValor = document.getElementById('editarValor');
    const editarEstado = document.getElementById('editarEstado');
    const editarObservaciones = document.getElementById('editarObservaciones');

    document.querySelectorAll('[data-bs-target="#modalEditarContrato"]').forEach((boton) => {
        boton.addEventListener('click', () => {
            formEditar.action = boton.dataset.action;
            editarValor.value = boton.dataset.valor ?? '';
            editarEstado.value = boton.dataset.estado ?? '';
            editarObservaciones.value = boton.dataset.observaciones ?? '';
        });
    });

    // Renovar
    const formRenovar = document.getElementById('formRenovarContrato');
    const renovarPlaca = document.getElementById('renovarPlaca');
    const renovarTarifa = document.getElementById('renovarTarifa');

    document.querySelectorAll('[data-bs-target="#modalRenovarContrato"]').forEach((boton) => {
        boton.addEventListener('click', () => {
            formRenovar.action = boton.dataset.action;
            renovarPlaca.textContent = boton.dataset.placa ?? '';
            filtrarTarifas(renovarTarifa, boton.dataset.tipo);
        });
    });
}
