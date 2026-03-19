<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Comparación de Quincenas - {{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            .diff-old { background-color: rgba(239, 68, 68, 0.1); color: #dc2626; text-decoration: line-through; }
            .diff-new { background-color: rgba(34, 197, 94, 0.1); color: #16a34a; font-weight: 500; }
            .badge-new { background-color: #dcfce7; color: #166534; }
            .badge-modified { background-color: #fef9c3; color: #854d0e; }
            .badge-removed { background-color: #fee2e2; color: #991b1b; }
        </style>
    </head>
    <body class="bg-gray-50 dark:bg-[#0a0a0a] text-[#1b1b18] p-6 lg:p-8 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Comparación de Quincenas</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        @if(isset($previousPeriod) && isset($latestPeriod))
                            Diferencias encontradas entre <b>{{ $previousPeriod }}</b> e inmediata anterior <b>{{ $latestPeriod }}</b>.
                        @else
                            Seleccione dos periodos para comparar las diferencias.
                        @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('employees.concept-report') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Volver a Reportes
                    </a>
                </div>
            </div>

            @if(isset($error))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ $error }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-[#161615] rounded-xl shadow-sm p-6 mb-8 border border-gray-200 dark:border-[#3E3E3A]">
                <form action="{{ route('employees.compare-periods') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="previous_period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quincena Anterior</label>
                        <select name="previous_period" id="previous_period" class="w-full rounded-md border-gray-300 dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-white focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 bg-white">
                            @foreach($periods as $period)
                                <option value="{{ $period }}" {{ $previousPeriod == $period ? 'selected' : '' }}>{{ $period }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="latest_period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quincena Nueva</label>
                        <select name="latest_period" id="latest_period" class="w-full rounded-md border-gray-300 dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-white focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 bg-white">
                            @foreach($periods as $period)
                                <option value="{{ $period }}" {{ $latestPeriod == $period ? 'selected' : '' }}>{{ $period }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="plaza_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Plaza</label>
                        <select name="plaza_type" id="plaza_type" class="w-full rounded-md border-gray-300 dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-white focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 bg-white">
                            <option value="">Todos los tipos</option>
                            @foreach($plazaTypes as $type)
                                <option value="{{ $type }}" {{ $selectedPlazaType == $type ? 'selected' : '' }}>
                                    @if($type == 1) Presupuestal 
                                    @elseif($type == 2) Eventuales 
                                    @elseif($type == 6) Pensionissste
                                    @elseif($type == 7) Residentes 
                                    @else {{ $type }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors font-medium">
                            Comparar
                        </button>
                    </div>
                </form>
                </form>
            </div>

            <div id="diff-container" class="space-y-6">
                @php
                    $newCount = collect($differences)->where('type', 'new')->count();
                    $modifiedCount = collect($differences)->where('type', 'modified')->count();
                    $removedCount = collect($differences)->where('type', 'removed')->count();
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                    <div onclick="filterType('all')" class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 p-4 rounded-lg cursor-pointer hover:shadow-md transition-shadow group">
                        <p class="text-sm text-blue-600 dark:text-blue-400 font-medium group-hover:underline">Todos</p>
                        <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ count($differences) }}</p>
                    </div>
                    <div onclick="filterType('new')" class="bg-green-50 dark:bg-green-900/10 border border-green-100 dark:border-green-900/30 p-4 rounded-lg cursor-pointer hover:shadow-md transition-shadow group">
                        <p class="text-sm text-green-600 dark:text-green-400 font-medium group-hover:underline">Nuevos Ingresos</p>
                        <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $newCount }}</p>
                    </div>
                    <div onclick="filterType('modified')" class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-900/30 p-4 rounded-lg cursor-pointer hover:shadow-md transition-shadow group">
                        <p class="text-sm text-yellow-600 dark:text-yellow-400 font-medium group-hover:underline">Cambios Detectados</p>
                        <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $modifiedCount }}</p>
                    </div>
                    <div onclick="filterType('removed')" class="bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 p-4 rounded-lg cursor-pointer hover:shadow-md transition-shadow group">
                        <p class="text-sm text-red-600 dark:text-red-400 font-medium group-hover:underline">Bajas / No presentes</p>
                        <p class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $removedCount }}</p>
                    </div>
                </div>

                @forelse($differences as $diff)
                    <div class="employee-card bg-white dark:bg-[#161615] shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-[#3E3E3A]" data-type="{{ $diff['type'] }}">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-[#3E3E3A] flex justify-between items-center bg-gray-50/50 dark:bg-[#0d0d0c]/50">
                            <div>
                                <span class="text-xs font-mono text-gray-500 dark:text-gray-400 uppercase tracking-wider">Empleado #{{ $diff['id_empleado'] }} @if($diff['id_plaza_empleado']) (Plaza: {{ $diff['id_plaza_empleado'] }}) @endif</span>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $diff['nombre_completo'] }}</h3>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium badge-{{ $diff['type'] }}">
                                @if($diff['type'] == 'new') Nuevo @elseif($diff['type'] == 'modified') Modificado @else Baja @endif
                            </span>
                        </div>
                        
                        @if($diff['type'] == 'modified')
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-[#3E3E3A]">
                                    <thead class="bg-gray-50 dark:bg-[#0d0d0c]">
                                        <tr>
                                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/4">Atributo</th>
                                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor Anterior ({{ $previousPeriod }})</th>
                                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor Nuevo ({{ $latestPeriod }})</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-[#3E3E3A]">
                                        @foreach($diff['changes'] as $field => $change)
                                            <tr>
                                                <td class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50/30 dark:bg-[#0d0d0c]/30">
                                                    {{ ucwords(str_replace('_', ' ', $field)) }}
                                                </td>
                                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400 diff-old italic">
                                                    {{ $change['old'] ?: '(vacio)' }}
                                                </td>
                                                <td class="px-6 py-3 text-sm text-gray-900 dark:text-white diff-new">
                                                    {{ $change['new'] ?: '(vacio)' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($diff['type'] == 'new')
                            <div class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <p>Este empleado no aparecía en la quincena anterior ({{ $previousPeriod }}).</p>
                            </div>
                        @else
                            <div class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <p>Este empleado aparecía en la quincena anterior ({{ $previousPeriod }}) pero ya no figura en la nueva ({{ $latestPeriod }}).</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white dark:bg-[#161615] rounded-xl p-12 text-center border border-dashed border-gray-300 dark:border-[#3E3E3A]">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Sin diferencias</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No se detectaron cambios en los conceptos de los empleados entre estos dos periodos.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <script>
            function filterType(type) {
                const cards = document.querySelectorAll('.employee-card');
                cards.forEach(card => {
                    if (type === 'all' || card.getAttribute('data-type') === type) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
        </script>
    </body>
</html>
