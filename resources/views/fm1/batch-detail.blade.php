<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lote FM1 #{{ $batch->id }} | Gestión de Plantilla</title>
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
                radial-gradient(at 0% 0%,   hsla(253,16%,7%,1)    0, transparent 50%),
                radial-gradient(at 50% 0%,  hsla(280,39%,30%,0.2) 0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(220,49%,30%,0.2) 0, transparent 50%);
            filter: blur(80px);
        }

        .glass {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .row-hover {
            transition: background 0.2s ease;
        }
        .row-hover:hover {
            background: rgba(168,85,247,0.06);
        }

        .btn-dl {
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.25);
            color: #34d399;
            transition: all 0.2s;
        }
        .btn-dl:hover { background: rgba(16,185,129,0.2); border-color: rgba(16,185,129,0.5); }

        .btn-zip {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            box-shadow: 0 0 20px rgba(168,85,247,0.25);
            transition: all 0.3s ease;
        }
        .btn-zip:hover { box-shadow: 0 0 30px rgba(168,85,247,0.4); transform: translateY(-1px); }

        .badge-completed { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .badge-processing { background: rgba(251,191,36,0.15); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); }
        .badge-failed     { background: rgba(239,68,68,0.15);  color: #f87171; border: 1px solid rgba(239,68,68,0.3); }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .slide-up { animation: slideUp 0.45s cubic-bezier(0.23,1,0.32,1) both; }
        .delay-1 { animation-delay: 0.1s; }
    </style>
</head>
<body class="bg-[#0a0a0a] text-white min-h-screen selection:bg-purple-500/30">

    <div class="mesh-bg"></div>

    <!-- Back button -->
    <a href="{{ route('fm1.import.index') }}" class="fixed top-6 left-6 z-50 flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 backdrop-blur-md border border-white/10 rounded-full text-sm text-white shadow-xl transition-all hover:scale-105">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Volver
    </a>

    <div class="relative z-10 max-w-6xl mx-auto px-6 py-20">

        <!-- Header -->
        <header class="mb-10 slide-up">
            <div class="flex items-start justify-between gap-6 flex-wrap">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-xs font-bold uppercase tracking-widest text-purple-400">Lote #{{ $batch->id }}</span>
                        <span class="text-xs px-2.5 py-1 rounded-full font-bold badge-{{ $batch->status }}">
                            {{ ucfirst($batch->status) }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-black tracking-tight text-white mb-1 truncate max-w-2xl" title="{{ $batch->original_filename }}">
                        {{ $batch->original_filename }}
                    </h1>
                    <div class="flex items-center gap-4 text-sm text-gray-500 flex-wrap">
                        <span>{{ $batch->created_at->format('d M Y · H:i') }}</span>
                        <span class="text-purple-300 font-bold">{{ $forms->total() }} registros totales</span>
                        @if($batch->notes)
                            <span class="italic">{{ $batch->notes }}</span>
                        @endif
                    </div>
                </div>

                <!-- Download all ZIP -->
                <a href="{{ route('fm1.import.batch.download', $batch->id) }}"
                   class="btn-zip px-6 py-3 rounded-2xl font-bold text-white text-sm flex items-center gap-2.5 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar todos (ZIP)
                </a>
            </div>
        </header>

        <!-- Table -->
        <div class="glass rounded-3xl overflow-hidden slide-up delay-1">
            <!-- Search bar -->
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input
                    type="text"
                    id="table-search"
                    placeholder="Filtrar por nombre, No. empleado, RFC…"
                    class="flex-1 bg-transparent text-sm text-gray-300 placeholder-gray-700 focus:outline-none"
                >
                <span class="text-xs text-gray-600">Página {{ $forms->currentPage() }} de {{ $forms->lastPage() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-600">#</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Nombre</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-600">No. Empleado</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-600">RFC</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Tipo Movimiento</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Fecha Mov.</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Plaza</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-gray-600">PDF</th>
                        </tr>
                    </thead>
                    <tbody id="records-table" class="divide-y divide-white/3">
                        @forelse($forms as $form)
                            <tr class="row-hover" data-search="{{ strtolower($form->nombre . ' ' . $form->num_empleado . ' ' . $form->rfc) }}">
                                <td class="px-5 py-4 text-gray-600 font-mono text-xs">{{ $form->id }}</td>
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-white">{{ $form->nombre }}</span>
                                </td>
                                <td class="px-5 py-4 text-gray-400 font-mono text-xs">{{ $form->num_empleado ?? '—' }}</td>
                                <td class="px-5 py-4 text-gray-400 font-mono text-xs">{{ $form->rfc ?? '—' }}</td>
                                <td class="px-5 py-4">
                                    @if($form->tipo_mov)
                                        <span class="px-2 py-0.5 rounded-md bg-purple-500/10 border border-purple-500/20 text-purple-300 text-xs font-semibold">
                                            {{ Str::limit($form->tipo_mov, 30) }}
                                        </span>
                                    @else
                                        <span class="text-gray-700">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-400 text-xs">
                                    {{ $form->fecha_movimiento ? $form->fecha_movimiento->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-xs">{{ $form->numero_plaza ?? '—' }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('fm1.import.record.pdf', $form->id) }}"
                                       class="btn-dl inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold"
                                       title="Descargar PDF de {{ $form->nombre }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-16 text-center text-gray-600">
                                    No hay registros en este lote.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($forms->hasPages())
                <div class="px-6 py-4 border-t border-white/5 flex justify-center">
                    {{ $forms->links('pagination::simple-tailwind') }}
                </div>
            @endif
        </div>

    </div>

    <script>
        // Client-side row filter
        const searchInput = document.getElementById('table-search');
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#records-table tr[data-search]').forEach(row => {
                const text = row.getAttribute('data-search');
                row.style.display = !q || text.includes(q) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
