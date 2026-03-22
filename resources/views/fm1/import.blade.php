<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Importar FM1 desde Excel | Gestión de Plantilla</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; overflow-x: hidden; }

        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background-color: #0a0a0a;
            background-image:
                radial-gradient(at 0% 0%,   hsla(253,16%,7%,1)     0, transparent 50%),
                radial-gradient(at 50% 0%,  hsla(225,39%,30%,0.2)  0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(280,49%,30%,0.2)  0, transparent 50%),
                radial-gradient(at 0% 100%, hsla(321,75%,40%,0.1)  0, transparent 50%);
            filter: blur(80px);
        }

        .glass {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .glass-hover {
            transition: all 0.35s cubic-bezier(0.23,1,0.32,1);
        }
        .glass-hover:hover {
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }

        .upload-zone {
            border: 2px dashed rgba(168,85,247,0.3);
            transition: all 0.3s ease;
        }
        .upload-zone:hover, .upload-zone.drag-over {
            border-color: rgba(168,85,247,0.7);
            background: rgba(168,85,247,0.05);
        }

        .badge-completed { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .badge-processing { background: rgba(251,191,36,0.15); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); }
        .badge-failed     { background: rgba(239,68,68,0.15);  color: #f87171; border: 1px solid rgba(239,68,68,0.3); }

        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            transition: all 0.3s ease;
            box-shadow: 0 0 20px rgba(168,85,247,0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 0 30px rgba(168,85,247,0.5);
        }

        .btn-dl {
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.25);
            color: #34d399;
            transition: all 0.25s ease;
        }
        .btn-dl:hover {
            background: rgba(16,185,129,0.2);
            border-color: rgba(16,185,129,0.5);
        }

        .btn-view {
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.25);
            color: #818cf8;
            transition: all 0.25s ease;
        }
        .btn-view:hover {
            background: rgba(99,102,241,0.2);
            border-color: rgba(99,102,241,0.5);
        }

        .btn-del {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: #f87171;
            transition: all 0.25s ease;
        }
        .btn-del:hover {
            background: rgba(239,68,68,0.18);
            border-color: rgba(239,68,68,0.4);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .slide-up { animation: slideUp 0.5s cubic-bezier(0.23,1,0.32,1) both; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
    </style>
</head>
<body class="bg-[#0a0a0a] text-white min-h-screen selection:bg-purple-500/30">

    <div class="mesh-bg"></div>

    <!-- Back button -->
    <a href="{{ url('/') }}" class="fixed top-6 left-6 z-50 flex items-center justify-center w-10 h-10 bg-white/5 hover:bg-white/10 backdrop-blur-md border border-white/10 rounded-full text-white shadow-xl transition-all hover:scale-110" title="Volver al Inicio">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </a>

    <div class="relative z-10 max-w-5xl mx-auto px-6 py-20">

        <!-- Header -->
        <header class="text-center mb-16 slide-up">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-300 text-xs font-bold uppercase tracking-widest mb-6">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Formatos FM1
            </div>
            <h1 class="text-5xl lg:text-6xl font-black tracking-tight mb-4 leading-none">
                Importar FM1
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-400 via-pink-400 to-indigo-400">
                    desde Excel
                </span>
            </h1>
            <p class="text-gray-400 text-lg font-light max-w-xl mx-auto">
                Carga uno o varios registros de movimiento de personal desde un archivo Excel y descarga sus formatos FM1 en PDF.
            </p>
        </header>

        <!-- Alerts -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-3 slide-up">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm flex items-center gap-3 slide-up">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Upload Form -->
        <section class="glass rounded-3xl p-8 mb-10 slide-up delay-1">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </span>
                Nuevo Lote de Importación
            </h2>

            <form id="import-form" action="{{ route('fm1.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Drop Zone -->
                <div id="drop-zone" class="upload-zone rounded-2xl p-10 text-center cursor-pointer" onclick="document.getElementById('fm1-file').click()">
                    <div id="dz-idle">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-300 font-semibold mb-1">Arrastra tu archivo Excel aquí</p>
                        <p class="text-gray-500 text-sm">o haz clic para seleccionar</p>
                        <p class="text-gray-600 text-xs mt-3">XLSX, XLS o CSV · Hasta 100 MB</p>
                    </div>
                    <div id="dz-selected" class="hidden">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p id="selected-name" class="text-emerald-300 font-bold mb-1"></p>
                        <p class="text-gray-500 text-xs">Haz clic para cambiar el archivo</p>
                    </div>
                    <input id="fm1-file" name="file" type="file" class="hidden" accept=".xlsx,.xls,.csv">
                </div>

                @error('file')
                    <p class="text-red-400 text-sm">{{ $message }}</p>
                @enderror

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Notas (opcional)</label>
                    <input
                        type="text"
                        name="notes"
                        id="notes"
                        placeholder="Ej: Quincena 06/2026 – Centro de Trabajo X"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/30 transition"
                        value="{{ old('notes') }}"
                    >
                </div>

                <!-- Submit -->
                <button id="submit-btn" type="submit" class="btn-primary w-full py-4 rounded-2xl font-bold text-white text-sm uppercase tracking-widest flex items-center justify-center gap-3">
                    <svg id="submit-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <svg id="loading-icon" class="w-5 h-5 hidden animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span id="submit-text">Iniciar Importación</span>
                </button>
            </form>
        </section>

        <!-- Template Download -->
        <section class="glass rounded-3xl p-6 mb-10 slide-up delay-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white">Plantilla de Excel</p>
                        <p class="text-xs text-gray-500">Descarga el formato con las columnas correctas para llenar los datos FM1</p>
                    </div>
                </div>
                <a href="{{ route('fm1.import.template') }}" class="btn-view px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Descargar plantilla
                </a>
            </div>
        </section>

        <!-- Batches List -->
        <section class="slide-up delay-2">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Lotes Importados
                    <span class="text-xs font-normal text-gray-500">({{ count($batches) }} lotes)</span>
                </h2>
                @if(count($batches) > 1)
                    <span class="text-xs text-gray-600">Total: {{ $batches->sum('forms_count') }} registros</span>
                @endif
            </div>

            @if($batches->isEmpty())
                <div class="glass rounded-3xl p-16 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-3xl bg-white/3 border border-white/8 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <p class="text-gray-500 font-semibold mb-2">No hay importaciones aún</p>
                    <p class="text-gray-700 text-sm">Sube tu primer archivo Excel para comenzar.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($batches as $batch)
                        <div class="glass glass-hover rounded-2xl p-6">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <!-- Left: info -->
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-12 h-12 flex-shrink-0 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-white truncate max-w-xs" title="{{ $batch->original_filename }}">
                                            {{ $batch->original_filename }}
                                        </p>
                                        <div class="flex items-center flex-wrap gap-3 mt-1">
                                            <span class="text-xs text-gray-500">
                                                {{ $batch->created_at->format('d M Y · H:i') }}
                                            </span>
                                            <span class="text-xs font-bold text-purple-300">
                                                {{ $batch->forms_count }} registros
                                            </span>
                                            <span class="text-xs px-2 py-0.5 rounded-full font-bold badge-{{ $batch->status }}">
                                                {{ ucfirst($batch->status) }}
                                            </span>
                                        </div>
                                        @if($batch->notes)
                                            <p class="text-xs text-gray-600 mt-1 italic truncate max-w-xs" title="{{ $batch->notes }}">{{ $batch->notes }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Right: actions -->
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <!-- View records -->
                                    <a href="{{ route('fm1.import.batch', $batch->id) }}"
                                       class="btn-view px-3 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver
                                    </a>

                                    <!-- Download all as ZIP -->
                                    @if($batch->forms_count > 0)
                                        <a href="{{ route('fm1.import.batch.download', $batch->id) }}"
                                           class="btn-dl px-3 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Descargar todo (ZIP)
                                        </a>
                                    @endif

                                    <!-- Delete batch -->
                                    <form action="{{ route('fm1.import.batch.destroy', $batch->id) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este lote y todos sus {{ $batch->forms_count }} registros FM1?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-del px-3 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

    </div>

    <script>
        // Drag & drop
        const dropZone  = document.getElementById('drop-zone');
        const fileInput = document.getElementById('fm1-file');
        const dzIdle    = document.getElementById('dz-idle');
        const dzSelected = document.getElementById('dz-selected');
        const selectedName = document.getElementById('selected-name');

        function showSelected(name) {
            selectedName.textContent = name;
            dzIdle.classList.add('hidden');
            dzSelected.classList.remove('hidden');
        }

        fileInput.addEventListener('change', function () {
            if (this.files[0]) showSelected(this.files[0].name);
        });

        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('drag-over'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                showSelected(files[0].name);
            }
        });
        dropZone.addEventListener('click', (e) => {
            if (e.target !== fileInput) fileInput.click();
        });

        // Loading state on submit
        const form = document.getElementById('import-form');
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const submitIcon = document.getElementById('submit-icon');
        const loadingIcon = document.getElementById('loading-icon');

        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70');
            submitText.textContent = 'Procesando...';
            submitIcon.classList.add('hidden');
            loadingIcon.classList.remove('hidden');
        });
    </script>
</body>
</html>
