<?php

namespace App\Http\Controllers\Dosen;

use App\Domain\Academic\LearningModule;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSCORMPackageRequest;
use App\Services\Scorm\SCORMPackageService;

class SCORMPackageController extends Controller
{
    public function store(StoreSCORMPackageRequest $request, SCORMPackageService $packages)
    {
        $module = LearningModule::query()->findOrFail($request->integer('learning_module_id'));

        $package = $packages->store($module, $request->file('package'), (string) $request->string('title'));

        return redirect()
            ->route('dosen.modules.index')
            ->with('status', "SCORM package {$package->title} berhasil diunggah.");
    }
}
