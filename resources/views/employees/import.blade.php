<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Importar Quincena - {{ config('app.name', 'Laravel') }}</title>
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

        <div class="max-w-2xl mx-auto">
            <h1 class="text-4xl font-bold mb-8 text-gray-900 dark:text-white">Importar Quincena</h1>
            
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-[#161615] rounded-xl shadow-sm p-8 border border-gray-200 dark:border-[#3E3E3A]">
                <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    

                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Archivo Excel / CSV</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-[#3E3E3A] border-dashed rounded-md hover:border-blue-400 transition-colors">
                            <div class="space-y-1 text-center" id="drop-zone">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                    <label for="file" class="relative cursor-pointer bg-transparent rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span id="file-label">Sube un archivo</span>
                                        <input id="file" name="file" type="file" class="sr-only" required accept=".xlsx,.xls,.csv">
                                    </label>
                                    <p class="pl-1">o arrastra y suelta</p>
                                </div>
                                <p class="text-xs text-gray-500">XLSX, XLS o CSV hasta 100MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-lg">
                            Iniciar Importación
                        </button>
                    </div>
                </form>
            </div>

            <!-- List of Imported Files -->
            @if(count($importFiles) > 0)
                <div class="mt-12 bg-white dark:bg-[#161615] rounded-xl shadow-sm border border-gray-200 dark:border-[#3E3E3A] overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-[#3E3E3A] bg-gray-50 dark:bg-[#0d0d0c]">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Archivos Importados Disponibles</h3>
                    </div>
                    <div class="p-6">
                        <ul class="divide-y divide-gray-100 dark:divide-[#3E3E3A]">
                            @foreach($importFiles as $file)
                                <li class="py-4 flex items-center justify-between gap-4">
                                    <div class="flex items-center min-w-0">
                                        <svg class="h-6 w-6 text-gray-400 mr-3 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="min-w-0 truncate">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $file['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ date('d/m/Y H:i', $file['date']) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ $file['url'] }}" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400 rounded-md hover:bg-blue-100 transition-colors">
                                            Descargar
                                        </a>
                                        <form action="{{ route('employees.delete-import', ['filename' => $file['name']]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este archivo y TODOS los registros de empleados asociados?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400 rounded-md hover:bg-red-100 transition-colors">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('file');
        const fileLabel = document.getElementById('file-label');
        const dropZone = document.getElementById('drop-zone');
        
        // Función para actualizar el label
        function updateFileLabel(file) {
            if (file) {
                fileLabel.textContent = file.name;
                fileLabel.classList.remove('text-blue-600');
                fileLabel.classList.add('text-green-600', 'font-bold');
            }
        }

        fileInput.onchange = function() {
            if (this.files[0]) {
                updateFileLabel(this.files[0]);
            }
        };

        // Drag & Drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
            
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files; // Asignar archivos al input
                updateFileLabel(files[0]);
            }
        });
    </script>
</body>
</html>
