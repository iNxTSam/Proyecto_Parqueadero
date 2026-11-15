export default function init() {
    const form = document.getElementById('formTipo');
    const titulo = document.getElementById('tituloTipo');
    const metodo = document.getElementById('tipoMethod');
    const storeAction = form.dataset.storeAction;
    const nombre = document.getElementById('tipoNombre');
    const descripcion = document.getElementById('tipoDescripcion');

    document.getElementById('btnNuevoTipo')?.addEventListener('click', () => {
        form.action = storeAction;
        metodo.innerHTML = '';
        titulo.textContent = 'Nuevo tipo de vehículo';
        form.reset();
    });

    document.querySelectorAll('[data-bs-target="#modalTipo"][data-nombre]').forEach((boton) => {
        boton.addEventListener('click', () => {
            form.action = boton.dataset.action;
            metodo.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            titulo.textContent = 'Editar tipo de vehículo';

            nombre.value = boton.dataset.nombre ?? '';
            descripcion.value = boton.dataset.descripcion ?? '';
        });
    });
}
