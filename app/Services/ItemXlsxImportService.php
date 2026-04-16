<?php

declare(strict_types=1);

namespace App\Services;

use App\Filament\Resources\Items\Support\ItemScope;
use App\Models\Community;
use App\Models\Item;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\ValidationException;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ItemXlsxImportService
{
    use Conditionable;

    private const int MAX_IMPORT_ROWS = 500;

    private const string FAILURE_DIRECTORY = 'item-import-failures';

    private const string TEMPLATE_NAME = 'plantilla-articulos-importacion.xlsx';

    private const string COMMUNITY_CATALOG_NAME = 'catalogo-comunidades.xlsx';

    /** @var list<string> */
    private const array TEMPLATE_HEADERS = [
        'community_id',
        'name',
        'description',
        'condition',
        'price',
        'acquired_at',
        'is_active',
    ];

    /** @var list<string> */
    private const array REQUIRED_HEADERS = [
        'community_id',
        'name',
    ];

    /** @var list<string> */
    private const array COMMUNITY_CATALOG_HEADERS = [
        'community_id',
        'community_name',
        'parish_name',
    ];

    /** @var array<string, Community|null> */
    private array $communityVisibilityCache = [];

    public function createTemplateDownloadResponse(): BinaryFileResponse
    {
        $directory = storage_path('app/tmp');
        File::ensureDirectoryExists($directory);

        $path = $directory.'/item-import-template-'.Str::uuid().'.xlsx';

        $writer = new Writer;
        $writer->openToFile($path);

        try {
            $writer->addRow(Row::fromValues(self::TEMPLATE_HEADERS));
            $writer->addRow(Row::fromValues([
                1,
                'Custodia principal',
                'Descripcion opcional',
                'B',
                0,
                now()->toDateString(),
                1,
            ]));
        } finally {
            $writer->close();
        }

        return response()
            ->download($path, self::TEMPLATE_NAME)
            ->deleteFileAfterSend(true);
    }

    public function createCommunityCatalogDownloadResponse(User $user): BinaryFileResponse
    {
        $directory = storage_path('app/tmp');
        File::ensureDirectoryExists($directory);

        $path = $directory.'/community-catalog-'.Str::uuid().'.xlsx';

        $rows = Community::query()
            ->visibleTo($user)
            ->with('parish:id,name')
            ->get()
            ->sortBy(fn (Community $community): string => Str::lower((string) $community->parish?->name).'|'.Str::lower((string) $community->name))
            ->values();

        $writer = new Writer;
        $writer->openToFile($path);

        try {
            $writer->addRow(Row::fromValues(self::COMMUNITY_CATALOG_HEADERS));

            foreach ($rows as $community) {
                $writer->addRow(Row::fromValues([
                    (int) $community->getKey(),
                    $community->name,
                    $community->parish?->name,
                ]));
            }
        } finally {
            $writer->close();
        }

        return response()
            ->download($path, self::COMMUNITY_CATALOG_NAME)
            ->deleteFileAfterSend(true);
    }

    /**
     * @return array{total:int, created:int, failed:int, failed_rows:list<array{row:int, errors:string, data:array<string,mixed>}>, failure_report_path:?string}
     */
    public function import(User $user, string $filePath): array
    {
        $this->communityVisibilityCache = [];

        [$header, $rows] = $this->readTemplateRows($filePath);

        $this->validateHeaders($header);

        if (count($rows) > self::MAX_IMPORT_ROWS) {
            throw ValidationException::withMessages([
                'file' => ['La plantilla supera el limite de '.self::MAX_IMPORT_ROWS.' filas con datos.'],
            ]);
        }

        $created = 0;
        $failedRows = [];

        foreach ($rows as $row) {
            $validated = $this->validateRow($row['data']);

            if (! $validated['is_valid']) {
                $failedRows[] = [
                    'row' => $row['row'],
                    'errors' => $validated['errors'],
                    'data' => $validated['normalized_data'],
                ];

                continue;
            }

            /** @var array{community_id:int, name:string, description:?string, condition:?string, price:?int, acquired_at:?string, is_active:?bool} $rowData */
            $rowData = $validated['normalized_data'];

            $itemData = [
                'community_id' => $rowData['community_id'],
                'name' => $rowData['name'],
                'description' => $rowData['description'],
                'condition' => $rowData['condition'] ?: 'B',
                'price' => $rowData['price'] ?? 0,
                'acquired_at' => $rowData['acquired_at'],
                'is_active' => $rowData['is_active'] ?? true,
                'image_path' => null,
            ];

            $itemData = ItemScope::applyScopedValues($itemData, $user);

            $communityId = (int) ($itemData['community_id'] ?? 0);
            $community = $this->resolveVisibleCommunity($user, $communityId);

            if (! $community) {
                $failedRows[] = [
                    'row' => $row['row'],
                    'errors' => 'La comunidad no existe o no pertenece al alcance del usuario.',
                    'data' => $rowData,
                ];

                continue;
            }

            $itemData['parish_id'] = (int) $community->parish_id;
            $itemData['community_id'] = (int) $community->getKey();

            Item::query()->create($itemData);
            $created++;
        }

        $failureReportPath = $this->storeFailureReport($failedRows);

        return [
            'total' => count($rows),
            'created' => $created,
            'failed' => count($failedRows),
            'failed_rows' => $failedRows,
            'failure_report_path' => $failureReportPath,
        ];
    }

    /**
     * @return array{0:list<string>,1:list<array{row:int,data:array<string,mixed>}>}
     */
    private function readTemplateRows(string $filePath): array
    {
        $reader = new Reader;
        $reader->open($filePath);

        try {
            $firstSheet = null;

            foreach ($reader->getSheetIterator() as $sheet) {
                $firstSheet = $sheet;

                break;
            }

            if (! $firstSheet) {
                throw ValidationException::withMessages([
                    'file' => ['El archivo XLSX no contiene hojas para procesar.'],
                ]);
            }

            $header = [];
            $rows = [];
            $excelRowNumber = 0;

            foreach ($firstSheet->getRowIterator() as $row) {
                $excelRowNumber++;

                $values = $row->toArray();

                if ($excelRowNumber === 1) {
                    $header = $this->normalizeHeaderRow($values);

                    continue;
                }

                if ($this->rowIsEmpty($values)) {
                    continue;
                }

                $rows[] = [
                    'row' => $excelRowNumber,
                    'data' => $this->mapRowData($header, $values),
                ];
            }

            return [$header, $rows];
        } finally {
            $reader->close();
        }
    }

    /**
     * @param  array<int, mixed>  $values
     * @return list<string>
     */
    private function normalizeHeaderRow(array $values): array
    {
        $headers = [];

        foreach ($values as $index => $value) {
            $header = Str::of((string) $value)
                ->replace("\u{FEFF}", '')
                ->trim()
                ->lower()
                ->value();

            $headers[] = $header !== '' ? $header : "column_{$index}";
        }

        return $headers;
    }

    /**
     * @param  list<string>  $headers
     * @param  array<int, mixed>  $values
     * @return array<string, mixed>
     */
    private function mapRowData(array $headers, array $values): array
    {
        $mappedData = [];

        foreach (self::TEMPLATE_HEADERS as $column) {
            $columnIndex = array_search($column, $headers, true);

            $mappedData[$column] = is_int($columnIndex) ? ($values[$columnIndex] ?? null) : null;
        }

        return $mappedData;
    }

    /**
     * @param  list<string>  $header
     */
    private function validateHeaders(array $header): void
    {
        $missingHeaders = array_values(array_diff(self::REQUIRED_HEADERS, $header));

        if ($missingHeaders !== []) {
            throw ValidationException::withMessages([
                'file' => [
                    'Faltan columnas requeridas en la plantilla: '.implode(', ', $missingHeaders).'.',
                ],
            ]);
        }
    }

    /**
     * @param  array<int, mixed>  $values
     */
    private function rowIsEmpty(array $values): bool
    {
        foreach ($values as $value) {
            if ($value instanceof DateTimeInterface) {
                return false;
            }

            if (! blank($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $rowData
     * @return array{is_valid:bool,errors:string,normalized_data:array{community_id:int|string|null,name:string|null,description:?string,condition:?string,price:int|string|null,acquired_at:?string,is_active:bool|string|int|null}}
     */
    private function validateRow(array $rowData): array
    {
        $normalizedData = [
            'community_id' => $this->normalizeInteger($rowData['community_id'] ?? null),
            'name' => $this->normalizeString($rowData['name'] ?? null),
            'description' => $this->normalizeString($rowData['description'] ?? null),
            'condition' => $this->normalizeCondition($rowData['condition'] ?? null),
            'price' => $this->normalizeInteger($rowData['price'] ?? null),
            'acquired_at' => $this->normalizeDate($rowData['acquired_at'] ?? null),
            'is_active' => $this->normalizeBoolean($rowData['is_active'] ?? null),
        ];

        $validator = Validator::make($normalizedData, [
            'community_id' => ['required', 'integer', 'exists:communities,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'condition' => ['nullable', 'in:B,M,R'],
            'price' => ['nullable', 'integer', 'min:0'],
            'acquired_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return [
                'is_valid' => false,
                'errors' => implode(' | ', $validator->errors()->all()),
                'normalized_data' => $normalizedData,
            ];
        }

        /** @var array{community_id:int,name:string,description:?string,condition:?string,price:?int,acquired_at:?string,is_active:?bool} $validatedData */
        $validatedData = $validator->validated();

        return [
            'is_valid' => true,
            'errors' => '',
            'normalized_data' => $validatedData,
        ];
    }

    private function normalizeString(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (blank($value)) {
            return null;
        }

        return trim((string) $value);
    }

    private function normalizeCondition(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return Str::upper(trim((string) $value));
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (blank($value)) {
            return null;
        }

        return trim((string) $value);
    }

    private function normalizeBoolean(mixed $value): bool|string|int|null
    {
        if (blank($value)) {
            return null;
        }

        if (is_bool($value) || is_int($value)) {
            return $value;
        }

        $normalized = Str::lower(trim((string) $value));

        if (in_array($normalized, ['1', 'true', 'si', 'sí', 'yes'], true)) {
            return true;
        }

        if (in_array($normalized, ['0', 'false', 'no'], true)) {
            return false;
        }

        return trim((string) $value);
    }

    private function normalizeInteger(mixed $value): int|string|null
    {
        if (blank($value)) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return floor($value) === $value ? (int) $value : (string) $value;
        }

        $stringValue = trim((string) $value);
        $sanitized = str_replace([',', ' '], '', $stringValue);

        if (is_numeric($sanitized) && floor((float) $sanitized) === (float) $sanitized) {
            return (int) $sanitized;
        }

        return $stringValue;
    }

    private function resolveVisibleCommunity(User $user, int $communityId): ?Community
    {
        if ($communityId <= 0) {
            return null;
        }

        $cacheKey = $user->getKey().':'.$communityId;

        if (array_key_exists($cacheKey, $this->communityVisibilityCache)) {
            return $this->communityVisibilityCache[$cacheKey];
        }

        $community = Community::query()
            ->visibleTo($user)
            ->whereKey($communityId)
            ->first();

        $this->communityVisibilityCache[$cacheKey] = $community;

        return $community;
    }

    /**
     * @param  list<array{row:int, errors:string, data:array<string, mixed>}>  $failedRows
     */
    private function storeFailureReport(array $failedRows): ?string
    {
        if ($failedRows === []) {
            return null;
        }

        $stream = fopen('php://temp', 'w+');

        if ($stream === false) {
            return null;
        }

        fputcsv($stream, ['row', 'errors', ...self::TEMPLATE_HEADERS]);

        foreach ($failedRows as $failedRow) {
            fputcsv($stream, [
                $failedRow['row'],
                $failedRow['errors'],
                $this->stringifyCsvValue($failedRow['data']['community_id'] ?? null),
                $this->stringifyCsvValue($failedRow['data']['name'] ?? null),
                $this->stringifyCsvValue($failedRow['data']['description'] ?? null),
                $this->stringifyCsvValue($failedRow['data']['condition'] ?? null),
                $this->stringifyCsvValue($failedRow['data']['price'] ?? null),
                $this->stringifyCsvValue($failedRow['data']['acquired_at'] ?? null),
                $this->stringifyCsvValue($failedRow['data']['is_active'] ?? null),
            ]);
        }

        rewind($stream);
        $content = stream_get_contents($stream);
        fclose($stream);

        if ($content === false) {
            return null;
        }

        $path = self::FAILURE_DIRECTORY.'/'.Str::uuid().'.csv';
        Storage::disk('local')->put($path, $content);

        return $path;
    }

    private function stringifyCsvValue(mixed $value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value === null) {
            return '';
        }

        return (string) $value;
    }
}
