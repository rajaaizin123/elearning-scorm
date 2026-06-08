<?php

namespace App\Services\Scorm;

use App\Domain\Academic\LearningModule;
use App\Domain\Scorm\SCORMPackage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use ZipArchive;

class SCORMPackageService
{
    public function __construct(private readonly SCORMManifestParser $manifestParser)
    {
    }

    public function store(LearningModule $module, UploadedFile $file, string $title): SCORMPackage
    {
        $uuid = (string) Str::uuid();
        $basePath = trim(config('scorm.base_path', 'scorm'), '/')."/{$uuid}";
        $disk = Storage::disk(config('scorm.disk'));
        $zipPath = $file->storeAs("{$basePath}/source", 'package.zip', config('scorm.disk'));
        $extractPath = $basePath;
        $localExtractPath = storage_path("app/scorm/tmp/{$uuid}");

        if (! is_dir($localExtractPath)) {
            mkdir($localExtractPath, 0755, true);
        }

        $zip = new ZipArchive();
        $localZipPath = $file->getRealPath();

        if ($zip->open($localZipPath) !== true) {
            throw new RuntimeException('Unable to open SCORM zip package.');
        }

        $zip->extractTo($localExtractPath);
        $zip->close();

        $manifest = $this->manifestParser->parse("{$localExtractPath}/imsmanifest.xml");
        $this->copyExtractedFilesToDisk($localExtractPath, $extractPath, $disk);

        return SCORMPackage::query()->create([
            'learning_module_id' => $module->id,
            'uuid' => $uuid,
            'title' => $title ?: $manifest['title'],
            'version' => $manifest['version'],
            'zip_path' => $zipPath,
            'extract_path' => $extractPath,
            'launch_path' => $manifest['launch_path'],
            'manifest' => $manifest,
            'status' => 'draft',
        ]);
    }

    private function copyExtractedFilesToDisk(string $source, string $target, $disk): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $relativePath = str_replace('\\', '/', substr($file->getPathname(), strlen($source) + 1));
            $stream = fopen($file->getPathname(), 'r');

            if ($stream === false) {
                throw new RuntimeException("Unable to read extracted SCORM file: {$relativePath}");
            }

            $disk->put("{$target}/{$relativePath}", $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }
}
