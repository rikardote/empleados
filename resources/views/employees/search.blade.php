<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consulta de Empleado - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen">
    <a href="{{ url('/') }}" class="fixed top-6 left-6 z-50 flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-full text-white shadow-xl transition-all hover:scale-110 group" title="Volver al Inicio">
        <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>
    <div class="container mx-auto px-4 py-12">

        <div class="max-w-6xl mx-auto">
            <h1 class="text-4xl font-bold mb-8 text-gray-900 dark:text-white">Consulta por Empleado</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Search Box -->
                <div class="md:col-span-2 bg-white dark:bg-[#161615] rounded-xl shadow-sm p-6 border border-gray-200 dark:border-[#3E3E3A]">
                    <form action="{{ route('employees.search') }}" method="GET" class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-2">Número de Empleado</label>
                            <input type="text" name="id_empleado" value="{{ $id_empleado }}" placeholder="Ej. 439059" 
                                class="w-full rounded-md border-gray-300 dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-3 bg-white">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-md font-medium transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Period Filter -->
                @if($id_empleado && count($available_periods) > 0)
                    <div class="bg-white dark:bg-[#161615] rounded-xl shadow-sm p-6 border border-gray-200 dark:border-[#3E3E3A]">
                        <form action="{{ route('employees.search') }}" method="GET" id="period-filter-form">
                            <input type="hidden" name="id_empleado" value="{{ $id_empleado }}">
                            <label for="periodo" class="block text-xs font-medium text-gray-500 uppercase mb-2">Filtrar por Quincena</label>
                            <select name="periodo" onchange="document.getElementById('period-filter-form').submit()" 
                                class="w-full rounded-md border-gray-300 dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:text-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-3 bg-white">
                                <option value="">Todas (Historial)</option>
                                        @foreach($available_periods as $p)
                                            <option value="{{ $p }}" {{ $selected_periodo == $p ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if($id_empleado)
                        @if(count($employees) > 0)
                            <div class="space-y-8">
                                @foreach($employees as $employee)
                                    <div class="bg-white dark:bg-[#161615] rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-[#3E3E3A]">
                                        <!-- Header -->
                                        <div class="bg-gray-50 dark:bg-[#252522] px-8 py-6 border-b border-gray-200 dark:border-[#3E3E3A] flex flex-wrap justify-between items-center gap-4">
                                            <div>
                                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                                    {{ $employee->nombre }} {{ $employee->apellido_1 }} {{ $employee->apellido_2 }}
                                                </h3>
                                                <p class="text-blue-600 dark:text-blue-400 font-medium mt-1">
                                                    Empleado ID: {{ $employee->id_empleado }} 
                                                    <span class="mx-2 text-gray-300">|</span> 
                                                    Plaza: {{ $employee->id_plaza_empleado }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="px-4 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-bold uppercase tracking-wider">
                                                    Quincena: {{ $employee->periodo }}
                                                </span>
                                                @php
                                                    $typeLabels = [1 => 'Presupuestal', 2 => 'Eventuales', 6 => 'Pensionissste', 7 => 'Residentes'];
                                                    $label = $typeLabels[$employee->id_tipo_plaza] ?? $employee->id_tipo_plaza;
                                                @endphp
                                                <span class="px-4 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg text-sm font-bold uppercase tracking-wider">
                                                    {{ $label }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Full Details (Always shown if found) -->
                                        <div class="p-8">
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                                @php
                                                    $attributes = $employee->getAttributes();
                                                    
                                                    // Group attributes logically
                                                    $groups = [
                                                        'Datos Generales' => ['id_legal', 'id_c_u_r_p_st', 'numero_ss', 'id_sindicato'],
                                                        'Estructura Laboral' => ['id_tipo_plaza', 'id_plaza_empleado', 'id_plaza', 'id_puesto_plaza', 'n_puesto_plaza', 'id_nivel', 'id_sub_nivel', 'desc_puesto', 'desc_adscripcion', 'id_centro_trabajo', 'n_centro_trabajo', 'id_area_generadora', 'n_area_generadora'],
                                                        'Fechas e Ingreso' => ['fecha_ingreso_st', 'fec_alta_empleado', 'fec_imputacion', 'fec_pago'],
                                                        'Pago y Jornada' => ['id_forma_pago', 'clave_forma_pago', 'id_banco', 'num_cuenta', 'id_turno', 'id_tipo_jornada', 'id_horario', 'n_horario', 'hora_entrada_to', 'hora_salida_to', 'num_horas'],
                                                        'Ubicación' => ['poblacion', 'n_municipio', 'n_div_geografica', 'id_div_geografica', 'id_zona', 'id_centro_pago']
                                                    ];

                                                    $categorized = $employee->getCategorizedNomina();
                                                @endphp

                                                @foreach($groups as $groupName => $fields)
                                                    <div class="space-y-4">
                                                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b pb-2 border-gray-100 dark:border-[#3E3E3A]">
                                                            {{ $groupName }}
                                                        </h4>
                                                        <div class="space-y-3">
                                                            @foreach($fields as $field)
                                                                @if(isset($attributes[$field]))
                                                                    <div>
                                                                        <p class="text-xs text-gray-500 mb-0.5">{{ strtoupper(str_replace('_', ' ', $field)) }}</p>
                                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                                                            {{ $attributes[$field] ?: '---' }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <!-- Nomina Data -->
                                                <div class="col-span-full mt-10 bg-gray-50 dark:bg-[#0a0a0a] rounded-2xl p-8 border border-gray-200 dark:border-[#3E3E3A]">
                                                    <!-- Percepciones -->
                                                    <div class="mb-10">
                                                        <h4 class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-widest mb-6 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                            Percepciones
                                                        </h4>
                                                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                                            @foreach($categorized['perceptions'] as $key => $val)
                                                                <div class="p-4 bg-white dark:bg-[#161615] rounded-xl shadow-sm border border-gray-100 dark:border-[#3E3E3A] hover:border-green-200 transition-colors">
                                                                    <p class="text-[10px] text-gray-400 uppercase font-bold truncate mb-1" title="{{ $key }}">{{ $key }}</p>
                                                                    <p class="text-lg font-black text-green-600 dark:text-green-400">
                                                                        ${{ number_format((float)$val, 2) }}
                                                                    </p>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="mt-4 flex justify-end">
                                                            <div class="bg-green-50 dark:bg-green-900/20 px-4 py-2 rounded-lg border border-green-100 dark:border-green-900/30">
                                                                <span class="text-xs font-bold text-green-700 dark:text-green-300 uppercase">Subtotal Percepciones:</span>
                                                                <span class="text-lg font-black text-green-600 dark:text-green-400 ml-2">${{ number_format($categorized['total_devengos'], 2) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Deducciones -->
                                                    <div class="mb-4">
                                                        <h4 class="text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-widest mb-6 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 14l-7 7m0 0l-7-7m7 7V3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                            Deducciones
                                                        </h4>
                                                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                                            @foreach($categorized['deductions'] as $key => $val)
                                                                <div class="p-4 bg-white dark:bg-[#161615] rounded-xl shadow-sm border border-gray-100 dark:border-[#3E3E3A] hover:border-red-200 transition-colors">
                                                                    <p class="text-[10px] text-gray-400 uppercase font-bold truncate mb-1" title="{{ $key }}">{{ $key }}</p>
                                                                    <p class="text-lg font-black text-red-600 dark:text-red-400">
                                                                        ${{ number_format((float)$val, 2) }}
                                                                    </p>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="mt-4 flex justify-end">
                                                            <div class="bg-red-50 dark:bg-red-900/20 px-4 py-2 rounded-lg border border-red-100 dark:border-red-900/30">
                                                                <span class="text-xs font-bold text-red-700 dark:text-red-300 uppercase">Total Retenido:</span>
                                                                <span class="text-lg font-black text-red-600 dark:text-red-400 ml-2">${{ number_format($categorized['total_retenido_a'], 2) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Total Neto -->
                                                    <div class="mt-12 flex justify-center">
                                                        <div class="bg-blue-600 text-white px-10 py-6 rounded-3xl shadow-2xl transform hover:scale-105 transition-transform">
                                                            <p class="text-xs uppercase font-bold tracking-widest opacity-80 mb-1 text-center">Líquido a Pagar</p>
                                                            <p class="text-5xl font-black text-center">${{ number_format($categorized['liquido'], 2) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                @else
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-8 rounded-2xl flex items-center shadow-sm">
                        <div class="flex-shrink-0 mr-6">
                            <svg class="h-10 w-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-yellow-800 dark:text-yellow-200">Sin coincidencias</h3>
                            <p class="text-gray-700 dark:text-gray-300 mt-1">
                                No se encontraron registros para el empleado <strong>{{ $id_empleado }}</strong>{{ $selected_periodo ? ' en la quincena ' . $selected_periodo : '' }}.
                            </p>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</body>
</html>
