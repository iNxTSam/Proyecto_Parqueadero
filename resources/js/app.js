import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

const pages = import.meta.glob('./pages/*.js', { eager: true });

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

async function postJson(url, payload) {
    const response = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            Accept: 'application/json',
        },
        body: JSON.stringify(payload),
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const message = data.message ?? Object.values(data.errors ?? {}).flat()[0] ?? 'No fue posible completar la operación.';
        throw new Error(message);
    }

    return data;
}

window.PQ = { csrfToken, postJson };

document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;

    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', () => body.classList.toggle('sidebar-open'));
    });

    document.querySelectorAll('[data-sidebar-close]').forEach((element) => {
        element.addEventListener('click', () => body.classList.remove('sidebar-open'));
    });

    document.querySelectorAll('.toast').forEach((element) => {
        new bootstrap.Toast(element, { delay: 5200 }).show();
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
        new bootstrap.Tooltip(element);
    });

    const page = body.dataset.page;
    const module = pages[`./pages/${page}.js`];

    if (page && module?.default) {
        module.default();
    }
});
