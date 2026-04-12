# Inventory Parish Migration (Laravel 13 + Filament 5 + Livewire 4 + Shield)

## Required Legacy Artifact

- `legacy/sql/asyservi_sc_inventariop.sql`

If the SQL file is missing, import commands stop and report the missing artifact.

## Recommended Workflow

1. Configure MySQL/MariaDB credentials in `.env`.
2. Run schema and base seeders:
   - `php artisan migrate:fresh --seed`
3. Import legacy core data:
   - `php artisan inventory:import legacy/sql/asyservi_sc_inventariop.sql`
4. Migrate image BLOBs to `storage`:
   - `php artisan inventory:import-media legacy/sql/asyservi_sc_inventariop.sql`
5. Reconcile table counts:
   - `php artisan inventory:reconcile legacy/sql/asyservi_sc_inventariop.sql --strict`
6. Create or refresh support credentials:
   - `php artisan inventory:bootstrap-admin support support@inventariop.local ChangeMe123!`

## Migrated Credential Security

- Legacy users are imported with a new random password.
- Legacy MD5 hash is kept only as temporary reference (`legacy_password_md5`).
- `force_password_reset=true` blocks panel navigation until password is updated.
- To force extra resets:
  - All users: `php artisan inventory:force-password-reset --all`
  - Single user: `php artisan inventory:force-password-reset <id|username|email>`

## Initial Roles

- `technical_support`
- `diocese_manager`
- `parish_manager`
- `community_manager`

Roles are created in `InventoryRolesSeeder` and synchronized with Shield permissions.

## Notes

- Panel login supports `username` or `email`.
- Context scoping (deanery/parish/community) is applied in Filament resource queries.
- Migrated images are stored under `storage/app/public/inventory-legacy/...`.
