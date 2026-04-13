<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Inventario Parroquial') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600,700|instrument-sans:400,500,600,700" rel="stylesheet" />

        <style>
            :root {
                --primary: #2e4a7d;
                --success: #2e7d32;
                --info: #1f2a44;
                --warning: #c9a646;
                --danger: #b91c1c;
                --ink: #182033;
                --muted: #5c6578;
                --line: #d8dee8;
                --paper: #f5f7fb;
                --white: #ffffff;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: 'Instrument Sans', sans-serif;
                color: var(--ink);
                background:
                    radial-gradient(80rem 40rem at 100% 0%, rgba(46, 74, 125, 0.15), transparent 60%),
                    radial-gradient(60rem 30rem at 0% 35%, rgba(201, 166, 70, 0.16), transparent 60%),
                    var(--paper);
                min-height: 100vh;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .container {
                width: min(1160px, calc(100% - 2rem));
                margin: 0 auto;
            }

            .topbar {
                position: sticky;
                top: 0;
                z-index: 20;
                backdrop-filter: blur(8px);
                background: color-mix(in srgb, var(--paper) 86%, transparent);
                border-bottom: 1px solid color-mix(in srgb, var(--line) 65%, transparent);
            }

            .topbar-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.9rem 0;
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 0.7rem;
                font-weight: 700;
                color: var(--info);
            }

            .brand-mark {
                display: inline-grid;
                place-items: center;
                width: 2.1rem;
                height: 2.1rem;
                border-radius: 999px;
                background: linear-gradient(135deg, var(--primary), var(--info));
                color: var(--white);
                font-size: 0.78rem;
                font-weight: 700;
                letter-spacing: 0.03em;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.45rem;
                border-radius: 999px;
                padding: 0.72rem 1.1rem;
                border: 1px solid transparent;
                font-weight: 600;
                font-size: 0.93rem;
                transition: transform 150ms ease, box-shadow 150ms ease, background-color 150ms ease;
            }

            .btn:hover {
                transform: translateY(-1px);
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--primary), var(--info));
                color: var(--white);
                box-shadow: 0 10px 20px -14px rgba(31, 42, 68, 0.7);
            }

            .btn-primary:hover {
                box-shadow: 0 14px 24px -14px rgba(31, 42, 68, 0.85);
            }

            .btn-soft {
                background: color-mix(in srgb, var(--white) 70%, var(--warning));
                border-color: color-mix(in srgb, var(--warning) 55%, var(--line));
                color: var(--info);
            }

            .hero {
                padding: 4.4rem 0 2.5rem;
            }

            .hero-grid {
                display: grid;
                grid-template-columns: 1.15fr 0.85fr;
                gap: 1.3rem;
                align-items: stretch;
            }

            .hero-main {
                background: var(--white);
                border: 1px solid var(--line);
                border-radius: 1.45rem;
                padding: clamp(1.35rem, 3vw, 2.4rem);
                box-shadow: 0 20px 44px -34px rgba(31, 42, 68, 0.45);
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                background: color-mix(in srgb, var(--primary) 10%, var(--white));
                border: 1px solid color-mix(in srgb, var(--primary) 28%, var(--line));
                color: var(--primary);
                border-radius: 999px;
                padding: 0.32rem 0.74rem;
                font-size: 0.78rem;
                font-weight: 700;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }

            .hero h1 {
                margin: 1rem 0 0;
                font-family: 'Cormorant Garamond', serif;
                font-size: clamp(2rem, 5.4vw, 3.35rem);
                line-height: 1.03;
                color: var(--info);
            }

            .hero p {
                margin: 1rem 0 0;
                color: var(--muted);
                max-width: 62ch;
                line-height: 1.65;
                font-size: 1.02rem;
            }

            .hero-actions {
                margin-top: 1.5rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .hero-side {
                border-radius: 1.45rem;
                padding: 1.3rem;
                border: 1px solid color-mix(in srgb, var(--warning) 38%, var(--line));
                background:
                    linear-gradient(165deg, color-mix(in srgb, var(--warning) 13%, var(--white)) 0%, var(--white) 66%),
                    var(--white);
                box-shadow: 0 20px 44px -34px rgba(31, 42, 68, 0.45);
            }

            .hero-side h2 {
                margin: 0;
                font-size: 1.07rem;
                color: var(--info);
            }

            .hero-side ul {
                margin: 1rem 0 0;
                padding: 0;
                list-style: none;
                display: grid;
                gap: 0.62rem;
            }

            .hero-side li {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                color: #26334f;
                font-size: 0.94rem;
            }

            .dot {
                width: 0.54rem;
                height: 0.54rem;
                border-radius: 999px;
                background: var(--warning);
                flex: 0 0 auto;
            }

            .section {
                padding: 1.3rem 0 0.8rem;
            }

            .section-title {
                margin: 0;
                font-family: 'Cormorant Garamond', serif;
                font-size: clamp(1.7rem, 4vw, 2.35rem);
                color: var(--info);
            }

            .section-subtitle {
                margin: 0.45rem 0 0;
                color: var(--muted);
                line-height: 1.6;
                max-width: 70ch;
            }

            .cards {
                margin-top: 1.1rem;
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.88rem;
            }

            .card {
                background: var(--white);
                border: 1px solid var(--line);
                border-radius: 1rem;
                padding: 1rem;
                box-shadow: 0 12px 26px -26px rgba(31, 42, 68, 0.8);
            }

            .card h3 {
                margin: 0;
                font-size: 1rem;
                color: var(--info);
            }

            .card p {
                margin: 0.46rem 0 0;
                color: var(--muted);
                line-height: 1.55;
                font-size: 0.92rem;
            }

            .card-accent {
                width: 2.3rem;
                height: 0.2rem;
                border-radius: 999px;
                background: linear-gradient(90deg, var(--primary), var(--warning));
                margin-bottom: 0.62rem;
            }

            .roles {
                margin-top: 1.1rem;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.88rem;
            }

            .role-item {
                background: color-mix(in srgb, var(--white) 80%, var(--primary));
                border: 1px solid color-mix(in srgb, var(--primary) 30%, var(--line));
                border-radius: 1rem;
                padding: 1rem;
            }

            .role-item h3 {
                margin: 0;
                font-size: 0.97rem;
                color: var(--info);
            }

            .role-item p {
                margin: 0.42rem 0 0;
                font-size: 0.9rem;
                color: var(--muted);
                line-height: 1.5;
            }

            .cta {
                margin: 2.1rem auto 3.1rem;
                border-radius: 1.3rem;
                border: 1px solid color-mix(in srgb, var(--success) 40%, var(--line));
                background:
                    radial-gradient(70rem 20rem at 100% 0%, color-mix(in srgb, var(--success) 14%, var(--white)), transparent 64%),
                    linear-gradient(160deg, color-mix(in srgb, var(--white) 84%, var(--success)) 0%, var(--white) 66%);
                padding: 1.4rem;
                text-align: center;
            }

            .cta h2 {
                margin: 0;
                font-family: 'Cormorant Garamond', serif;
                font-size: clamp(1.65rem, 4vw, 2.3rem);
                color: var(--info);
            }

            .cta p {
                margin: 0.6rem auto 0;
                max-width: 64ch;
                color: var(--muted);
                line-height: 1.6;
            }

            .cta .btn {
                margin-top: 1rem;
            }

            .footer {
                border-top: 1px solid var(--line);
                padding: 0.9rem 0 1.5rem;
                color: #6a7488;
                font-size: 0.84rem;
                text-align: center;
            }

            @media (max-width: 980px) {
                .hero {
                    padding-top: 3.2rem;
                }

                .hero-grid {
                    grid-template-columns: 1fr;
                }

                .cards {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 640px) {
                .topbar-row {
                    padding: 0.75rem 0;
                }

                .brand span:last-child {
                    font-size: 0.9rem;
                }

                .btn {
                    width: 100%;
                }

                .hero-actions {
                    width: 100%;
                }

                .cards,
                .roles {
                    grid-template-columns: 1fr;
                }

                .hero-main,
                .hero-side,
                .cta {
                    border-radius: 1rem;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .btn,
                .card {
                    transition: none;
                }
            }
        </style>
    </head>
    <body>
        @php($loginUrl = route('filament.admin.auth.login'))

        <header class="topbar">
            <div class="container topbar-row">
                <a href="{{ url('/') }}" class="brand" aria-label="Inicio">
                    <span class="brand-mark">IP</span>
                    <span>{{ config('app.name', 'Inventario Parroquial') }}</span>
                </a>

                <a href="{{ $loginUrl }}" class="btn btn-primary">Ingresar</a>
            </div>
        </header>

        <main>
            <section class="hero">
                <div class="container hero-grid">
                    <article class="hero-main">
                        <span class="eyebrow">Gestion patrimonial</span>
                        <h1>Control integral del inventario parroquial en un solo sistema.</h1>
                        <p>
                            Inventario Parroquial centraliza el registro, seguimiento y trazabilidad de bienes eclesiales por arciprestazgo,
                            parroquia y comunidad. Facilita la consulta historica, el control de restauraciones y la operacion por niveles de acceso.
                        </p>
                        <div class="hero-actions">
                            <a href="{{ $loginUrl }}" class="btn btn-primary">Ingresar al sistema</a>
                            <a href="#modulos" class="btn btn-soft">Ver modulos</a>
                        </div>
                    </article>

                    <aside class="hero-side" aria-label="Beneficios clave">
                        <h2>Lo esencial en el dia a dia</h2>
                        <ul>
                            <li><span class="dot" aria-hidden="true"></span>Inventario de articulos con estado, valor y fecha de adquisicion.</li>
                            <li><span class="dot" aria-hidden="true"></span>Registro de restauraciones con costo y evidencia.</li>
                            <li><span class="dot" aria-hidden="true"></span>Estructura territorial: arciprestazgos, parroquias y comunidades.</li>
                            <li><span class="dot" aria-hidden="true"></span>Gestion pastoral: sacerdotes, titulos, cargos y asignaciones.</li>
                            <li><span class="dot" aria-hidden="true"></span>Acceso por rol y alcance para proteger la informacion.</li>
                        </ul>
                    </aside>
                </div>
            </section>

            <section id="modulos" class="section">
                <div class="container">
                    <h2 class="section-title">Que puedes gestionar</h2>
                    <p class="section-subtitle">
                        El sistema organiza la informacion institucional en modulos conectados para mantener coherencia operativa y trazabilidad.
                    </p>

                    <div class="cards">
                        <article class="card">
                            <div class="card-accent" aria-hidden="true"></div>
                            <h3>Arciprestazgos y parroquias</h3>
                            <p>Estructura de jurisdiccion para administrar inventario y usuarios por territorio.</p>
                        </article>
                        <article class="card">
                            <div class="card-accent" aria-hidden="true"></div>
                            <h3>Comunidades y articulos</h3>
                            <p>Catalogo de bienes con descripcion, estado, valor economico y control de actividad.</p>
                        </article>
                        <article class="card">
                            <div class="card-accent" aria-hidden="true"></div>
                            <h3>Restauraciones</h3>
                            <p>Historial de intervenciones, costos acumulados y evidencias asociadas a cada articulo.</p>
                        </article>
                        <article class="card">
                            <div class="card-accent" aria-hidden="true"></div>
                            <h3>Sacerdotes</h3>
                            <p>Base de datos pastoral con titulos, curriculum y datos de contacto.</p>
                        </article>
                        <article class="card">
                            <div class="card-accent" aria-hidden="true"></div>
                            <h3>Asignaciones parroquiales</h3>
                            <p>Relacion entre sacerdote, parroquia y cargo, con control de vigencia.</p>
                        </article>
                        <article class="card">
                            <div class="card-accent" aria-hidden="true"></div>
                            <h3>Seguridad y auditoria</h3>
                            <p>Usuarios, roles y permisos para gobierno de acceso segun responsabilidades institucionales.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section">
                <div class="container">
                    <h2 class="section-title">Acceso por roles</h2>
                    <p class="section-subtitle">
                        Cada perfil visualiza solo la informacion de su alcance, garantizando control y confidencialidad de datos.
                    </p>

                    <div class="roles">
                        <article class="role-item">
                            <h3>Soporte tecnico</h3>
                            <p>Administracion global, configuracion del sistema y gestion completa de seguridad.</p>
                        </article>
                        <article class="role-item">
                            <h3>Gestor diocesano</h3>
                            <p>Supervision de inventario y operacion en su arciprestazgo con visibilidad consolidada.</p>
                        </article>
                        <article class="role-item">
                            <h3>Gestor parroquial</h3>
                            <p>Operacion de bienes, comunidades y asignaciones en su parroquia.</p>
                        </article>
                        <article class="role-item">
                            <h3>Gestor comunitario</h3>
                            <p>Actualizacion de articulos y seguimiento local dentro de su comunidad.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="container cta">
                <h2>Listo para ingresar al panel administrativo</h2>
                <p>
                    Accede al sistema para administrar registros, consultar indicadores y mantener actualizado el inventario institucional.
                </p>
                <a href="{{ $loginUrl }}" class="btn btn-primary">Ingresar al login</a>
            </section>
        </main>

        <footer class="footer">
            <div class="container">{{ config('app.name', 'Inventario Parroquial') }} · Plataforma de gestion institucional</div>
        </footer>
    </body>
</html>
