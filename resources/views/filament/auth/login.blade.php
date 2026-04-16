<x-filament-panels::page.simple :heading="null" :subheading="null">
    <style>
        .fi-simple-main.fi-width-lg {
            max-width: min(1160px, 100%);
        }

        .inv-login {
            --inv-primary: #2e4a7d;
            --inv-success: #2e7d32;
            --inv-info: #1f2a44;
            --inv-warning: #c9a646;
            --inv-danger: #b91c1c;
            --inv-line: #d7deea;
            --inv-muted: #5e6678;
            --inv-paper: #f3f6fb;

            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 1rem;
            align-items: stretch;
            margin: 0 auto;
        }

        .inv-login-main,
        .inv-login-side {
            border-radius: 1.15rem;
            border: 1px solid var(--inv-line);
            box-shadow: 0 18px 36px -30px rgba(31, 42, 68, 0.66);
            overflow: hidden;
        }

        .inv-login-main {
            background: linear-gradient(165deg, #ffffff 0%, var(--inv-paper) 100%);
            padding: 1.2rem;
        }

        .inv-login-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            color: var(--inv-info);
            font-weight: 700;
        }

        .inv-login-mark {
            width: 2rem;
            height: 2rem;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            font-size: 0.76rem;
            letter-spacing: 0.03em;
            color: #fff;
            background: linear-gradient(135deg, var(--inv-primary), var(--inv-info));
        }

        .inv-login-head {
            margin-top: 1rem;
        }

        .inv-login-head h1 {
            margin: 0;
            font-size: clamp(1.38rem, 2.2vw, 1.95rem);
            color: var(--inv-info);
        }

        .inv-login-head p {
            margin: 0.45rem 0 0;
            color: var(--inv-muted);
            line-height: 1.55;
        }

        .inv-login-form {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 0.95rem;
            border: 1px solid color-mix(in srgb, var(--inv-primary) 20%, var(--inv-line));
            background: #ffffff;
        }

        .inv-login-home-link {
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid color-mix(in srgb, var(--inv-warning) 58%, var(--inv-line));
            background: color-mix(in srgb, #ffffff 72%, var(--inv-warning));
            color: var(--inv-info);
            font-weight: 600;
            padding: 0.62rem 0.98rem;
            transition: transform 150ms ease, box-shadow 150ms ease;
        }

        .inv-login-home-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 26px -18px rgba(31, 42, 68, 0.5);
        }

        .inv-login-side {
            background:
                radial-gradient(130% 95% at 100% 0%, color-mix(in srgb, var(--inv-warning) 22%, #ffffff), transparent 60%),
                radial-gradient(120% 90% at 0% 100%, color-mix(in srgb, var(--inv-primary) 16%, #ffffff), transparent 58%),
                #ffffff;
            padding: 1.25rem;
        }

        .inv-login-side h2 {
            margin: 0;
            color: var(--inv-info);
            font-size: clamp(1.1rem, 1.45vw, 1.4rem);
        }

        .inv-login-side p {
            margin: 0.55rem 0 0;
            color: var(--inv-muted);
            line-height: 1.55;
        }

        .inv-login-side-grid {
            margin-top: 1rem;
            display: grid;
            gap: 0.62rem;
        }

        .inv-login-pill {
            border: 1px solid color-mix(in srgb, var(--inv-info) 18%, var(--inv-line));
            background: color-mix(in srgb, #ffffff 72%, var(--inv-primary));
            border-radius: 0.8rem;
            padding: 0.78rem;
        }

        .inv-login-pill h3 {
            margin: 0;
            color: var(--inv-info);
            font-size: 0.94rem;
        }

        .inv-login-pill p {
            margin: 0.34rem 0 0;
            font-size: 0.86rem;
            line-height: 1.5;
        }

        .inv-login-roles {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.58rem;
        }

        .inv-login-role {
            border-radius: 0.7rem;
            border: 1px solid color-mix(in srgb, var(--inv-success) 28%, var(--inv-line));
            background: color-mix(in srgb, #ffffff 82%, var(--inv-success));
            padding: 0.62rem;
            color: var(--inv-info);
            font-size: 0.82rem;
            font-weight: 600;
        }

        .dark .inv-login {
            --inv-primary: #7f99d4;
            --inv-success: #63b175;
            --inv-info: #d7e4ff;
            --inv-warning: #d7bd6b;
            --inv-danger: #e27d7d;
            --inv-line: #2d3a55;
            --inv-muted: #a8b5d0;
            --inv-paper: #0f1728;
        }

        .dark .inv-login-main,
        .dark .inv-login-side {
            border-color: var(--inv-line);
            box-shadow: 0 24px 42px -30px rgba(0, 0, 0, 0.9);
        }

        .dark .inv-login-main {
            background: linear-gradient(165deg, #0f1729 0%, #111d34 100%);
        }

        .dark .inv-login-form {
            border-color: color-mix(in srgb, var(--inv-primary) 24%, var(--inv-line));
            background: #0b1426;
        }

        .dark .inv-login-home-link {
            border-color: color-mix(in srgb, var(--inv-warning) 55%, var(--inv-line));
            background: color-mix(in srgb, #0f1729 72%, var(--inv-warning));
            color: var(--inv-info);
        }

        .dark .inv-login-home-link:hover {
            box-shadow: 0 14px 24px -18px rgba(0, 0, 0, 0.75);
        }

        .dark .inv-login-side {
            background:
                radial-gradient(130% 95% at 100% 0%, color-mix(in srgb, var(--inv-warning) 18%, #0f1729), transparent 60%),
                radial-gradient(120% 90% at 0% 100%, color-mix(in srgb, var(--inv-primary) 22%, #0f1729), transparent 58%),
                #0f1729;
        }

        .dark .inv-login-pill {
            border-color: color-mix(in srgb, var(--inv-primary) 24%, var(--inv-line));
            background: color-mix(in srgb, #0f1729 72%, var(--inv-primary));
        }

        .dark .inv-login-role {
            border-color: color-mix(in srgb, var(--inv-success) 35%, var(--inv-line));
            background: color-mix(in srgb, #0f1729 76%, var(--inv-success));
            color: #dce9ff;
        }

        .dark .inv-login-form .fi-fo-field-label,
        .dark .inv-login-form .fi-input-wrp-label {
            color: #c8d5ef;
        }

        .dark .inv-login-form .fi-fo-field-helper-text,
        .dark .inv-login-form .fi-fo-field-wrp-hint {
            color: #97a9cb;
        }

        .dark .inv-login-form .fi-fo-field-wrp-error-message,
        .dark .inv-login-form .fi-fo-field-wrp-error-list {
            color: #f6b9b9;
        }

        .dark .inv-login-form .fi-input-wrp {
            border-color: color-mix(in srgb, var(--inv-primary) 30%, var(--inv-line));
            background: color-mix(in srgb, #0f1729 90%, #1a2742);
        }

        .dark .inv-login-form .fi-input-wrp:focus-within {
            border-color: color-mix(in srgb, var(--inv-primary) 70%, #acc0ea);
            box-shadow: 0 0 0 1px color-mix(in srgb, var(--inv-primary) 55%, #acc0ea);
        }

        .dark .inv-login-form .fi-input {
            color: #edf3ff;
        }

        .dark .inv-login-form .fi-input::placeholder {
            color: #8ea1c5;
        }

        .dark .inv-login-form .fi-input-wrp-prefix,
        .dark .inv-login-form .fi-input-wrp-suffix,
        .dark .inv-login-form .fi-input-wrp-actions .fi-icon {
            color: #9cb0d4;
        }

        .dark .inv-login-form .fi-checkbox-input {
            border-color: color-mix(in srgb, var(--inv-primary) 44%, var(--inv-line));
            background-color: #0f1729;
            accent-color: var(--inv-primary);
        }

        .dark .inv-login-form .fi-checkbox-input:checked {
            border-color: var(--inv-primary);
            background-color: var(--inv-primary);
        }

        .dark .inv-login-form a,
        .dark .inv-login-form .fi-link {
            color: #9eb7ea;
        }

        .dark .inv-login-form .fi-btn {
            border-color: transparent;
            background: linear-gradient(135deg, var(--inv-primary), color-mix(in srgb, var(--inv-primary) 72%, #86a6ea));
            color: #f7fbff;
            box-shadow: 0 14px 26px -18px rgba(0, 0, 0, 0.8);
        }

        @media (max-width: 900px) {
            .inv-login {
                grid-template-columns: 1fr;
            }

            .inv-login-main,
            .inv-login-side {
                border-radius: 1rem;
            }
        }

        @media (max-width: 640px) {
            .inv-login-main,
            .inv-login-side {
                padding: 1rem;
            }

            .inv-login-home-link {
                width: 100%;
            }

            .inv-login-roles {
                grid-template-columns: 1fr;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .inv-login-home-link {
                transition: none;
            }

            .inv-login-home-link:hover {
                transform: none;
                box-shadow: none;
            }
        }
    </style>

    <section class="inv-login">
        <article class="inv-login-main">
            <div class="inv-login-brand">
                <span class="inv-login-mark">IP</span>
            </div>

            <header class="inv-login-head">
                <h1>Acceso al panel de inventario</h1>
                <p>
                    Ingresa con tu usuario o correo institucional para administrar articulos,
                    restauraciones y estructura pastoral segun tu rol.
                </p>
            </header>

            <div class="inv-login-form">
                {{ $this->content }}
            </div>

            <a href="{{ url('/') }}" class="inv-login-home-link">Volver al inicio</a>
        </article>

        <aside class="inv-login-side" aria-label="Beneficios y alcance">
            <h2>Beneficios y alcance</h2>
            <p>
                Esta plataforma integra informacion patrimonial y pastoral con visibilidad
                controlada por arciprestazgo, parroquia y comunidad.
            </p>

            <div class="inv-login-side-grid">
                <article class="inv-login-pill">
                    <h3>Inventario conectado</h3>
                    <p>Articulos, comunidades y parroquias en una misma trazabilidad operativa.</p>
                </article>
                <article class="inv-login-pill">
                    <h3>Control de restauraciones</h3>
                    <p>Seguimiento historico de intervenciones con costo y evidencia asociada.</p>
                </article>
                <article class="inv-login-pill">
                    <h3>Gestion pastoral</h3>
                    <p>Sacerdotes, cargos, titulos y asignaciones vigentes en contexto territorial.</p>
                </article>
            </div>

            <div class="inv-login-roles" aria-label="Roles de acceso">
                <div class="inv-login-role">Soporte tecnico</div>
                <div class="inv-login-role">Gestor diocesano</div>
                <div class="inv-login-role">Gestor parroquial</div>
                <div class="inv-login-role">Gestor comunitario</div>
            </div>
        </aside>
    </section>
</x-filament-panels::page.simple>
