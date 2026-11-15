# Sistema de Parqueadero

Aplicación web para administrar la operación de un parqueadero: entradas y salidas de vehículos, contratos, tarifas, bahías, clientes, usuarios y reportes. Incluye tres roles con permisos separados: **superadministrador**, **administrador** y **vigilante**.

## Stack

| Componente | Tecnología |
| --- | --- |
| Backend | PHP 8.3+ · Laravel 12 |
| Base de datos | MySQL 8 (SQLite para pruebas) |
| Frontend | Blade · Bootstrap 5 · Vite 7 |
| Reportes | DomPDF y CSV |
| Calidad | PHPUnit · Pint · Larastan |

## Funcionalidades

### Vigilante
- Registro de entrada con verificación del cliente y de su contrato.
- Ticket único por entrada.
- Registro de salida con validación del ticket para aprendices.
- Consulta de vehículos y ocupación por tipo.

### Administrador
- Gestión de usuarios (solo administradores y vigilantes).
- Gestión de clientes y vehículos.
- Creación, edición y renovación de contratos.
- Emisión automática de recibos al crear o renovar un contrato.
- Panel con contratos próximos a vencer.

### Superadministrador
- Tarifas por tipo de vehículo y periodo.
- Bahías y capacidad instalada.
- Tipos de vehículo.
- Reportes de ocupación, ingresos, contratos y usuarios en CSV o PDF.
- Panel con facturación y ocupación general.

## Reglas de negocio

- **Aprendiz** (tipo de cliente 1): requiere un contrato vigente para ingresar y debe presentar el ticket al salir.
- **Natural** (tipo de cliente 2): puede ingresar sin contrato; la salida no exige ticket.
- Solo puede existir **una entrada abierta por vehículo**.
- El cupo se calcula sumando la capacidad de las bahías **activas** del tipo de vehículo.
- Un vehículo solo puede tener **un contrato activo** a la vez.
- Al renovar, el contrato anterior se marca como finalizado y se crea uno nuevo.
- Los contratos vencidos se marcan como finalizados automáticamente al consultar el módulo.

## Requisitos

- PHP 8.3 o superior con `pdo_mysql` y `mbstring`.
- Composer 2.
- Node.js 20 o superior.
- MySQL 8 (o Docker para levantarlo con el `docker-compose.yml` incluido).

## Instalación

```bash
git clone https://github.com/iNxTSam/Proyecto_Parqueadero.git
cd Proyecto_Parqueadero

composer install
npm install

cp .env.example .env
php artisan key:generate

docker compose up -d
php artisan migrate --seed

npm run build
php artisan serve
```

La aplicación queda en `http://127.0.0.1:8000`.

> En Windows, usa `copy .env.example .env` en lugar de `cp`.

El `docker-compose.yml` levanta MySQL en el puerto `3307` con las credenciales que ya trae `.env.example`. Si usas un MySQL local, ajusta `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

## Usuarios de demostración

| Rol | Documento | Contraseña |
| --- | --- | --- |
| Superadministrador | `1000000001` | `Demo12345!` |
| Administrador | `1000000002` | `Demo12345!` |
| Vigilante | `1000000003` | `Demo12345!` |

Los seeders también crean dos clientes de ejemplo (`900000001` aprendiz y `900000002` natural) y dos vehículos (`ABC123` y `XYZ45A`).

## Estructura

```
app/
  Exceptions/          Errores de negocio
  Http/Controllers/    Controladores por rol
  Http/Middleware/     Roles y control de caché
  Http/Requests/       Validación de formularios
  Models/              Eloquent (tablas en español)
  Services/            Parqueo, contratos y reportes
database/
  migrations/          Esquema (fuente de verdad)
  seeders/             Catálogos y datos de demostración
  parqueadero.sql      Volcado completo para entrega/importación
resources/
  css/app.css          Tema Bootstrap
  js/app.js            Bootstrap y carga de módulos
  js/pages/            Lógica por pantalla
  views/               Blade
routes/web.php         Rutas agrupadas por rol
```

## Pruebas y calidad

```bash
php artisan test
vendor/bin/pint --test
vendor/bin/phpstan analyse
npm run build
```

- **47 pruebas** de feature sobre autenticación, permisos, CRUD, contratos, parqueo y reportes.
- Las pruebas usan SQLite en memoria, por lo que no requieren MySQL.
- Larastan analiza `app/` en nivel 6.
- El pipeline de GitHub Actions ejecuta pruebas en PHP 8.3–8.5, migraciones sobre MySQL 8, análisis estático, build de assets y auditoría de dependencias.

## Seguridad

- Autenticación por sesión con contraseñas hasheadas (bcrypt).
- Límite de 5 intentos de login por minuto.
- Los usuarios inactivos no pueden iniciar sesión.
- Middleware de rol en todas las rutas protegidas.
- El administrador no puede crear ni modificar superadministradores, ni desactivar su propia cuenta.
- Validación con Form Requests y consultas parametrizadas vía Eloquent.
- Protección CSRF en todos los formularios.
- Cookies de sesión regeneradas al iniciar sesión.

## Base de datos para entrega

Las migraciones son la fuente de verdad del esquema. Para regenerar el volcado SQL:

```bash
docker exec parqueadero-db mysqldump -uroot -proot --databases parqueadero \
  --add-drop-database --routines --skip-comments > database/parqueadero.sql
```

También puedes importar directamente `database/parqueadero.sql` en un MySQL vacío.

## Limitaciones conocidas

- No hay registro público ni recuperación de contraseña; los usuarios los crea un administrador.
- No se envían correos ni notificaciones.
- Los reportes se generan al momento; solo se guarda su metadata, no el archivo.
- No hay aplicación móvil.
- El volcado `database/parqueadero.sql` es una foto del esquema y los datos demo; para desarrollo usa migraciones y seeders.

## Licencia

MIT.
