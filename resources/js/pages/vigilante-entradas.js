export default function init() {
    const rutas = window.PQ_RUTAS ?? {};
    const propietario = document.getElementById('ingresoPropietario');
    const vehiculo = document.getElementById('ingresoVehiculo');
    const btnVerificar = document.getElementById('btnVerificarCliente');
    const btnIngresar = document.getElementById('btnRegistrarIngreso');
    const mensaje = document.getElementById('ingresoMensaje');
    const contrato = document.getElementById('ingresoContrato');
    const formIngreso = document.getElementById('formIngreso');

    let clienteActual = null;

    const mostrar = (elemento, tono, texto) => {
        if (!elemento) return;
        elemento.className = `alert alert-${tono}`;
        elemento.textContent = texto;
        elemento.classList.remove('d-none');
    };

    const ocultar = (elemento) => {
        elemento?.classList.add('d-none');
        if (elemento) elemento.textContent = '';
    };

    const limpiarValidacion = () => {
        clienteActual = null;
        vehiculo.innerHTML = '<option value="">Primero verifica el cliente</option>';
        vehiculo.disabled = true;
        btnIngresar.disabled = true;
        ocultar(mensaje);
        ocultar(contrato);
    };

    const verificarContrato = async () => {
        if (!vehiculo.value) {
            btnIngresar.disabled = true;
            ocultar(contrato);
            return;
        }

        if (clienteActual?.rol_id !== 1) {
            ocultar(contrato);
            btnIngresar.disabled = false;
            return;
        }

        btnIngresar.disabled = true;
        ocultar(contrato);

        try {
            const data = await window.PQ.postJson(rutas.verificarContrato, { vehiculo_id: vehiculo.value });

            if (data.existe) {
                mostrar(contrato, 'success', 'Contrato activo. El vehículo puede ingresar.');
                btnIngresar.disabled = false;
            } else {
                mostrar(contrato, 'danger', 'El aprendiz no tiene un contrato activo para este vehículo.');
            }
        } catch (error) {
            mostrar(contrato, 'danger', error.message);
        }
    };

    btnVerificar?.addEventListener('click', async () => {
        const documento = propietario.value.trim();

        limpiarValidacion();

        if (!documento) {
            mostrar(mensaje, 'danger', 'Ingresa el documento del propietario.');
            return;
        }

        btnVerificar.disabled = true;

        try {
            const data = await window.PQ.postJson(rutas.verificarCliente, { propietario_id: documento });

            if (!data.existe) {
                mostrar(mensaje, 'danger', 'Cliente no encontrado.');
                return;
            }

            clienteActual = data.cliente;

            if (!data.cliente.activo) {
                mostrar(mensaje, 'danger', 'El cliente está inactivo.');
                return;
            }

            mostrar(mensaje, 'success', `Cliente: ${data.cliente.nombre}`);

            vehiculo.innerHTML = '<option value="">Seleccione el vehículo</option>';

            data.vehiculos.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.placa;
                option.textContent = item.etiqueta;
                vehiculo.appendChild(option);
            });

            vehiculo.disabled = data.vehiculos.length === 0;

            if (data.vehiculos.length === 0) {
                mostrar(mensaje, 'danger', 'El cliente no tiene vehículos activos registrados.');
            }
        } catch (error) {
            mostrar(mensaje, 'danger', error.message);
        } finally {
            btnVerificar.disabled = false;
        }
    });

    propietario?.addEventListener('input', limpiarValidacion);
    vehiculo?.addEventListener('change', verificarContrato);

    formIngreso?.addEventListener('submit', () => {
        btnIngresar.disabled = true;
        btnIngresar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Registrando...';
    });

    // Modal de salida
    const modalSalida = document.getElementById('modalSalida');
    const formSalida = document.getElementById('formSalida');
    const salidaPlaca = document.getElementById('salidaPlaca');
    const salidaPropietario = document.getElementById('salidaPropietario');
    const salidaTicketGroup = document.getElementById('salidaTicketGroup');
    const salidaTicket = document.getElementById('salidaTicket');

    modalSalida?.addEventListener('show.bs.modal', (event) => {
        const boton = event.relatedTarget;
        if (!boton) return;

        formSalida.action = boton.dataset.action;
        salidaPlaca.textContent = boton.dataset.placa;
        salidaPropietario.textContent = boton.dataset.propietario;
        salidaTicket.value = '';

        const requiereTicket = boton.dataset.ticketRequerido === '1';
        salidaTicketGroup.classList.toggle('d-none', !requiereTicket);
        salidaTicket.required = requiereTicket;
    });
}
