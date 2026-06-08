<?php

return [
    'disk' => env('SCORM_STORAGE_DISK', env('FILESYSTEM_DISK', 'public')),
    'upload_max_kb' => env('SCORM_UPLOAD_MAX_KB', 512000),
    'base_path' => 'scorm',
];
