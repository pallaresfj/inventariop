<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemImportFailureDownloadController extends Controller
{
    public function __invoke(Request $request, string $file): StreamedResponse
    {
        abort_if((int) $request->query('owner') !== (int) $request->user()?->getAuthIdentifier(), 403);
        abort_unless((bool) preg_match('/\A[a-zA-Z0-9\-_]+\.csv\z/', $file), 404);

        $path = "item-import-failures/{$file}";

        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, $file, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
