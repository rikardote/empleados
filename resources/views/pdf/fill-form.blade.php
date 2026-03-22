<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movimientos de Personal - FM1 | Gestión de Plantilla</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            overflow-x: hidden;
        }
        
        .mesh-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: #0a0a0a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.2) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.2) 0, transparent 50%), 
                radial-gradient(at 0% 100%, hsla(321,75%,40%,0.1) 0, transparent 50%), 
                radial-gradient(at 100% 100%, hsla(180,100%,50%,0.05) 0, transparent 50%);
            filter: blur(80px);
            animation: meshMove 20s ease infinite alternate;
        }

        @keyframes meshMove {
            0% { transform: scale(1) translate(0, 0); }
            50% { transform: scale(1.1) translate(-2%, 2%); }
            100% { transform: scale(1) translate(0, 0); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-8px) scale(1.02);
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px #1a1a1a inset !important;
            -webkit-text-fill-color: white !important;
        }
    </style>
</head>
<body class="bg-[#0a0a0a] text-white/90 min-h-screen p-4 md:p-8 font-sans antialiased selection:bg-blue-500/30 relative">
    
    <div class="mesh-gradient"></div>

    <div class="max-w-6xl mx-auto space-y-10 relative z-10">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl lg:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-500 tracking-tight mb-2 leading-tight">Movimientos de Personal</h1>
                <p class="text-gray-400 font-light text-lg">Gestión y llenado de formatos FM1 <span class="mx-2 text-white/10">|</span> <span class="text-indigo-400/80 font-bold">Smart Generator</span></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('pdf.inspector') }}" class="group relative inline-flex items-center px-5 py-3 bg-white/5 border border-rose-500/20 rounded-2xl text-sm font-bold text-rose-400 hover:text-white hover:border-rose-500/60 hover:bg-rose-500/10 transition-all duration-300 backdrop-blur-md">
                    <span class="mr-2 text-base">🎯</span>
                    Inspector de Coordenadas
                </a>
                <a href="{{ route('pdf.fill') }}" class="group relative inline-flex items-center px-6 py-3 bg-white/5 border border-white/10 rounded-2xl text-sm font-bold text-gray-300 hover:text-white hover:border-indigo-500/50 transition-all duration-500 backdrop-blur-md overflow-hidden">
                    <span class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-indigo-600/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-in-out"></span>
                    <svg class="w-5 h-5 mr-2 relative z-10 transition-transform group-hover:scale-110 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span class="relative z-10">Nuevo Registro</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                <p class="text-emerald-700 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-lg">
                <p class="text-rose-700 text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Stepper (Diseño Premium) -->
        <div class="glass-card rounded-[2.5rem] p-10 relative overflow-hidden">
            <div class="flex items-center justify-between max-w-4xl mx-auto relative mt-2">
                <!-- Línea de fondo -->
                <div class="absolute top-6 left-0 w-full h-1 bg-white/5 rounded-full -translate-y-1/2 z-0"></div>
                <!-- Línea de progreso -->
                <div id="progress-line" class="absolute top-6 left-0 h-1.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 rounded-full -translate-y-1/2 z-0 transition-all duration-700 ease-in-out shadow-sm shadow-indigo-500/30" style="width: 0%"></div>

                @foreach(['Trabajador', 'Movimiento', 'Plaza', 'Antecedentes', 'Firmas'] as $i => $label)
                    <div class="step-indicator relative z-10 flex flex-col items-center gap-3 group transition-transform duration-500" data-step="{{ $i + 1 }}">
                        <div class="step-circle w-12 h-12 rounded-2xl flex items-center justify-center font-black transition-all duration-500 shadow-sm border border-transparent
                            {{ $i == 0 ? 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-blue-500/40 ring-4 ring-blue-500/20 scale-110 -translate-y-1' : 'bg-white/5 border border-white/10 text-gray-500' }}">
                            {{ $i + 1 }}
                        </div>
                        <span class="step-label text-xs font-bold uppercase tracking-wider transition-all duration-300 {{ $i == 0 ? 'text-indigo-400' : 'text-gray-600' }}">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Formulario (Lado Izquierdo) -->
            <div class="lg:col-span-2 space-y-8">
                <form id="fm1-form" action="{{ $selectedForm ? route('pdf.update', $selectedForm->id) : route('pdf.process') }}" method="POST" class="space-y-8 pb-20">
                    @csrf
                    
                    <!-- Etapa 1: Datos del Trabajador -->
                    <div class="form-step" data-step="1">
                        <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-12 h-12 bg-blue-500/10 border border-blue-500/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black text-white leading-tight">Datos del Trabajador</h2>
                                    <p class="text-gray-500 text-sm font-medium">Información básica y profesional del colaborador</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Nombre Completo</label>
                                        <input type="text" name="nombre" required value="{{ old('nombre', $selectedForm->nombre ?? '') }}"
                                            class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Número de Empleado</label>
                                        <input type="text" name="num_empleado" value="{{ old('num_empleado', $selectedForm->num_empleado ?? '') }}"
                                            class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">CURP</label>
                                            <input type="text" name="curp" maxlength="18" value="{{ old('curp', $selectedForm->curp ?? '') }}"
                                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">RFC</label>
                                            <input type="text" name="rfc" maxlength="13" value="{{ old('rfc', $selectedForm->rfc ?? '') }}"
                                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Sexo</label>
                                            <select name="sexo" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium appearance-none">
                                                <option value="" class="bg-[#1a1a1a]">Seleccionar...</option>
                                                <option value="MASCULINO" {{ old('sexo', $selectedForm->sexo ?? '') == 'MASCULINO' ? 'selected' : '' }} class="bg-[#1a1a1a]">Masculino</option>
                                                <option value="FEMENINO" {{ old('sexo', $selectedForm->sexo ?? '') == 'FEMENINO' ? 'selected' : '' }} class="bg-[#1a1a1a]">Femenino</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Nacionalidad</label>
                                            <input type="text" name="nacionalidad" value="{{ old('nacionalidad', $selectedForm->nacionalidad ?? 'MEXICANA') }}"
                                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium">
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Escolaridad</label>
                                        <input type="text" name="escolaridad" value="{{ old('escolaridad', $selectedForm->escolaridad ?? '') }}"
                                            class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Cédula Profesional</label>
                                        <input type="text" name="cedula" value="{{ old('cedula', $selectedForm->cedula ?? '') }}"
                                            class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Hijos</label>
                                        <input type="text" name="hijos" value="{{ old('hijos', $selectedForm->hijos ?? '') }}"
                                            class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Domicilio</label>
                                        <textarea name="domicilio" rows="2" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium resize-none">{{ old('domicilio', $selectedForm->domicilio ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end mt-12">
                                <button type="button" class="next-step px-10 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-500 flex items-center gap-3 shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:-translate-y-1 group">
                                    Siguiente: Movimiento
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 2: Movimiento -->
                    <div class="form-step hidden" data-step="2">
                        <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-12 h-12 bg-purple-500/10 border border-purple-500/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black text-white leading-tight">Tipo de Movimiento</h2>
                                    <p class="text-gray-500 text-sm font-medium">Naturaleza y temporalidad del cambio</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Código</label>
                                    <input type="text" name="cod_tipo_movimiento" value="{{ old('cod_tipo_movimiento', $selectedForm->cod_tipo_movimiento ?? '') }}"
                                        class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium"
                                        placeholder="Ej: 01, 10, 20...">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Descripción</label>
                                    <input type="text" name="tipo_mov" value="{{ old('tipo_mov', $selectedForm->tipo_mov ?? '') }}"
                                        class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium"
                                        placeholder="Ej: Alta, Baja, Reingreso...">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Fecha Inicial</label>
                                    <input type="date" name="fecha_movimiento" value="{{ old('fecha_movimiento', optional($selectedForm)->fecha_movimiento?->format('Y-m-d')) }}"
                                        class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Fecha Final</label>
                                    <input type="date" name="fecha_final" value="{{ old('fecha_final', optional($selectedForm)->fecha_final?->format('Y-m-d')) }}"
                                        class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition-all duration-500 text-white placeholder-gray-600 shadow-sm hover:border-white/20 focus:shadow-md font-medium">
                                </div>
                            </div>
                            <div class="flex justify-between mt-12">
                                <button type="button" class="prev-step px-8 py-5 bg-white/5 border border-white/10 text-gray-400 font-black rounded-2xl hover:bg-white/10 hover:text-white transition-all duration-500 flex items-center gap-3 backdrop-blur-md">
                                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                    Anterior
                                </button>
                                <button type="button" class="next-step px-10 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-500 flex items-center gap-3 shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:-translate-y-1 group">
                                    Siguiente: Plaza
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 3: Plaza y Ubicación -->
                    <div class="form-step hidden" data-step="3">
                        <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden text-gray-300">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-12 h-12 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black text-white leading-tight">Datos de la Plaza</h2>
                                    <p class="text-gray-500 text-sm font-medium">Ubicación física y administrativa del puesto</p>
                                </div>
                            </div>

                            <div class="p-0 space-y-10">
                                <!-- Identificación -->
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Código de Puesto</label>
                                        <input type="text" name="codigo_puesto" value="{{ old('codigo_puesto', $selectedForm->codigo_puesto ?? '') }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all duration-300 text-white placeholder-gray-600">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Nivel/Subnivel</label>
                                        <input type="text" name="nivel_subnivel" value="{{ old('nivel_subnivel', $selectedForm->nivel_subnivel ?? '') }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all duration-300 text-white placeholder-gray-600">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Denominación</label>
                                        <input type="text" name="denominacion_puesto" value="{{ old('denominacion_puesto', $selectedForm->denominacion_puesto ?? '') }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all duration-300 text-white placeholder-gray-600">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Número de Plaza</label>
                                        <input type="text" name="numero_plaza" value="{{ old('numero_plaza', $selectedForm->numero_plaza ?? '') }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all duration-300 text-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Tipo de Plaza</label>
                                        <input type="text" name="tipo_plaza" value="{{ old('tipo_plaza', $selectedForm->tipo_plaza ?? '') }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all duration-300 text-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Ocupación</label>
                                        <input type="text" name="ocupacion" value="{{ old('ocupacion', $selectedForm->ocupacion ?? '') }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all duration-300 text-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Estatus</label>
                                        <input type="text" name="estatus_plaza" value="{{ old('estatus_plaza', $selectedForm->estatus_plaza ?? '') }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all duration-300 text-white">
                                    </div>
                                </div>

                                <div class="h-px bg-white/5"></div>

                                <!-- Ubicación -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    <div class="space-y-6">
                                        <div class="grid grid-cols-4 gap-4">
                                            <div class="col-span-1">
                                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Unidad Adm.</label>
                                                <input type="text" name="unidad_administrativa" value="{{ old('unidad_administrativa', $selectedForm->unidad_administrativa ?? '') }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl transition-all text-white">
                                            </div>
                                            <div class="col-span-3">
                                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Denominación Unidad</label>
                                                <input type="text" name="unidad_administrativa_denominacion" value="{{ old('unidad_administrativa_denominacion', $selectedForm->unidad_administrativa_denominacion ?? '') }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl transition-all text-white">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-4">
                                            <div class="col-span-1">
                                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Adscripción</label>
                                                <input type="text" name="adscripcion" value="{{ old('adscripcion', $selectedForm->adscripcion ?? '') }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl transition-all text-white">
                                            </div>
                                            <div class="col-span-3">
                                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Denominación Adsc.</label>
                                                <input type="text" name="adscripcion_denominacion" value="{{ old('adscripcion_denominacion', $selectedForm->adscripcion_denominacion ?? '') }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl transition-all text-white">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-6">
                                        <div class="grid grid-cols-4 gap-4">
                                            <div class="col-span-1">
                                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Servicio</label>
                                                <input type="text" name="servicio" value="{{ old('servicio', $selectedForm->servicio ?? '') }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl transition-all text-white">
                                            </div>
                                            <div class="col-span-3">
                                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Denominación Serv.</label>
                                                <input type="text" name="servicio_denominacion" value="{{ old('servicio_denominacion', $selectedForm->servicio_denominacion ?? '') }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl transition-all text-white">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-4">
                                            <div class="col-span-1">
                                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Cod. Turno</label>
                                                <input type="text" name="codigo_turno" value="{{ old('codigo_turno', $selectedForm->codigo_turno ?? '') }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl transition-all text-white">
                                            </div>
                                            <div class="col-span-3">
                                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Descripción Turno</label>
                                                <input type="text" name="codigo_turno_descripcion" value="{{ old('codigo_turno_descripcion', $selectedForm->codigo_turno_descripcion ?? '') }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl transition-all text-white">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="h-px bg-white/5"></div>

                                <!-- Horarios -->
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    @foreach(['horario_codigo' => 'Horario Cod.', 'horario_entrada1' => 'Entrada 1', 'horario_salida1' => 'Salida 1', 'horario_entrada2' => 'Entrada 2', 'horario_salida2' => 'Salida 2'] as $f => $l)
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">{{ $l }}</label>
                                            <input type="text" name="{{ $f }}" value="{{ old($f, $selectedForm->$f ?? '') }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-600 focus:bg-white/10 transition-all" placeholder="{{ str_contains($f, 'entrada') || str_contains($f, 'salida') ? '00:00' : '' }}">
                                        </div>
                                    @endforeach
                                </div>

                                <div class="h-px bg-white/5"></div>

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
                                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">{{ $label }}</label>
                                            <select name="{{ $key }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm text-white focus:bg-white/10 transition-all appearance-none">
                                                <option value="----" {{ old($key, $selectedForm->$key ?? '') == '----' ? 'selected' : '' }} class="bg-[#1a1a1a]">----</option>
                                                <option value="X" {{ old($key, $selectedForm->$key ?? '') == 'X' ? 'selected' : '' }} class="bg-[#1a1a1a]">X</option>
                                            </select>
                                        </div>
                                    @endforeach
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Riesgos Prof. (%)</label>
                                        <input type="text" name="riesgos_profesionales" value="{{ old('riesgos_profesionales', $selectedForm->riesgos_profesionales ?? '') }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-600 focus:bg-white/10 transition-all" placeholder="XX%">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Observaciones</label>
                                    <textarea name="observaciones" rows="3" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-gray-600 focus:bg-white/10 transition-all resize-none">{{ old('observaciones', $selectedForm->observaciones ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="flex justify-between mt-12">
                                <button type="button" class="prev-step px-8 py-5 bg-white/5 border border-white/10 text-gray-400 font-black rounded-2xl hover:bg-white/10 hover:text-white transition-all duration-500 flex items-center gap-3 backdrop-blur-md">
                                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                    Anterior
                                </button>
                                <button type="button" class="next-step px-10 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-500 flex items-center gap-3 shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:-translate-y-1 group">
                                    Siguiente: Antecedentes
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 4: Antecedentes -->
                    <div class="form-step hidden" data-step="4">
                        <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-12 h-12 bg-orange-500/10 border border-orange-500/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black text-white leading-tight">Antecedentes de Ocupación</h2>
                                    <p class="text-gray-500 text-sm font-medium">Historial previo de la vacante</p>
                                </div>
                            </div>

                            <div class="p-0 space-y-10">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Nombre del Anterior Trabajador</label>
                                            <input type="text" name="nombre_ant" value="{{ old('nombre_ant', $selectedForm->nombre_ant ?? '') }}"
                                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all duration-300 text-white placeholder-gray-600">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Núm. Empleado Ant.</label>
                                                <input type="text" name="num_empleado_ant" value="{{ old('num_empleado_ant', $selectedForm->num_empleado_ant ?? '') }}"
                                                    class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 outline-none transition-all text-white">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Cod. Movimiento</label>
                                                <input type="text" name="cod_movi_ant" value="{{ old('cod_movi_ant', $selectedForm->cod_movi_ant ?? '') }}"
                                                    class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 outline-none transition-all text-white">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Tipo de Movimiento Ant.</label>
                                            <input type="text" name="tipo_mov_ant" value="{{ old('tipo_mov_ant', $selectedForm->tipo_mov_ant ?? '') }}"
                                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 outline-none transition-all text-white font-medium">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Fecha Inicio</label>
                                                <input type="date" name="fecha_inicio_ant" value="{{ old('fecha_inicio_ant', optional($selectedForm)->fecha_inicio_ant?->format('Y-m-d')) }}"
                                                    class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 outline-none transition-all text-white font-medium">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Fecha Fin</label>
                                                <input type="date" name="fecha_fin_ant" value="{{ old('fecha_fin_ant', optional($selectedForm)->fecha_fin_ant?->format('Y-m-d')) }}"
                                                    class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 outline-none transition-all text-white font-medium">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="h-px bg-white/5"></div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Nombre del Trabajador que Ocupará la Plaza</label>
                                        <input type="text" name="nombre_trab_ant" value="{{ old('nombre_trab_ant', $selectedForm->nombre_trab_ant ?? '') }}"
                                            class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:bg-white/10 outline-none transition-all text-white">
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
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">{{ $label }}</label>
                                                <select name="{{ $key }}" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white appearance-none focus:bg-white/10">
                                                    <option value="---" {{ old($key, $selectedForm->$key ?? '') == '---' ? 'selected' : '' }} class="bg-[#1a1a1a]">---</option>
                                                    <option value="X" {{ old($key, $selectedForm->$key ?? '') == 'X' ? 'selected' : '' }} class="bg-[#1a1a1a]">X</option>
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between mt-12">
                                <button type="button" class="prev-step px-8 py-5 bg-white/5 border border-white/10 text-gray-400 font-black rounded-2xl hover:bg-white/10 hover:text-white transition-all duration-500 flex items-center gap-3 backdrop-blur-md">
                                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                    Anterior
                                </button>
                                <button type="button" class="next-step px-10 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-500 flex items-center gap-3 shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:-translate-y-1 group">
                                    Siguiente: Firmas
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 5: Firmas -->
                    <div class="form-step hidden" data-step="5">
                        <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-12 h-12 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black text-white leading-tight">Firmas y Cargos</h2>
                                    <p class="text-gray-500 text-sm font-medium">Validación final del documento</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                                <div class="space-y-4">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Titular del Área</label>
                                    <input type="text" name="titular_area" value="{{ old('titular_area', $selectedForm->titular_area ?? '') }}" placeholder="Nombre completo" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl outline-none text-white text-sm">
                                    <input type="text" name="cargo_titular_area" value="{{ old('cargo_titular_area', $selectedForm->cargo_titular_area ?? '') }}" placeholder="Cargo" class="w-full px-5 py-3 bg-white/5 border border-white/5 rounded-xl outline-none text-gray-400 text-xs">
                                </div>
                                <div class="space-y-4">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Responsable Admvo.</label>
                                    <input type="text" name="responsable_admvo" value="{{ old('responsable_admvo', $selectedForm->responsable_admvo ?? '') }}" placeholder="Nombre completo" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl outline-none text-white text-sm">
                                    <input type="text" name="cargo_responsable_admvo" value="{{ old('cargo_responsable_admvo', $selectedForm->cargo_responsable_admvo ?? '') }}" placeholder="Cargo" class="w-full px-5 py-3 bg-white/5 border border-white/5 rounded-xl outline-none text-gray-400 text-xs">
                                </div>
                                <div class="space-y-4">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Titular del Centro</label>
                                    <input type="text" name="titular_centro" value="{{ old('titular_centro', $selectedForm->titular_centro ?? '') }}" placeholder="Nombre completo" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl outline-none text-white text-sm">
                                    <input type="text" name="cargo_titular_centro" value="{{ old('cargo_titular_centro', $selectedForm->cargo_titular_centro ?? '') }}" placeholder="Cargo" class="w-full px-5 py-3 bg-white/5 border border-white/5 rounded-xl outline-none text-gray-400 text-xs">
                                </div>
                            </div>

                            <div class="p-8 bg-blue-500/5 border border-blue-500/10 rounded-3xl mb-12">
                                <div class="flex items-start gap-4">
                                    <div class="p-2 bg-blue-500/20 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-bold mb-1">Confirmación de Datos</h4>
                                        <p class="text-gray-400 text-sm">Verifique que no existan errores antes de finalizar. Toda la información será plasmada en el FM1 oficial.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center gap-6">
                                <button type="button" class="prev-step px-8 py-5 bg-white/5 border border-white/10 text-gray-400 font-black rounded-2xl hover:bg-white/10 hover:text-white transition-all duration-500 flex items-center gap-3 backdrop-blur-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                    Anterior
                                </button>
                                <div class="flex gap-4">
                                    @if($selectedForm)
                                        <a href="{{ route('pdf.fill') }}" class="px-8 py-5 bg-white/5 border border-white/10 text-rose-400 font-black rounded-2xl hover:bg-rose-500/10 transition-all duration-500">
                                            Cancelar
                                        </a>
                                    @endif
                                    <button type="submit" class="px-10 py-5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-black rounded-2xl hover:from-emerald-600 hover:to-teal-600 transition-all duration-500 flex items-center gap-3 shadow-xl shadow-emerald-500/20 group">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $selectedForm ? 'Actualizar' : 'Finalizar y Generar PDF' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </form>
            </div>

            <!-- Listado (Lado Derecho Premium) -->
            <div class="lg:col-span-1">
                <div class="glass-card rounded-[2.5rem] p-8 flex flex-col h-full max-h-[1200px] sticky top-8">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                        <h2 class="text-xl font-black text-white tracking-tight">Registros Recientes</h2>
                    </div>

                    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar space-y-4">
                        @forelse($forms as $form)
                            <a href="{{ route('pdf.fill', $form->id) }}" 
                                class="group block p-5 rounded-[1.5rem] border {{ $selectedForm && $selectedForm->id == $form->id ? 'bg-indigo-500/10 border-indigo-500/30' : 'bg-white/5 border-white/5' }} hover:bg-white/10 hover:border-indigo-500/20 transition-all duration-300 relative overflow-hidden">
                                @if($selectedForm && $selectedForm->id == $form->id)
                                    <div class="absolute left-0 top-0 h-full w-1.5 bg-indigo-500"></div>
                                @endif
                                
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">{{ $form->num_empleado ?? 'N/A' }}</span>
                                    <span class="text-[10px] text-gray-500 font-medium">{{ $form->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <h4 class="text-sm font-bold text-gray-200 group-hover:text-white transition-colors line-clamp-1 uppercase mb-2">{{ $form->nombre }}</h4>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] px-2 py-0.5 bg-blue-500/10 text-blue-400 rounded-md font-black uppercase tracking-tighter">{{ $form->tipo_movimiento ?? 'SIN TIPO' }}</span>
                                    <form action="{{ route('pdf.destroy', $form->id) }}" method="POST" onsubmit="return confirm('¿Seguro?')" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-rose-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-20">
                                <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/5">
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <p class="text-gray-500 text-sm">No hay registros aún</p>
                            </div>
                        @endforelse
                    </div>

                    <style>
                        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
                        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); border-radius: 10px; }
                        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
                        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
                    </style>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let currentStep = 1;
            const totalSteps = 5;
            const form = document.getElementById('fm1-form');
            const steps = document.querySelectorAll('.form-step');
            const indicators = document.querySelectorAll('.step-indicator');
            const circles = document.querySelectorAll('.step-circle');
            const labels = document.querySelectorAll('.step-label');
            const progressLine = document.getElementById('progress-line');

            const updateStepper = () => {
                const progressWidth = ((currentStep - 1) / (totalSteps - 1)) * 100;
                progressLine.style.width = `${progressWidth}%`;

                circles.forEach((circle, index) => {
                    const stepNum = index + 1;
                    const label = labels[index];
                    
                    if (stepNum < currentStep) {
                        // Completado (Emerald Style)
                        circle.className = 'step-circle w-12 h-12 rounded-2xl flex items-center justify-center font-black transition-all duration-500 shadow-lg bg-gradient-to-br from-emerald-500 to-teal-500 text-white shadow-emerald-500/20 z-10';
                        circle.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>';
                        label.className = 'step-label text-[10px] font-black uppercase tracking-wider text-emerald-500 transition-all duration-300';
                    } else if (stepNum === currentStep) {
                        // Activo (Blue/Indigo Style)
                        circle.className = 'step-circle w-12 h-12 rounded-2xl flex items-center justify-center font-black transition-all duration-500 shadow-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-blue-500/40 ring-4 ring-blue-500/20 scale-110 -translate-y-1 z-10';
                        circle.innerHTML = stepNum;
                        label.className = 'step-label text-[10px] font-black uppercase tracking-wider text-indigo-400 transition-all duration-300';
                    } else {
                        // Pendiente (Glass Style)
                        circle.className = 'step-circle w-12 h-12 rounded-2xl flex items-center justify-center font-black transition-all duration-500 bg-white/5 border border-white/10 text-gray-600';
                        circle.innerHTML = stepNum;
                        label.className = 'step-label text-[10px] font-bold uppercase tracking-wider text-gray-600 transition-all duration-300';
                    }
                });
            };

            const showStep = (stepNum) => {
                steps.forEach(step => {
                    step.classList.add('hidden');
                    if (step.dataset.step == stepNum) {
                        step.classList.remove('hidden');
                    }
                });
                window.scrollTo({ top: 0, behavior: 'smooth' });
                updateStepper();
            };

            // Botones Siguiente
            document.querySelectorAll('.next-step').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        showStep(currentStep);
                    }
                });
            });

            // Botones Anterior
            document.querySelectorAll('.prev-step').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (currentStep > 1) {
                        currentStep--;
                        showStep(currentStep);
                    }
                });
            });

            // Inicializar
            showStep(currentStep);
        });
    </script>
</body>
</html>
