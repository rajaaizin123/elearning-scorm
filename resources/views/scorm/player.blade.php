@php
    $initialProgress = min(max((float) $track->progress, 0), 100);
    $initialStatus = $track->lesson_status ?: 'incomplete';
    $lastAccessed = $track->last_accessed_at?->diffForHumans();
@endphp

<x-app-layout>
    <div class="space-y-4">
        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <span>SCORM {{ $package->version }}</span>
                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                        <span id="scorm-status-label">{{ ucfirst($initialStatus) }}</span>
                    </div>
                    <h1 class="mt-1 truncate text-xl font-semibold text-slate-950">{{ $package->title }}</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        <span id="scorm-sync-status">Siap menyimpan progres.</span>
                        @if($lastAccessed)
                            <span class="text-slate-400">Terakhir dibuka {{ $lastAccessed }}.</span>
                        @endif
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="w-full sm:w-72">
                        <div class="mb-1 flex items-center justify-between text-xs font-semibold text-slate-600">
                            <span>Progress</span>
                            <span id="scorm-progress-label">{{ number_format($initialProgress, 0) }}%</span>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-200" role="progressbar" aria-label="Progress belajar SCORM" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $initialProgress }}">
                            <div id="scorm-progress-bar" class="h-full rounded-full bg-sky-600 transition-all duration-300" style="width: {{ $initialProgress }}%"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" id="scorm-focus-button" class="inline-flex h-10 items-center justify-center rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2" title="Fokus ke konten">
                            Fokus
                        </button>
                        <button type="button" id="scorm-refresh-button" class="inline-flex h-10 items-center justify-center rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2" title="Muat ulang konten">
                            Ulangi
                        </button>
                        <button type="button" id="scorm-fullscreen-button" class="inline-flex h-10 items-center justify-center rounded-md bg-slate-900 px-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-700 focus:ring-offset-2" title="Mode layar penuh">
                            Layar penuh
                        </button>
                        <button type="button" id="scorm-complete-button" class="inline-flex h-10 items-center justify-center rounded-md bg-emerald-600 px-3 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" title="Selesaikan course">
                            Selesaikan Course
                        </button>
                    </div>
                </div>
            </div>

            <div id="scorm-player-shell" class="bg-slate-950 p-2 sm:p-3">
                <div class="overflow-hidden rounded-md bg-white shadow-xl">
                    @if($isPlaceholderLaunch ?? false)
                        <div class="flex h-[calc(100vh-18rem)] min-h-[520px] flex-col items-center justify-center bg-white p-8 text-center">
                            <h2 class="text-xl font-semibold text-slate-900">Package SCORM Ini Bukan Course Launchable</h2>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                                File launch package ini berisi halaman contoh “Not implemented yet”. Gunakan package SCORM 2004 yang memiliki runtime atau sequencing example yang benar-benar launchable.
                            </p>
                        </div>
                    @else
                        <iframe id="scorm-frame" src="{{ $launchUrl }}" title="{{ $package->title }}" class="h-[calc(100vh-18rem)] min-h-[520px] w-full bg-white"></iframe>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <script>
        window.SCORM_TRACK_URL = @json(route('mahasiswa.scorm.track', $package));
        window.SCORM_INITIAL_DATA = @json($track->runtime_data ?? []);
        window.SCORM_INITIAL_PROGRESS = @json($initialProgress);
        window.SCORM_INITIAL_STATUS = @json($initialStatus);
        window.SCORM_COMPLETE_REDIRECT_URL = @json(route('mahasiswa.dashboard'));
    </script>
    <script>
        const runtimeData = { ...window.SCORM_INITIAL_DATA };
        const isPlaceholderLaunch = @json($isPlaceholderLaunch ?? false);
        const frame = document.getElementById('scorm-frame');
        const playerShell = document.getElementById('scorm-player-shell');
        const progressBar = document.getElementById('scorm-progress-bar');
        const progressLabel = document.getElementById('scorm-progress-label');
        const progressRoot = progressBar.parentElement;
        const statusLabel = document.getElementById('scorm-status-label');
        const syncStatus = document.getElementById('scorm-sync-status');
        const focusButton = document.getElementById('scorm-focus-button');
        const refreshButton = document.getElementById('scorm-refresh-button');
        const fullscreenButton = document.getElementById('scorm-fullscreen-button');
        const completeButton = document.getElementById('scorm-complete-button');

        let currentProgress = Number(window.SCORM_INITIAL_PROGRESS) || 0;
        let currentStatus = window.SCORM_INITIAL_STATUS || 'incomplete';
        let commitTimer = null;
        let isCommitting = false;
        let quizInteractionIndex = 0;

        function clampProgress(value) {
            const numeric = Number.parseFloat(value);

            if (Number.isNaN(numeric)) {
                return currentProgress;
            }

            return Math.min(Math.max(numeric <= 1 ? numeric * 100 : numeric, 0), 100);
        }

        function progressFromPageLocation() {
            const location = runtimeData['cmi.core.lesson_location'] ?? runtimeData['cmi.location'];
            const pageIndex = Number.parseInt(location, 10);

            if (Number.isNaN(pageIndex) || ! frame) {
                return null;
            }

            try {
                const pageArray = frame.contentWindow?.pageArray;
                const totalPages = Array.isArray(pageArray) ? pageArray.length : Number.parseInt(frame.contentWindow?.pageArray?.length, 10);

                if (totalPages > 0) {
                    return clampProgress(((pageIndex + 1) / totalPages) * 100);
                }
            } catch (error) {
                return null;
            }

            return null;
        }

        function progressFromRuntime() {
            if (runtimeData['cmi.progress_measure'] !== undefined) {
                return clampProgress(runtimeData['cmi.progress_measure']);
            }

            const status = runtimeData['cmi.core.lesson_status'] || runtimeData['cmi.completion_status'] || runtimeData['cmi.success_status'] || currentStatus;

            if (['completed', 'passed'].includes(status)) {
                return 100;
            }

            const pageProgress = progressFromPageLocation();

            if (pageProgress !== null) {
                return Math.max(currentProgress, pageProgress);
            }

            return currentProgress;
        }

        function setSyncStatus(message, tone = 'slate') {
            const colors = {
                slate: 'text-slate-500',
                sky: 'text-sky-700',
                emerald: 'text-emerald-700',
                rose: 'text-rose-700',
            };

            syncStatus.className = colors[tone] || colors.slate;
            syncStatus.textContent = message;
        }

        function updateProgress(progress = progressFromRuntime()) {
            currentProgress = clampProgress(progress);
            const rounded = Math.round(currentProgress);

            progressBar.style.width = `${currentProgress}%`;
            progressLabel.textContent = `${rounded}%`;
            progressRoot.setAttribute('aria-valuenow', currentProgress.toFixed(2));
        }

        function updateStatus(status) {
            currentStatus = status || currentStatus;
            statusLabel.textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);

            if (['completed', 'passed'].includes(currentStatus)) {
                progressBar.classList.remove('bg-sky-600');
                progressBar.classList.add('bg-emerald-600');
            }
        }

        function buildPayload() {
            const status = runtimeData['cmi.core.lesson_status'] || runtimeData['cmi.completion_status'] || runtimeData['cmi.success_status'] || currentStatus;

            return {
                runtime_data: runtimeData,
                progress: progressFromRuntime(),
                lesson_status: status,
            };
        }

        function markCourseCompleted() {
            runtimeData['cmi.core.lesson_status'] = 'completed';
            runtimeData['cmi.completion_status'] = 'completed';
            runtimeData['cmi.success_status'] = 'passed';
            runtimeData['cmi.progress_measure'] = 1;
            runtimeData['cmi.core.exit'] = '';
            runtimeData['cmi.exit'] = 'normal';
            updateStatus('completed');
            updateProgress(100);
        }

        async function commitRuntime() {
            if (isCommitting) {
                return true;
            }

            isCommitting = true;
            setSyncStatus('Menyimpan progres...', 'sky');

            try {
                const response = await fetch(window.SCORM_TRACK_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(buildPayload()),
                });

                if (!response.ok) {
                    throw new Error('Gagal menyimpan progres.');
                }

                const payload = await response.json();
                updateProgress(payload.progress ?? progressFromRuntime());
                updateStatus(payload.lesson_status || currentStatus);
                setSyncStatus('Progress tersimpan.', 'emerald');

                return true;
            } catch (error) {
                setSyncStatus('Progress belum tersimpan. Coba commit lagi dari konten.', 'rose');

                return false;
            } finally {
                isCommitting = false;
            }
        }

        function scheduleCommit() {
            window.clearTimeout(commitTimer);
            commitTimer = window.setTimeout(commitRuntime, 1200);
        }

        function setInteractionValue(index, field, value) {
            runtimeData[`cmi.interactions.${index}.${field}`] = value ?? '';
        }

        function recordQuestion(id, text, type, learnerResponse, correctAnswer, wasCorrect, objectiveId) {
            const existingIndex = Object.entries(runtimeData).find(([key, value]) => {
                return key.endsWith('.id') && value === id;
            })?.[0]?.match(/^cmi\.interactions\.(\d+)\.id$/)?.[1];
            const index = existingIndex !== undefined ? Number(existingIndex) : quizInteractionIndex++;

            setInteractionValue(index, 'id', id);
            setInteractionValue(index, 'type', type);
            setInteractionValue(index, 'student_response', learnerResponse);
            setInteractionValue(index, 'correct_responses.0.pattern', correctAnswer);
            setInteractionValue(index, 'result', wasCorrect ? 'correct' : 'wrong');
            setInteractionValue(index, 'objectives.0.id', objectiveId || '');
            setInteractionValue(index, 'description', text);
            setInteractionValue(index, 'timestamp', new Date().toISOString());
            scheduleCommit();
        }

        function installQuizHooks() {
            if (! frame) {
                return;
            }

            try {
                const launchWindow = frame.contentWindow;

                if (!launchWindow || launchWindow.__lmsQuizHooksInstalled) {
                    return;
                }

                const originalRecordQuestion = launchWindow.RecordQuestion;
                const originalRecordTest = launchWindow.RecordTest;

                launchWindow.RecordQuestion = (...args) => {
                    recordQuestion(...args);

                    if (typeof originalRecordQuestion === 'function') {
                        return originalRecordQuestion.apply(launchWindow, args);
                    }

                    return undefined;
                };

                if (typeof originalRecordTest === 'function') {
                    launchWindow.RecordTest = (score) => {
                        const result = originalRecordTest.call(launchWindow, score);
                        updateProgress(100);
                        scheduleCommit();

                        return result;
                    };
                }

                launchWindow.__lmsQuizHooksInstalled = true;
            } catch (error) {
                // Cross-origin SCORM packages cannot be hooked beyond the standard runtime API.
            }
        }

        const scorm12Api = {
            LMSInitialize: () => {
                setSyncStatus('Konten aktif. Progress akan tersimpan otomatis.', 'sky');
                return 'true';
            },
            LMSFinish: () => {
                commitRuntime();
                return 'true';
            },
            LMSGetValue: (key) => runtimeData[key] ?? '',
            LMSSetValue: (key, value) => {
                runtimeData[key] = value;

                if (key === 'cmi.progress_measure') {
                    updateProgress(value);
                }

                if (key === 'cmi.core.lesson_location' || key === 'cmi.location') {
                    updateProgress();
                }

                if (key === 'cmi.core.lesson_status' || key === 'cmi.completion_status') {
                    updateStatus(value);
                    updateProgress();
                }

                scheduleCommit();
                return 'true';
            },
            LMSCommit: () => {
                commitRuntime();
                return 'true';
            },
            LMSGetLastError: () => '0',
            LMSGetErrorString: () => 'No error',
            LMSGetDiagnostic: () => '',
        };

        const scorm2004Api = {
            Initialize: () => {
                setSyncStatus('Konten SCORM 2004 aktif. Progress akan tersimpan otomatis.', 'sky');
                return 'true';
            },
            Terminate: () => {
                commitRuntime();
                return 'true';
            },
            GetValue: (key) => runtimeData[key] ?? '',
            SetValue: (key, value) => {
                runtimeData[key] = value;

                if (key === 'cmi.progress_measure') {
                    updateProgress(value);
                }

                if (key === 'cmi.location') {
                    updateProgress();
                }

                if (key === 'cmi.completion_status' || key === 'cmi.success_status') {
                    updateStatus(value === 'passed' ? 'passed' : value);
                    updateProgress();
                }

                scheduleCommit();
                return 'true';
            },
            Commit: () => {
                commitRuntime();
                return 'true';
            },
            GetLastError: () => '0',
            GetErrorString: () => 'No error',
            GetDiagnostic: () => '',
        };

        window.API = scorm12Api;
        window.API_1484_11 = scorm2004Api;

        focusButton.addEventListener('click', () => frame?.focus());
        frame?.addEventListener('load', () => {
            installQuizHooks();
            window.setTimeout(installQuizHooks, 500);
        });
        refreshButton.addEventListener('click', () => {
            frame?.contentWindow?.location.reload();
        });
        fullscreenButton.addEventListener('click', () => {
            if (document.fullscreenElement) {
                document.exitFullscreen();
                return;
            }

            playerShell.requestFullscreen?.();
        });
        completeButton.addEventListener('click', async () => {
            completeButton.disabled = true;
            completeButton.classList.add('opacity-75');
            markCourseCompleted();

            const saved = await commitRuntime();

            if (saved) {
                window.location.href = window.SCORM_COMPLETE_REDIRECT_URL;
                return;
            }

            completeButton.disabled = false;
            completeButton.classList.remove('opacity-75');
        });

        window.addEventListener('beforeunload', () => {
            if (navigator.sendBeacon) {
                const payload = new Blob([JSON.stringify(buildPayload())], { type: 'application/json' });
                navigator.sendBeacon(window.SCORM_TRACK_URL, payload);
                return;
            }

            commitRuntime();
        });

        updateProgress(currentProgress);
        updateStatus(currentStatus);
        window.setInterval(installQuizHooks, 1000);
    </script>
</x-app-layout>
