<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Domain\Scorm\SCORMPackage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SCORMPlayerController extends Controller
{
    public function show(Request $request, SCORMPackage $package)
    {
        $track = $package->tracks()->firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        return view('scorm.player', [
            'package' => $package,
            'track' => $track,
            'launchUrl' => Storage::disk(config('scorm.disk'))->url($package->extract_path.'/'.$package->launch_path),
            'isPlaceholderLaunch' => $this->isPlaceholderLaunch($package),
        ]);
    }

    private function isPlaceholderLaunch(SCORMPackage $package): bool
    {
        $disk = Storage::disk(config('scorm.disk'));
        $path = $package->extract_path.'/'.$package->launch_path;

        if (! $disk->exists($path)) {
            return false;
        }

        $content = $disk->get($path);

        return str_contains($content, 'Not implemented yet')
            && str_contains($content, 'runtime and sequencing examples');
    }
}
