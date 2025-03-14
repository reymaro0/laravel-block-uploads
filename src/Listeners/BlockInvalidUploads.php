<?php

namespace BlockUploads\Listeners;

use Illuminate\Filesystem\Events\FileStored;
use Illuminate\Support\Facades\Storage;

class BlockInvalidUploads
{
    protected $blockedExtensions;

    public function __construct()
    {
        $this->blockedExtensions = config('blockuploads.blocked_extensions', []);
    }

    public function handle(FileStored $event)
    {
        $extension = strtolower(pathinfo($event->path, PATHINFO_EXTENSION));

        if (in_array($extension, $this->blockedExtensions)) {
            Storage::delete($event->path);
            \Log::warning("File deleted due to extension not allowed: " . $event->path);
        }
    }
}
