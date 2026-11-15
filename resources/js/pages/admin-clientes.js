export default function init() {
    const form = document.getElementById('formCliente');
    const titulo = document.getElementById('tituloCliente');
    const metodo = document.getElementById('clienteMethod');
    const storeAction = form.dataset.storeAction;

    const campos = {
        id: document.getElementById('clienteId'),
        nombre: document.getElementById('clienteNombre'),
        telefono: document.getElementById('clienteTelefono'),
        correo: document.getElementById('clienteCorreo'),
        rol: document.getElementById('clienteRol'),
        activo: document.getElementById('clienteActivo'),
    };

    document.getElementById('btnNuevoCliente')?.addEventListener('click', () => {
        form.action = storeAction;
        metodo.innerHTML = '';
        titulo.textContent = 'Nuevo cliente';
        form.reset();
        campos.rol.value = campos.rol.options[0]?.value ?? '';
        campos.activo.value = '1';
    });

    document.querySelectorAll('[data-action][data-telefono]').forEach((boton) => {
        boton.addEventListener('click', () => {
            form.action = boton.dataset.action;
            metodo.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            titulo.textContent = 'Editar cliente';

            campos.id.value = boton.dataset.id ?? '';
            campos.nombre.value = boton.dataset.nombre ?? '';
            campos.telefono.value = boton.dataset.telefono ?? '';
            campos.correo.value = boton.dataset.correo ?? '';
            campos.rol.value = boton.dataset.rol ?? '';
            campos.activo.value = boton.dataset.activo ?? '1';
        });
    });
}
