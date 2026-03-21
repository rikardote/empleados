<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos de Personal - FM1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen p-4 md:p-8">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Movimientos de Personal</h1>
                <p class="text-slate-500">Gestión y llenado de formatos FM1</p>
            </div>
            <a href="{{ route('pdf.fill') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Registro
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                <p class="text-emerald-700 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Formulario (Lado Izquierdo) -->
            <div class="lg:col-span-2 space-y-8">
                <form action="{{ $selectedForm ? route('pdf.update', $selectedForm->id) : route('pdf.process') }}" method="POST" class="space-y-8 pb-20">
                    @csrf
                    
                    <!-- Sección 1: Datos del Trabajador -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-50 px-8 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                1. Datos del Trabajador
                            </h2>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre Completo</label>
                                    <input type="text" name="nombre" required value="{{ old('nombre', $selectedForm->nombre ?? '') }}"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Número de Empleado</label>
                                    <input type="text" name="num_empleado" value="{{ old('num_empleado', $selectedForm->num_empleado ?? '') }}"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">CURP</label>
                                        <input type="text" name="curp" maxlength="18" value="{{ old('curp', $selectedForm->curp ?? '') }}"
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">RFC</label>
                                        <input type="text" name="rfc" maxlength="13" value="{{ old('rfc', $selectedForm->rfc ?? '') }}"
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Sexo</label>
                                        <select name="sexo" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                            <option value="">Seleccionar...</option>
                                            <option value="MASCULINO" {{ old('sexo', $selectedForm->sexo ?? '') == 'MASCULINO' ? 'selected' : '' }}>Masculino</option>
                                            <option value="FEMENINO" {{ old('sexo', $selectedForm->sexo ?? '') == 'FEMENINO' ? 'selected' : '' }}>Femenino</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nacionalidad</label>
                                        <input type="text" name="nacionalidad" value="{{ old('nacionalidad', $selectedForm->nacionalidad ?? 'MEXICANA') }}"
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Escolaridad</label>
                                    <input type="text" name="escolaridad" value="{{ old('escolaridad', $selectedForm->escolaridad ?? '') }}"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Cédula Profesional</label>
                                    <input type="text" name="cedula" value="{{ old('cedula', $selectedForm->cedula ?? '') }}"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Hijos</label>
                                    <input type="text" name="hijos" value="{{ old('hijos', $selectedForm->hijos ?? '') }}"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Domicilio</label>
                                    <textarea name="domicilio" rows="2" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all resize-none">{{ old('domicilio', $selectedForm->domicilio ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 2: Tipo de Movimiento -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-50 px-8 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                2. Tipo de Movimiento
                            </h2>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Código</label>
                                <input type="text" name="cod_tipo_movimiento" value="{{ old('cod_tipo_movimiento', $selectedForm->cod_tipo_movimiento ?? '') }}"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none transition-all"
                                    placeholder="Ej: 01, 10, 20...">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Descripción del Movimiento</label>
                                <input type="text" name="tipo_mov" value="{{ old('tipo_mov', $selectedForm->tipo_mov ?? '') }}"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none transition-all"
                                    placeholder="Ej: Alta, Baja, Reingreso...">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha Inicial</label>
                                <input type="date" name="fecha_movimiento" value="{{ old('fecha_movimiento', optional($selectedForm)->fecha_movimiento?->format('Y-m-d')) }}"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha Final</label>
                                <input type="date" name="fecha_final" value="{{ old('fecha_final', optional($selectedForm)->fecha_final?->format('Y-m-d')) }}"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Sección 3: Datos de la Plaza -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-50 px-8 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                3. Datos de la Plaza
                            </h2>
                        </div>
                        <div class="p-8 space-y-8">
                            <!-- Identificación -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Código de Puesto</label>
                                    <input type="text" name="codigo_puesto" value="{{ old('codigo_puesto', $selectedForm->codigo_puesto ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nivel/Subnivel</label>
                                    <input type="text" name="nivel_subnivel" value="{{ old('nivel_subnivel', $selectedForm->nivel_subnivel ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Denominación</label>
                                    <input type="text" name="denominacion_puesto" value="{{ old('denominacion_puesto', $selectedForm->denominacion_puesto ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Número de Plaza</label>
                                    <input type="text" name="numero_plaza" value="{{ old('numero_plaza', $selectedForm->numero_plaza ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tipo de Plaza</label>
                                    <input type="text" name="tipo_plaza" value="{{ old('tipo_plaza', $selectedForm->tipo_plaza ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ocupación</label>
                                    <input type="text" name="ocupacion" value="{{ old('ocupacion', $selectedForm->ocupacion ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Estatus</label>
                                    <input type="text" name="estatus_plaza" value="{{ old('estatus_plaza', $selectedForm->estatus_plaza ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <!-- Ubicación -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-4 gap-2">
                                        <div class="col-span-1">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Unidad Adm.</label>
                                            <input type="text" name="unidad_administrativa" value="{{ old('unidad_administrativa', $selectedForm->unidad_administrativa ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                        </div>
                                        <div class="col-span-3">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Denominación Unidad</label>
                                            <input type="text" name="unidad_administrativa_denominacion" value="{{ old('unidad_administrativa_denominacion', $selectedForm->unidad_administrativa_denominacion ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2">
                                        <div class="col-span-1">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Adscripción</label>
                                            <input type="text" name="adscripcion" value="{{ old('adscripcion', $selectedForm->adscripcion ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                        </div>
                                        <div class="col-span-3">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Denominación Adsc.</label>
                                            <input type="text" name="adscripcion_denominacion" value="{{ old('adscripcion_denominacion', $selectedForm->adscripcion_denominacion ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2">
                                        <div class="col-span-1">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ads. Física</label>
                                            <input type="text" name="adscripcion_fisica" value="{{ old('adscripcion_fisica', $selectedForm->adscripcion_fisica ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                        </div>
                                        <div class="col-span-3">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Denominación Fis.</label>
                                            <input type="text" name="adscripcion_fisica_denominacion" value="{{ old('adscripcion_fisica_denominacion', $selectedForm->adscripcion_fisica_denominacion ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-4 gap-2">
                                        <div class="col-span-1">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Servicio</label>
                                            <input type="text" name="servicio" value="{{ old('servicio', $selectedForm->servicio ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                        </div>
                                        <div class="col-span-3">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Denominación Serv.</label>
                                            <input type="text" name="servicio_denominacion" value="{{ old('servicio_denominacion', $selectedForm->servicio_denominacion ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2">
                                        <div class="col-span-1">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cod. Turno</label>
                                            <input type="text" name="codigo_turno" value="{{ old('codigo_turno', $selectedForm->codigo_turno ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                        </div>
                                        <div class="col-span-3">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Descripción Turno</label>
                                            <input type="text" name="codigo_turno_descripcion" value="{{ old('codigo_turno_descripcion', $selectedForm->codigo_turno_descripcion ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jornada</label>
                                        <input type="text" name="jornada" value="{{ old('jornada', $selectedForm->jornada ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                    </div>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <!-- Horarios -->
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Horario Cod.</label>
                                    <input type="text" name="horario_codigo" value="{{ old('horario_codigo', $selectedForm->horario_codigo ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Entrada 1</label>
                                    <input type="text" name="horario_entrada1" value="{{ old('horario_entrada1', $selectedForm->horario_entrada1 ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg" placeholder="00:00">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Salida 1</label>
                                    <input type="text" name="horario_salida1" value="{{ old('horario_salida1', $selectedForm->horario_salida1 ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg" placeholder="00:00">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Entrada 2</label>
                                    <input type="text" name="horario_entrada2" value="{{ old('horario_entrada2', $selectedForm->horario_entrada2 ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg" placeholder="00:00">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Salida 2</label>
                                    <input type="text" name="horario_salida2" value="{{ old('horario_salida2', $selectedForm->horario_salida2 ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg" placeholder="00:00">
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <!-- Opciones y Características -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                @php
                                    $checkboxFields = [
                                        'turno_opcional' => 'Turno Opcional',
                                        'percepcion_adicional' => 'Percepción Adic.',
                                        'mando' => 'Mando',
                                        'enlace_alta_responsabilidad' => 'Enlace Alta Resp.',
                                        'enlace' => 'Enlace',
                                        'operativo' => 'Operativo',
                                        'rama_medica' => 'Rama Médica/Param.'
                                    ];
                                @endphp
                                @foreach($checkboxFields as $key => $label)
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">{{ $label }}</label>
                                        <select name="{{ $key }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                                            <option value="----" {{ old($key, $selectedForm->$key ?? '') == '----' ? 'selected' : '' }}>----</option>
                                            <option value="X" {{ old($key, $selectedForm->$key ?? '') == 'X' ? 'selected' : '' }}>X</option>
                                        </select>
                                    </div>
                                @endforeach
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Riesgos Prof. (%)</label>
                                    <input type="text" name="riesgos_profesionales" value="{{ old('riesgos_profesionales', $selectedForm->riesgos_profesionales ?? '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg" placeholder="XX%">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Observaciones</label>
                                <textarea name="observaciones" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none transition-all resize-none">{{ old('observaciones', $selectedForm->observaciones ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Sección 4: Antecedentes de Ocupación de la Plaza -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-50 px-8 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                4. Antecedentes de Ocupación de la Plaza
                            </h2>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del Anterior Trabajador</label>
                                        <input type="text" name="nombre_ant" value="{{ old('nombre_ant', $selectedForm->nombre_ant ?? '') }}"
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Núm. Empleado Ant.</label>
                                            <input type="text" name="num_empleado_ant" value="{{ old('num_empleado_ant', $selectedForm->num_empleado_ant ?? '') }}"
                                                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Cod. Movimiento</label>
                                            <input type="text" name="cod_movi_ant" value="{{ old('cod_movi_ant', $selectedForm->cod_movi_ant ?? '') }}"
                                                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tipo de Movimiento Ant.</label>
                                        <input type="text" name="tipo_mov_ant" value="{{ old('tipo_mov_ant', $selectedForm->tipo_mov_ant ?? '') }}"
                                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha Inicio</label>
                                            <input type="date" name="fecha_inicio_ant" value="{{ old('fecha_inicio_ant', optional($selectedForm)->fecha_inicio_ant?->format('Y-m-d')) }}"
                                                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha Fin</label>
                                            <input type="date" name="fecha_fin_ant" value="{{ old('fecha_fin_ant', optional($selectedForm)->fecha_fin_ant?->format('Y-m-d')) }}"
                                                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="border-slate-100">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del Trabajador que Ocupará la Plaza</label>
                                    <input type="text" name="nombre_trab_ant" value="{{ old('nombre_trab_ant', $selectedForm->nombre_trab_ant ?? '') }}"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    @php
                                        $antCheckboxFields = [
                                            'turno_opcional_ant' => 'Turno Opc.',
                                            'percepcion_adicional_ant' => 'Perc. Adic.',
                                            'riesgos_prof_ant' => 'Riesgos Prof.'
                                        ];
                                    @endphp
                                    @foreach($antCheckboxFields as $key => $label)
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">{{ $label }}</label>
                                            <select name="{{ $key }}" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs">
                                                <option value="---" {{ old($key, $selectedForm->$key ?? '') == '---' ? 'selected' : '' }}>---</option>
                                                <option value="X" {{ old($key, $selectedForm->$key ?? '') == 'X' ? 'selected' : '' }}>X</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 5: Firmas y Cargos -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-50 px-8 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                5. Firmas y Cargos
                            </h2>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Titular del Área</label>
                                <input type="text" name="titular_area" value="{{ old('titular_area', $selectedForm->titular_area ?? '') }}"
                                    placeholder="Nombre completo"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 outline-none transition-all mb-2">
                                <input type="text" name="cargo_titular_area" value="{{ old('cargo_titular_area', $selectedForm->cargo_titular_area ?? '') }}"
                                    placeholder="Cargo ostentado"
                                    class="w-full px-4 py-1.5 border border-slate-100 bg-slate-50 rounded-lg focus:ring-2 focus:ring-rose-300 outline-none text-sm transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Responsable Admvo.</label>
                                <input type="text" name="responsable_admvo" value="{{ old('responsable_admvo', $selectedForm->responsable_admvo ?? '') }}"
                                    placeholder="Nombre completo"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 outline-none transition-all mb-2">
                                <input type="text" name="cargo_responsable_admvo" value="{{ old('cargo_responsable_admvo', $selectedForm->cargo_responsable_admvo ?? '') }}"
                                    placeholder="Cargo ostentado"
                                    class="w-full px-4 py-1.5 border border-slate-100 bg-slate-50 rounded-lg focus:ring-2 focus:ring-rose-300 outline-none text-sm transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Titular del Centro</label>
                                <input type="text" name="titular_centro" value="{{ old('titular_centro', $selectedForm->titular_centro ?? '') }}"
                                    placeholder="Nombre completo"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 outline-none transition-all mb-2">
                                <input type="text" name="cargo_titular_centro" value="{{ old('cargo_titular_centro', $selectedForm->cargo_titular_centro ?? '') }}"
                                    placeholder="Cargo ostentado"
                                    class="w-full px-4 py-1.5 border border-slate-100 bg-slate-50 rounded-lg focus:ring-2 focus:ring-rose-300 outline-none text-sm transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="fixed bottom-8 left-4 right-4 md:static md:bottom-0 md:left-0 md:right-0">
                        <div class="max-w-6xl mx-auto flex gap-4">
                            <button type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                {{ $selectedForm ? 'Actualizar y Generar' : 'Guardar y Generar' }}
                            </button>
                            @if($selectedForm)
                                <a href="{{ route('pdf.fill') }}" class="px-8 py-4 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all flex items-center">
                                    Cancelar
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Listado (Lado Derecho) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col h-full max-h-[1200px]">
                    <div class="p-6 border-b border-slate-100">
                        <h2 class="text-xl font-bold text-slate-900">Registros Recientes</h2>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        @forelse($forms as $form)
                            <div class="p-4 rounded-xl border {{ $selectedForm && $selectedForm->id == $form->id ? 'border-blue-500 bg-blue-50' : 'border-slate-100 bg-slate-50' }} hover:border-slate-300 transition-all group">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm truncate max-w-[150px]">{{ $form->nombre }}</p>
                                        <p class="text-xs text-slate-500">{{ $form->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('pdf.fill', $form->id) }}" class="p-1.5 bg-white border border-slate-200 rounded-md text-slate-600 hover:text-blue-600 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                        <form action="{{ route('pdf.destroy', $form->id) }}" method="POST" onsubmit="return confirm('¿Seguro?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-white border border-slate-200 rounded-md text-slate-600 hover:text-red-600 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span class="text-[10px] px-2 py-0.5 bg-blue-100 text-blue-700 rounded font-bold uppercase tracking-tight">{{ $form->tipo_movimiento ?? 'SIN TIPO' }}</span>
                                    <span class="text-[10px] px-2 py-0.5 bg-slate-200 rounded text-slate-600 font-bold tracking-tight">ID: {{ $form->num_empleado ?? 'N/A' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <p class="text-slate-400 text-sm italic">No hay registros aún.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
