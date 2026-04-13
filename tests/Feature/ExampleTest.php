<?php

test('the home page explains the app and exposes login access', function (): void {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Control integral del inventario parroquial')
        ->assertSee('Que puedes gestionar')
        ->assertSee(route('filament.admin.auth.login', absolute: false));
});
