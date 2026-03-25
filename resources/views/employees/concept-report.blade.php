<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reporte por Concepto - {{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            .filter-new-active tr:not([data-is-new="true"]) {
                display: none;
            }
        </style>
    </head>
    <body class="bg-gray-50 dark:bg-[#0a0a0a] text-[#1b1b18] p-6 lg:p-8 min-h-screen">
        <a href="{{ url('/') }}" class="fixed top-6 left-6 z-50 flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-full text-white shadow-xl transition-all hover:scale-110 group" title="Volver al Inicio">
            <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Reporte de Empleados por Concepto</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Filtra empleados que tengan asignado un concepto de nómina específico en una quincena.</p>
                </div>

            </div>

            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 mb-8 border border-gray-200 dark:border-[#3E3E3A]">
                <form action="{{ route('employees.concept-report') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <div>
                        <label for="concept" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Concepto</label>
                        <select name="concept" id="concept" class="w-full rounded-md border-gray-300 dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-white focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 bg-white">
                            <option value="">-- Seleccionar --</option>
                            @foreach($concepts as $id => $name)
                                <option value="{{ $id }}" {{ $selectedConcept == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quincena (Periodo)</label>
                        <select name="period" id="period" class="w-full rounded-md border-gray-300 dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-white focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 bg-white">
                            <option value="">-- Seleccionar --</option>
                            @foreach($periods as $period)
                                <option value="{{ $period }}" {{ $selectedPeriod == $period ? 'selected' : '' }}>{{ $period }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors font-medium">
                            Consultar
                        </button>
                    </div>
                </form>
            </div>

            @if($selectedConcept && $selectedPeriod)
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center gap-3">
                        Resultados: {{ $concepts[$selectedConcept] ?? '' }} - {{ $selectedPeriod }}
                    </h2>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-800 rounded-full text-gray-600 dark:text-gray-300 flex items-center gap-2">
                            <span>{{ $employees->count() }} Empleados</span>
                            @if(isset($previousCount) && $previousCount > 0)
                                @php $netDiff = $employees->count() - $previousCount; @endphp
                                <span class="mx-1 text-gray-400">|</span>
                                <span class="flex gap-2 text-[11px] uppercase font-bold tracking-tight">
                                    <button type="button" onclick="toggleNewFilter(true)" class="text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 px-1 rounded transition-colors" title="Click para ver solo nuevos">
                                        +{{ $joinsCount }} ingresos
                                    </button>
                                    <button type="button" onclick="showLeavesModal()" class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 px-1 rounded transition-colors" title="Click para ver quiénes son">
                                        -{{ $leavesCount }} bajas
                                    </button>
                                    <span class="{{ $netDiff >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                                        (Neto: {{ $netDiff > 0 ? '+' : '' }}{{ $netDiff }})
                                    </span>
                                    <button id="resetFilterBtn" type="button" onclick="toggleNewFilter(false)" class="hidden ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 text-[10px] underline">
                                        (Ver todos)
                                    </button>
                                </span>
                            @endif
                        </span>
                        @if($employees->count() > 0)
                            <a href="{{ route('employees.concept-report.export', ['concept' => $selectedConcept, 'period' => $selectedPeriod]) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Exportar a Excel
                            </a>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-[#161615] shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-[#3E3E3A]">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-[#3E3E3A]">
                            <thead class="bg-gray-50 dark:bg-[#0d0d0c]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Num Empleado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre Completo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Puesto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Plaza</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Centro de Trabajo</th>
                                  
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody" class="bg-white dark:bg-[#161615] divide-y divide-gray-200 dark:divide-[#3E3E3A]">
                                @forelse($employees as $employee)
                                    @php 
                                        $isNew = !empty($previousEmployeeIds) && !in_array($employee->id_empleado, $previousEmployeeIds);
                                    @endphp
                                    <tr data-is-new="{{ $isNew ? 'true' : 'false' }}" class="hover:bg-gray-50 dark:hover:bg-[#0d0d0c] transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('employees.search', ['id_empleado' => $employee->id_empleado]) }}" class="text-blue-600 hover:underline cursor-pointer" title="Ver historial de este empleado">
                                                {{ $employee->id_empleado }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                            {{ $employee->nombre }} {{ $employee->apellido_1 }} {{ $employee->apellido_2 }}
                                            @if(!empty($previousEmployeeIds) && !in_array($employee->id_empleado, $previousEmployeeIds))
                                                <span class="ml-2 px-2 py-0.5 text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 rounded-full uppercase tracking-tighter shadow-sm border border-green-200 dark:border-green-800 animate-pulse">
                                                    Recién anexado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $employee->id_puesto_plaza  }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $employee->id_plaza }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                            {{ str_pad($employee->id_centro_pago, 5, '0', STR_PAD_LEFT) }} 
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            No se encontraron empleados con este concepto en el periodo seleccionado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <script>
            function toggleNewFilter(active) {
                const tbody = document.getElementById('employeeTableBody');
                const resetBtn = document.getElementById('resetFilterBtn');
                
                if (active) {
                    tbody.classList.add('filter-new-active');
                    resetBtn.classList.remove('hidden');
                } else {
                    tbody.classList.remove('filter-new-active');
                    resetBtn.classList.add('hidden');
                }
            }

            function showLeavesModal() {
                document.getElementById('leavesModal').classList.remove('hidden');
                document.getElementById('leavesModal').classList.add('flex');
            }

            function closeLeavesModal() {
                document.getElementById('leavesModal').classList.add('hidden');
                document.getElementById('leavesModal').classList.remove('flex');
            }
        </script>

        <!-- Modal para Bajas -->
        <div id="leavesModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-[#161615] rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden flex flex-col border border-gray-200 dark:border-[#3E3E3A]">
                <div class="p-6 border-b border-gray-200 dark:border-[#3E3E3A] flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Detalle de Bajas del Sindicato</h3>
                    <button onclick="closeLeavesModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto">
                    @if(!empty($leavesDetails))
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-[#3E3E3A]">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Nombre</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Destino / Estatus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-[#1b1b1b]">
                                @foreach($leavesDetails as $leave)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-200">
                                            <a href="{{ route('employees.search', ['id_empleado' => $leave['id_empleado']]) }}" class="text-blue-600 hover:underline cursor-pointer" title="Ver historial de este empleado">
                                                {{ $leave['id_empleado'] }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $leave['nombre'] }}</td>
                                        <td class="px-4 py-3">
                                            @if($leave['status'] == 'baja_total')
                                                <span class="px-2 py-1 text-[10px] font-bold bg-red-100 text-red-700 rounded uppercase">Baja</span>
                                            @elseif($leave['nueva_union'] == 'Sin Sindicato')
                                                <span class="px-2 py-1 text-[10px] font-bold bg-yellow-100 text-yellow-700 rounded uppercase">Sin Sindicato</span>
                                            @else
                                                <span class="px-2 py-1 text-[10px] font-bold bg-blue-100 text-blue-700 rounded uppercase">Se fue a: {{ $leave['nueva_union'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center py-8 text-gray-500 italic">No hay detalles de bajas disponibles para este periodo.</p>
                    @endif
                </div>
                <div class="p-6 border-t border-gray-200 dark:border-[#3E3E3A] flex justify-end">
                    <button onclick="closeLeavesModal()" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-white rounded-lg font-medium transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </body>
</html>
