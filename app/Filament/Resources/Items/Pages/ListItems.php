<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use App\Models\User;
use App\Services\ItemXlsxImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    private static function canUseBulkImportActions(): bool
    {
        $user = auth()->user();

        return $user instanceof User
            && $user->isTechnicalSupport()
            && ItemResource::canCreate();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('downloadCommunityCatalogXlsx')
                ->label('Descargar catalogo de comunidades')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn (): bool => self::canUseBulkImportActions())
                ->action(function (): BinaryFileResponse {
                    abort_unless(self::canUseBulkImportActions(), 403);

                    $user = auth()->user();

                    abort_unless($user instanceof User, 403);

                    return app(ItemXlsxImportService::class)->createCommunityCatalogDownloadResponse($user);
                }),
            Action::make('downloadItemsXlsxTemplate')
                ->label('Descargar plantilla XLSX')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn (): bool => self::canUseBulkImportActions())
                ->action(function (): BinaryFileResponse {
                    abort_unless(self::canUseBulkImportActions(), 403);

                    return app(ItemXlsxImportService::class)->createTemplateDownloadResponse();
                }),
            Action::make('importItemsFromXlsx')
                ->label('Importar articulos')
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(fn (): bool => self::canUseBulkImportActions())
                ->modalHeading('Importar articulos desde XLSX')
                ->modalDescription('Usa la accion "Descargar catalogo de comunidades" para consultar el community_id permitido.')
                ->modalSubmitActionLabel('Importar')
                ->form([
                    FileUpload::make('file')
                        ->label('Archivo XLSX')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    if (! self::canUseBulkImportActions()) {
                        Notification::make()
                            ->title('No tienes permisos para usar la carga masiva de articulos.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $user = auth()->user();

                    if (! $user instanceof User) {
                        Notification::make()
                            ->title('No fue posible identificar el usuario autenticado.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $uploadedFile = $data['file'] ?? null;

                    if (! $uploadedFile instanceof TemporaryUploadedFile) {
                        Notification::make()
                            ->title('Selecciona un archivo XLSX valido para continuar.')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $result = app(ItemXlsxImportService::class)->import(
                            $user,
                            $uploadedFile->getRealPath(),
                        );

                        $notification = Notification::make()
                            ->title('Importacion finalizada')
                            ->body(
                                "Total: {$result['total']} | Creadas: {$result['created']} | Fallidas: {$result['failed']}"
                            );

                        if ($result['failed'] > 0) {
                            $notification->warning();

                            if (filled($result['failure_report_path'])) {
                                $downloadUrl = URL::temporarySignedRoute(
                                    'items.import-failures.download',
                                    now()->addMinutes(30),
                                    [
                                        'file' => basename((string) $result['failure_report_path']),
                                        'owner' => $user->getAuthIdentifier(),
                                    ],
                                );

                                $notification->actions([
                                    NotificationAction::make('downloadImportFailures')
                                        ->label('Descargar errores')
                                        ->url($downloadUrl, shouldOpenInNewTab: true),
                                ]);
                            }
                        } else {
                            $notification->success();
                        }

                        $notification->send();
                    } catch (ValidationException $exception) {
                        Notification::make()
                            ->title('No se pudo procesar el archivo XLSX')
                            ->body(implode(' | ', $exception->errors()['file'] ?? $exception->errors()['*'] ?? ['Valida el archivo e intenta de nuevo.']))
                            ->danger()
                            ->send();
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('Error inesperado durante la importacion')
                            ->body('Intenta nuevamente. Si el error persiste, contacta al administrador.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
