export default function init() {
    const form = document.getElementById('formTarifa');
    const titulo = document.getElementById('tituloTarifa');
    const metodo = document.getElementById('tarifaMethod');
    const storeAction = form.dataset.storeAction;

    const campos = {
        nombre: document.getElementById('tarifaNombre'),
        tipo: document.getElementById('tarifaTipo'),
        tipoVehiculo: document.getElementById('tarifaTipoVehiculo'),
        valor: document.getElementById('tarifaValor'),
        dias: document.getElementById('tarifaDias'),
        activa: document.getElementById('tarifaActiva'),
    };

    document.getElementById('btnNuevaTarifa')?.addEventListener('click', () => {
        form.action = storeAction;
        metodo.innerHTML = '';
        titulo.textContent = 'Nueva tarifa';
        form.reset();
        campos.activa.value = '1';
    });

    document.querySelectorAll('[data-bs-target="#modalTarifa"][data-id]').forEach((boton) => {
        boton.addEventListener('click', () => {
            form.action = boton.dataset.action;
            metodo.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            titulo.textContent = 'Editar tarifa';

            campos.nombre.value = boton.dataset.nombre ?? '';
            campos.tipo.value = boton.dataset.tipo ?? 'Mes';
            campos.tipoVehiculo.value = boton.dataset.tipoVehiculo ?? '';
            campos.valor.value = boton.dataset.valor ?? '';
            campos.dias.value = boton.dataset.dias ?? '';
            campos.activa.value = boton.dataset.activa ?? '1';
        });
    });
}
