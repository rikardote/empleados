# Plan de Migración: Sistema de Incidencias (Laravel 5.2 -> 11)

Este documento detalla la estrategia para reconstruir la aplicación en una arquitectura moderna mientras mantenemos el sistema actual en producción.

## 1. Objetivos del Nuevo Proyecto
*   **Versión**: Laravel 11.x (PHP 8.3).
*   **Arquitectura**: Capa de Servicios (Service Layer) para lógica de negocio.
*   **Frontend**: Bootstrap 5 + Vanilla JS (para mantener coherencia actual) o Laravel Livewire para una experiencia más rápida.
*   **Seguridad**: Implementar Laravel Breeze para manejo de sesiones y roles.
*   **Base de Datos**: Reutilizar el esquema actual (`sistemas`) pero gestionado por migraciones modernas.

## 2. Fase 1: Infraestructura y Base de Datos
1.  **Crear Contenedor Paralelo**: Configurar un Docker separado con PHP 8.3 y Nginx.
2.  **Inicializar Laravel 11**: Instalación limpia sin copiar archivos del proyecto viejo inicialmente.
3.  **Mapeo de Base de Datos**:
    *   En el nuevo proyecto, crear migraciones que representen las tablas actuales.
    *   Usar `increments` o `bigIncrements` según corresponda para no romper las relaciones existentes.
    *   Identificar si hay tablas que requieran limpieza o normalización.

## 3. Fase 2: Mudar la Lógica Core (Services)
Esta parte será la más fluida gracias al trabajo de refactorización que ya iniciamos:
1.  **Migrar Constantes**: Mover `App\Constants\Incidencias` a una ubicación similar.
2.  **Services de Incidencias**:
    *   Mudar `SegmentadorQuincenal.php`.
    *   Mudar las Reglas (`Rules/`) adaptándolas a las nuevas versiones de Eloquent.
    *   Migrar `IncidenciasService.php`.
3.  **Service de Empleados**: Trasladar la lógica de filtrado y gestión de expedientes.

## 4. Fase 3: Autenticación y Usuarios
1.  **Laravel Breeze**: Instalar para tener un login seguro y moderno desde el día 1.
2.  **Migración de Usuarios**:
    *   Los passwords en 5.2 usan `bcrypt`, que sigue siendo compatible.
    *   Integrar la nueva columna `active` que creamos recientemente.
3.  **Roles y Permisos**: Implementar middleware moderno para distinguir entre Admin, Consulta y Operador.

## 5. Fase 4: Frontend y Reportes
1.  **Vistas (Views)**:
    *   Rediseñar la estructura de `layouts/main.blade.php`.
    *   Migrar las tablas de catálogos (Usuarios, Qnas, Puestos) usando componentes limpios.
2.  **Módulo de Reportes (Excel/PDF)**:
    *   Actualizar `Maatwebsite/Excel` a la v3.x (requiere reescritura de los archivos de exportación).
    *   Actualizar `MPDF` para los recibos oficiales.

## 6. Fase 5: Pruebas y Validación
1.  **Pest o PHPUnit**: Convertir las pruebas que hicimos en la versión vieja al formato de Laravel 11.
2.  **Doble Entrada**: Durante una quincena, capturar en ambos sistemas para validar que los totales de días y pases coincidan al 100%.

## 7. Próximos pasos inmediatos
1.  Definir la ruta del nuevo directorio (ej: `../incidencias-v11`).
2.  Configurar el `docker-compose.yml` para soportar ambas versiones.
3.  Comenzar con la migración de las tablas maestras (`deparments`, `puestos`, `qnas`).
