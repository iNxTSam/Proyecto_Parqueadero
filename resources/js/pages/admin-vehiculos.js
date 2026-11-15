export default function init() {
    const form = document.getElementById('formVehiculo');
    const titulo = document.getElementById('tituloVehiculo');
    const metodo = document.getElementById('vehiculoMethod');
    const storeAction = form.dataset.storeAction;

    const campos = {
        placa: document.getElementById('vehiculoPlaca'),
        tipo: document.getElementById('vehiculoTipo'),
        propietario: document.getElementById('vehiculoPropietario'),
        color: document.getElementById('vehiculoColor'),
        marca: document.getElementById('vehiculoMarca'),
        modelo: document.getElementById('vehiculoModelo'),
        activo: document.getElementById('vehiculoActivo'),
    };

    document.getElementById('btnNuevoVehiculo')?.addEventListener('click', () => {
        form.action = storeAction;
        metodo.innerHTML = '';
        titulo.textContent = 'Nuevo vehículo';
        form.reset();
        campos.activo.value = '1';
    });

    document.querySelectorAll('[data-action][data-placa]').forEach((boton) => {
        boton.addEventListener('click', () => {
            form.action = boton.dataset.action;
            metodo.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            titulo.textContent = 'Editar vehículo';

            campos.placa.value = boton.dataset.placa ?? '';
            campos.tipo.value = boton.dataset.tipo ?? '';
            campos.propietario.value = boton.dataset.propietario ?? '';
            campos.color.value = boton.dataset.color ?? '';
            campos.marca.value = boton.dataset.marca ?? '';
            campos.modelo.value = boton.dataset.modelo ?? '';
            campos.activo.value = boton.dataset.activo ?? '1';
        });
    });
}
