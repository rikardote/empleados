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
    </head>
    <body class="bg-gray-50 dark:bg-[#0a0a0a] text-[#1b1b18] p-6 lg:p-8 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Reporte de Empleados por Concepto</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Filtra empleados que tengan asignado un concepto de nómina específico en una quincena.</p>
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
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        Resultados: {{ $concepts[$selectedConcept] ?? '' }} - {{ $selectedPeriod }}
                    </h2>
                    <span class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-800 rounded-full text-gray-600 dark:text-gray-300">
                        {{ $employees->count() }} Empleados
                    </span>
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
                            <tbody class="bg-white dark:bg-[#161615] divide-y divide-gray-200 dark:divide-[#3E3E3A]">
                                @forelse($employees as $employee)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-[#0d0d0c] transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $employee->id_empleado }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                            {{ $employee->nombre }} {{ $employee->apellido_1 }} {{ $employee->apellido_2 }}
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
    </body>
</html>
