export default function init() {
    const form = document.getElementById('formUsuario');
    const titulo = document.getElementById('tituloUsuario');
    const metodo = document.getElementById('usuarioMethod');
    const password = document.getElementById('usuarioPassword');
    const passwordHint = document.getElementById('passwordHint');

    const campos = {
        nombre: document.getElementById('usuarioNombre'),
        cedula: document.getElementById('usuarioCedula'),
        carnet: document.getElementById('usuarioCarnet'),
        email: document.getElementById('usuarioEmail'),
        contacto: document.getElementById('usuarioContacto'),
        rol: document.getElementById('usuarioRol'),
        activo: document.getElementById('usuarioActivo'),
    };

    const modoCrear = () => {
        form.action = form.dataset.storeAction ?? form.action;
        metodo.innerHTML = '';
        titulo.textContent = 'Nuevo usuario';
        password.required = true;
        passwordHint.textContent = '';
        form.reset();

        Object.values(campos).forEach((campo) => {
            if (campo && campo.tagName !== 'SELECT') campo.value = '';
        });

        campos.activo.value = '1';
        campos.rol.value = campos.rol.options[0]?.value ?? '';
    };

    document.getElementById('btnNuevoUsuario')?.addEventListener('click', modoCrear);

    document.querySelectorAll('[data-action][data-cedula]').forEach((boton) => {
        boton.addEventListener('click', () => {
            form.action = boton.dataset.action;
            metodo.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            titulo.textContent = 'Editar usuario';

            campos.nombre.value = boton.dataset.nombre ?? '';
            campos.cedula.value = boton.dataset.cedula ?? '';
            campos.carnet.value = boton.dataset.carnet ?? '';
            campos.email.value = boton.dataset.email ?? '';
            campos.contacto.value = boton.dataset.contacto ?? '';
            campos.rol.value = boton.dataset.rol ?? '';
            campos.activo.value = boton.dataset.activo ?? '1';

            password.value = '';
            password.required = false;
            passwordHint.textContent = '(dejar vacía para conservar la actual)';
        });
    });
}
