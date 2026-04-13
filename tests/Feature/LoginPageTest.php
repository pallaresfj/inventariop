<?php

test('the login page uses the redesigned institutional layout', function (): void {
    $response = $this->get(route('filament.admin.auth.login'));

    $response->assertOk()
        ->assertSee('Acceso al panel de inventario')
        ->assertSee('Beneficios y alcance')
        ->assertSee('Usuario o correo')
        ->assertSee('Volver al inicio')
        ->assertSee('href="'.url('/').'"', false);
});
