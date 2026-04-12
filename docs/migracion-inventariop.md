# Migración Inventario Parroquial (Laravel 13 + Filament 5 + Livewire 4 + Shield)

## Estructura legado requerida

- `legacy/sql/asyservi_sc_inventariop.sql`

Si falta el SQL, los comandos de importación se detienen y reportan faltantes.

## Flujo recomendado

1. Preparar base de datos MySQL/MariaDB en `.env`.
2. Ejecutar migraciones y seed inicial:
   - `php artisan migrate:fresh --seed`
3. Importar datos legado:
   - `php artisan inventario:import legacy/sql/asyservi_sc_inventariop.sql`
4. Migrar imágenes BLOB a `storage`:
   - `php artisan inventario:import-media legacy/sql/asyservi_sc_inventariop.sql`
5. Conciliar conteos:
   - `php artisan inventario:reconcile legacy/sql/asyservi_sc_inventariop.sql --strict`
6. Crear o renovar credenciales de soporte:
   - `php artisan inventario:bootstrap-admin soporte soporte@inventariop.local Cambiar123!`

## Seguridad de credenciales migradas

- Usuarios legado se importan con contraseña aleatoria nueva.
- Se conserva hash MD5 legado solo como referencia temporal (`legacy_password_md5`).
- `force_password_reset=true` bloquea navegación del panel hasta actualizar contraseña en perfil.
- Para forzar reset adicional:
  - Todos: `php artisan inventario:force-password-reset --all`
  - Usuario puntual: `php artisan inventario:force-password-reset <id|username|email>`

## Roles iniciales

- `soporte_tecnico`
- `gestor_diocesis`
- `gestor_parroquia`
- `gestor_comunidad`

Se crean en `InventoryRolesSeeder` y se sincronizan permisos Shield.

## Notas

- El login del panel acepta `username` o correo.
- El filtrado por contexto (diocesis/parroquia/comunidad) se aplica en queries de recursos Filament.
- Imágenes migradas quedan en `storage/app/public/inventario-legacy/...`.
