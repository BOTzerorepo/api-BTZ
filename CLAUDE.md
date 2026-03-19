# CLAUDE.md — api-BTZ

## Proyecto

API REST para **BOTzero** — sistema de gestión logística (cargas, contenedores, transportes, tracking satelital).

- **Stack**: Laravel 9, PHP 8.1+, MySQL, JWT Auth (tymon/jwt-auth), Spatie Permission
- **Branch principal de trabajo**: `v2-upgrades`
- **Servidor sandbox**: `212.85.22.34` — rama `v2-upgrades`, DB `totaltradevpsv2`
- **Frontend relacionado**: `../front-web` (PHP/vanilla JS, sin framework)

## Convenciones de respuesta de la API

Todas las respuestas siguen el formato estándar:

```json
{ "success": bool, "code": "STRING_CODE", "message": "...", "errors": {} }
```

Códigos de error definidos en `app/Exceptions/Handler.php`:
- `TOKEN_EXPIRED` / `TOKEN_INVALID` / `UNAUTHORIZED` → 401
- `VALIDATION_ERROR` → 422
- `NOT_FOUND` / `ROUTE_NOT_FOUND` → 404
- `METHOD_NOT_ALLOWED` → 405
- `SERVER_ERROR` → 500
- `INVALID_CREDENTIALS` → 401 (solo login)

Usar el trait `app/Traits/ApiResponse.php` en controllers para respuestas consistentes.

## Autenticación

- JWT via `tymon/jwt-auth`, guard `api`
- Login devuelve: `token`, `id`, `username`, `email`, `company`, `role` (string), `permiso` (array de nombres de permisos), `transport_id`, `cliente_id`
- `role` = string del rol Spatie (ej: `'Traffic'`, `'Customer'`, `'Master'`, `'Transport'`, `'ClienteEmpresa'`)
- `permiso` = array de permisos del rol (ej: `['ver_cargas', 'editar_cargas', ...]`)

## Roles disponibles

`Traffic`, `Customer`, `Master`, `Transport`, `ClienteEmpresa`

## Tests

- Framework: PHPUnit con `php artisan test`
- Archivos en `tests/Feature/`: `AuthTest`, `CargaTest`, `CntrTest`, `ErrorHandlingTest`, `MaestrosTest`, `TransportTest`, `UserTest`
- Helper de autenticación JWT: `tests/Feature/Traits/WithJwtAuth.php`
- Usar `actingAsRole('Traffic')` para autenticarse como rol en tests
- Usar `authHeaders($user)` para autenticarse como usuario específico

### Base de datos para tests

Los tests usan `RefreshDatabase` y requieren **MySQL local** (no SQLite — las migraciones tienen sintaxis MySQL-específica).

Configurar en `phpunit.xml`:
```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_HOST" value="127.0.0.1"/>
<env name="DB_PORT" value="3306"/>
<env name="DB_DATABASE" value="botzero_test"/>
<env name="DB_USERNAME" value="root"/>
<env name="DB_PASSWORD" value=""/>
```

Setup inicial:
```bash
# Iniciar MySQL (XAMPP en macOS: /Applications/XAMPP)
/Applications/XAMPP/xamppfiles/bin/mysql -u root -e "CREATE DATABASE botzero_test;"
php artisan migrate --env=testing
```

Correr tests sin warnings de deprecación:
```bash
php -d error_reporting=24575 artisan test 2>/dev/null
```

## Estructura relevante

```
app/
  Exceptions/Handler.php       # Manejo centralizado de errores API
  Traits/ApiResponse.php       # Trait para respuestas estándar en controllers
  Http/Controllers/
    AuthController.php         # Login, logout, register, getAuthenticatedUser
    UserController.php         # CRUD usuarios
    RolePermissionController.php
    cargaController.php
    cntrController.php
    TransportController.php
    ...
  Models/
    User.php                   # campos: id, username, pass, empresa, transport_id, cliente_id
    ...
database/
  factories/
    UserFactory.php            # campo 'pass' (no 'password'), empresa requerida
tests/
  Feature/
    Traits/WithJwtAuth.php
```

## Comandos útiles

```bash
# Servidor local
php artisan serve

# Limpiar cachés
php artisan config:clear && php artisan cache:clear && php artisan route:clear

# Tests
php -d error_reporting=24575 artisan test 2>/dev/null
php -d error_reporting=24575 artisan test --filter=AuthTest 2>/dev/null

# Migraciones
php artisan migrate
php artisan migrate --env=testing
```

## Notas importantes

- El campo de contraseña en la tabla `users` es `pass`, no `password`
- `UserFactory` usa `bcrypt()` sobre el campo `pass`
- Las deprecation warnings son de PHP 8.2 vs Laravel 9 (vendor code) — no afectan el funcionamiento
- No commitear `.env` — contiene credenciales de producción
