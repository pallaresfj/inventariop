<?php

namespace App\Providers\Filament;

use App\Filament\Auth\EditProfile;
use App\Filament\Auth\Login;
use App\Filament\Widgets\InventoryHighlights;
use App\Filament\Widgets\InventoryOverview;
use App\Http\Middleware\ForcePasswordReset;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Enums\UserMenuPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->passwordReset()
            ->profile(EditProfile::class)
            ->userMenu(position: UserMenuPosition::Sidebar)
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => '#2E4A7D',
                'success' => '#2E7D32',
                'info' => '#1F2A44',
                'warning' => '#C9A646',
                'danger' => '#B91C1C',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                InventoryHighlights::class,
                InventoryOverview::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): View => view('filament.admin.mobile-actions'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): View => view('filament.admin.dashboard-styles'),
            )
            ->bootUsing(function (): void {
                Action::configureUsing(function (Action $action): void {
                    if ($action instanceof EditAction || $action instanceof DeleteAction || $action instanceof ViewAction || $action instanceof ReplicateAction) {
                        $tooltip = match (true) {
                            $action instanceof EditAction => 'Editar',
                            $action instanceof DeleteAction => 'Eliminar',
                            $action instanceof ViewAction => 'Ver',
                            $action instanceof ReplicateAction => 'Duplicar',
                            default => null,
                        };

                        $action
                            ->iconButton()
                            ->size(Size::Large)
                            ->iconSize(IconSize::Large)
                            ->tooltip($tooltip);
                    }

                    if ($action instanceof CreateAction || in_array($action->getName(), ['save', 'cancel'], true)) {
                        $action->extraAttributes(['class' => 'fi-mobile-wide-action'], merge: true);
                    }
                });
            })
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('Seguridad')
                    ->navigationLabel('Roles')
                    ->navigationIcon('heroicon-o-shield-check')
                    ->navigationSort(20),
            ])
            ->authMiddleware([
                Authenticate::class,
                ForcePasswordReset::class,
            ]);
    }
}
