# Sistema de Parqueadero

Aplicación web para administrar la operación de un parqueadero: registro de entrada y salida de vehículos, tarifas, contratos, recibos y reportes, con tres roles: **vigilante**, **administrador** y **superadministrador**.

## Funcionalidades

### Vigilante
- Registro de entrada y salida de vehículos.
- Verificación de usuario y contrato antes del ingreso.
- Consulta y gestión de vehículos.

### Administrador
- Gestión de usuarios y roles.
- Creación, actualización y renovación de contratos.
- Gestión de clientes y vehículos.

### Superadministrador
- Tarifas por tipo de vehículo.
- Bahías del parqueadero.
- Reportes exportables en CSV y PDF.
- Configuración de tipos de vehículo.

## Stack

- PHP 8.2+ y Laravel 12
- MySQL
- Blade, Tailwind CSS 4 y Vite
- DomPDF para los reportes

## Instalación

```bash
git clone https://github.com/iNxTSam/Proyecto_Parqueadero.git
cd Proyecto_Parqueadero
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configura la base de datos en `.env`:

```dotenv
DB_CONNECTION=mysql
DB_DATABASE=proyecto_parqueadero
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base de datos y ejecuta las migraciones:

```bash
php artisan migrate
npm run build
php artisan serve
```

La aplicación queda disponible en `http://127.0.0.1:8000`.

## Estructura

- `app/Http/Controllers`: lógica por rol (`VigilanteController`, `AdminController`, `SuperAdminController` y autenticación).
- `app/Models`: usuarios, vehículos, tickets, tarifas, contratos, recibos, bahías y reportes.
- `routes/web.php`: rutas agrupadas por rol.
- `resources/views`: vistas Blade de administración, superadministración y vigilancia.

## Contexto

Proyecto académico de Ingeniería de Software. Repositorio: [github.com/iNxTSam](https://github.com/iNxTSam).
