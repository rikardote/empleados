<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sindicato | Dashboard de Gestión</title>
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

        .glow-blue:hover { box-shadow: 0 0 40px rgba(59, 130, 246, 0.15); }
        .glow-indigo:hover { box-shadow: 0 0 40px rgba(99, 102, 241, 0.15); }
        .glow-emerald:hover { box-shadow: 0 0 40px rgba(16, 185, 129, 0.15); }
        .glow-orange:hover { box-shadow: 0 0 40px rgba(249, 115, 22, 0.15); }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="bg-[#0a0a0a] text-white min-h-screen selection:bg-blue-500/30">
    
    <div class="mesh-gradient"></div>

    <div class="relative z-10 max-w-6xl mx-auto px-6 py-20 lg:py-32">
        <!-- Header -->
        <header class="text-center mb-24 max-w-3xl mx-auto">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/5 border border-white/10 text-blue-400 text-xs font-bold uppercase tracking-widest mb-8 floating">
                <span class="relative flex h-2 w-2 mr-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                Panel Administrativo v2.0
            </div>
            <h1 class="text-6xl lg:text-8xl font-black tracking-tight mb-8 leading-none">
                Gestión de <br/>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-500">
                    Plantillas
                </span>
            </h1>
            <p class="text-xl text-gray-400 leading-relaxed font-light">
                Plataforma avanzada para el análisis, comparación y gestión <br class="hidden md:block"/> de capital humano con tecnología de vanguardia.
            </p>
        </header>

        <!-- Tool Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Concept Report -->
            <a href="{{ route('employees.concept-report') }}" class="group">
                <div class="glass-card glow-blue p-10 rounded-[2.5rem] h-full relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl transition-all duration-500 group-hover:bg-blue-500/20"></div>
                    
                    <div class="flex items-center justify-between mb-8">
                        <div class="w-16 h-16 bg-blue-500/10 border border-blue-500/20 rounded-2xl flex items-center justify-center group-hover:bg-blue-500/20 transition-all duration-500">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Estadísticas</div>
                    </div>
                    
                    <h2 class="text-3xl font-bold mb-4">Reporte por Sindicato</h2>
                    <p class="text-gray-400 leading-relaxed mb-10 text-lg">
                        Visualización analítica completa de la distribución de empleados a través de las diferentes organizaciones sindicales.
                    </p>
                    
                    <div class="flex items-center text-blue-400 font-bold group-hover:gap-4 transition-all">
                        <span>Lanzar herramienta</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </div>
            </a>

            <!-- Compare Periods -->
            <a href="{{ route('employees.compare-periods') }}" class="group">
                <div class="glass-card glow-indigo p-10 rounded-[2.5rem] h-full relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl transition-all duration-500 group-hover:bg-indigo-500/20"></div>

                    <div class="flex items-center justify-between mb-8">
                        <div class="w-16 h-16 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl flex items-center justify-center group-hover:bg-indigo-500/20 transition-all duration-500">
                            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Comparativa</div>
                    </div>

                    <h2 class="text-3xl font-bold mb-4">Comparar Quincenas</h2>
                    <p class="text-gray-400 leading-relaxed mb-10 text-lg">
                        Módulo de auditoría inteligente para detectar automáticamente altas, bajas y cambios estructurales entre periodos.
                    </p>

                    <div class="flex items-center text-indigo-400 font-bold group-hover:gap-4 transition-all">
                        <span>Iniciar análisis</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </div>
            </a>

            <!-- Import Data -->
            <a href="{{ route('employees.import-form') }}" class="group">
                <div class="glass-card glow-emerald p-10 rounded-[2.5rem] h-full relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl transition-all duration-500 group-hover:bg-emerald-500/20"></div>

                    <div class="flex items-center justify-between mb-8">
                        <div class="w-16 h-16 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-center group-hover:bg-emerald-500/20 transition-all duration-500">
                            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                            </svg>
                        </div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Carga Datos</div>
                    </div>

                    <h2 class="text-3xl font-bold mb-4">Importar Nómina</h2>
                    <p class="text-gray-400 leading-relaxed mb-10 text-lg">
                        Integración simplificada de archivos Excel y CSV para la actualización masiva de la base de datos centralizada.
                    </p>

                    <div class="flex items-center text-emerald-400 font-bold group-hover:gap-4 transition-all">
                        <span>Subir quincena</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </div>
            </a>

            <!-- Individual Search -->
            <a href="{{ route('employees.search') }}" class="group">
                <div class="glass-card glow-orange p-10 rounded-[2.5rem] h-full relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl transition-all duration-500 group-hover:bg-orange-500/20"></div>

                    <div class="flex items-center justify-between mb-8">
                        <div class="w-16 h-16 bg-orange-500/10 border border-orange-500/20 rounded-2xl flex items-center justify-center group-hover:bg-orange-500/20 transition-all duration-500">
                            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Consulta</div>
                    </div>

                    <h2 class="text-3xl font-bold mb-4">Consulta Individual</h2>
                    <p class="text-gray-400 leading-relaxed mb-10 text-lg">
                        Explorador detallado de perfiles para auditar el historial laboral y conceptos de nómina de cada colaborador.
                    </p>

                    <div class="flex items-center text-orange-400 font-bold group-hover:gap-4 transition-all">
                        <span>Localizar perfil</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Footer -->
        <footer class="mt-32 pt-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6 text-gray-600">
            <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-[0.2em]">
                <span class="text-white/20">Powering</span>
                <span class="text-blue-500/50">Human Resources Intelligence</span>
            </div>
            <div class="flex gap-8 text-[10px] font-bold uppercase tracking-widest">
                <span class="hover:text-white transition-colors cursor-default">Laravel v{{ app()->version() }}</span>
                <span class="hover:text-white transition-colors cursor-default">PHP v{{ PHP_VERSION }}</span>
            </div>
        </footer>
    </div>

</body>
</html>
