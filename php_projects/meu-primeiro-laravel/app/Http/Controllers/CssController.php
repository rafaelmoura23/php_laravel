<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class CssController extends Controller
{
    public function getCss($filename)
    {
        $path = resource_path('css/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        $mimeType = 'text/css'; // MIME type para CSS
        $content = file_get_contents($path);

        return Response::make($content, 200, [
            'Content-Type' => $mimeType,
        ]);
    }
}

