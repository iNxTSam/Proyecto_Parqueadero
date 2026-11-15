export default function init() {
    const form = document.getElementById('formBahia');
    const titulo = document.getElementById('tituloBahia');
    const metodo = document.getElementById('bahiaMethod');
    const storeAction = form.dataset.storeAction;

    const campos = {
        numero: document.getElementById('bahiaNumero'),
        tipo: document.getElementById('bahiaTipo'),
        capacidad: document.getElementById('bahiaCapacidad'),
        ubicacion: document.getElementById('bahiaUbicacion'),
        activa: document.getElementById('bahiaActiva'),
    };

    document.getElementById('btnNuevaBahia')?.addEventListener('click', () => {
        form.action = storeAction;
        metodo.innerHTML = '';
        titulo.textContent = 'Nueva bahía';
        form.reset();
        campos.activa.value = '1';
    });

    document.querySelectorAll('[data-bs-target="#modalBahia"][data-numero]').forEach((boton) => {
        boton.addEventListener('click', () => {
            form.action = boton.dataset.action;
            metodo.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            titulo.textContent = 'Editar bahía';

            campos.numero.value = boton.dataset.numero ?? '';
            campos.tipo.value = boton.dataset.tipo ?? '';
            campos.capacidad.value = boton.dataset.capacidad ?? '';
            campos.ubicacion.value = boton.dataset.ubicacion ?? '';
            campos.activa.value = boton.dataset.activa ?? '1';
        });
    });
}
