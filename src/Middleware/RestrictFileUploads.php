<?php

namespace BlockUploads\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RestrictFileUploads
{
    protected $blockedExtensions;

    public function __construct()
    {
        $this->blockedExtensions = config('blockuploads.blocked_extensions', []);
    }

    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post')) {
            foreach ($request->allFiles() as $file) {
                if ($file instanceof UploadedFile) {
                    $extension = strtolower($file->getClientOriginalExtension());

                    if (in_array($extension, $this->blockedExtensions)) {
                        \Log::warning("Upload attempt blocked: " . $file->getClientOriginalName());
                        abort(403, 'File extension not allowed.');
                    }
                }
            }
        }

        return $next($request);
    }
}
