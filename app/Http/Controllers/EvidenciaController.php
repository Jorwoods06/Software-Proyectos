<?php

namespace App\Http\Controllers;

use App\Models\Evidencia;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EvidenciaController extends Controller
{
    /**
     * Tipos de archivos permitidos
     */
    private const TIPOS_PERMITIDOS = [
        'imagen' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
        'pdf' => ['pdf'],
        'documento' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
    ];

    /**
     * Tamaño máximo de archivo en KB (10MB = 10240 KB)
     */
    private const TAMANO_MAXIMO = 10240;

    /**
     * Subir archivo de evidencia para una tarea
     */
    public function store(Request $request, $tareaId)
    {
        // Validar que la tarea existe
        $tarea = Tarea::find($tareaId);
        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'La tarea no existe.'
            ], 404);
        }

        // Validar archivo
        $request->validate([
            'archivo' => [
                'required',
                'file',
                'max:' . self::TAMANO_MAXIMO,
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    $extension = strtolower($value->getClientOriginalExtension());
                    $extensionesPermitidas = array_merge(
                        self::TIPOS_PERMITIDOS['imagen'],
                        self::TIPOS_PERMITIDOS['pdf'],
                        self::TIPOS_PERMITIDOS['documento']
                    );

                    if (!in_array($extension, $extensionesPermitidas)) {
                        $fail('El archivo debe ser una imagen (JPG, PNG, GIF, SVG, WEBP), PDF o documento Word/Excel (DOC, DOCX, XLS, XLSX, PPT, PPTX).');
                    }
                },
            ],
        ]);

        try {
            $archivo = $request->file('archivo');
            $extension = strtolower($archivo->getClientOriginalExtension());
            $nombreOriginal = $archivo->getClientOriginalName();
            
            // Determinar tipo de archivo
            $tipoArchivo = $this->determinarTipoArchivo($extension);
            
            // Generar nombre único para el archivo
            $nombreArchivo = $this->generarNombreUnico($tareaId, $nombreOriginal, $extension);
            
            // Ruta donde se guardará el archivo en S3
            $rutaArchivo = 'evidencias/' . $nombreArchivo;
            
            // Guardar archivo en S3 (privado)
            $archivoSubido = Storage::disk('s3')->put($rutaArchivo, file_get_contents($archivo), 'private');
            
            // Verificar que el archivo se subió correctamente
            if (!$archivoSubido) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir el archivo a S3.'
                ], 500);
            }
            
            // Log para debugging
            Log::info('Archivo subido a S3: ' . $rutaArchivo . ' - Ruta completa: ' . Storage::disk('s3')->path($rutaArchivo));
            
            // Guardar registro en base de datos mediante modelo
            $evidenciaId = Evidencia::crearEvidencia($tareaId, $rutaArchivo, $tipoArchivo);
            
            // Obtener la evidencia creada
            $evidencia = Evidencia::obtenerPorId($evidenciaId);
            
            // NO generar URL temporal aquí (se generará cuando se liste o se necesite)
            // Solo retornar los datos básicos, similar a TechnicalDataSheetDocumentController
            return response()->json([
                'success' => true,
                'message' => 'Evidencia subida correctamente.',
                'evidencia' => [
                    'id' => $evidencia->id,
                    'archivo' => $evidencia->archivo,
                    'tipo' => $evidencia->tipo,
                    'nombre_archivo' => basename($evidencia->archivo),
                    'created_at_formatted' => \Carbon\Carbon::parse($evidencia->created_at)->format('d/m/Y g:i A')
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la evidencia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todas las evidencias de una tarea
     */
    public function index($tareaId)
    {
        // Validar que la tarea existe
        $tarea = Tarea::find($tareaId);
        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'La tarea no existe.'
            ], 404);
        }

        try {
            // Obtener evidencias mediante modelo
            $evidencias = Evidencia::obtenerPorTarea($tareaId);
            
            // Agregar URL del endpoint de descarga y URL directa para cada evidencia
            $evidenciasConUrl = array_map(function($evidencia) use ($tareaId) {
                // URL del endpoint de download (para enlaces y botones)
                $evidencia['url'] = route('tareas.evidencias.download', [
                    'tareaId' => $tareaId, 
                    'evidenciaId' => $evidencia['id']
                ]);
                
                // URL directa de S3 (para imágenes en <img src=""> y PDFs)
                // Solo generar URL directa si es visualizable (imagen o PDF)
                if (in_array($evidencia['tipo'], ['imagen', 'pdf'])) {
                    $evidencia['url_directa'] = Evidencia::obtenerUrlTemporal($evidencia['archivo'], 60);
                } else {
                    $evidencia['url_directa'] = null;
                }
                
                $evidencia['es_imagen'] = in_array($evidencia['tipo'], ['imagen']);
                // Archivo es visualizable si es imagen o PDF
                $evidencia['es_visualizable'] = in_array($evidencia['tipo'], ['imagen', 'pdf']);
                $evidencia['icono'] = $this->obtenerIconoTipoArchivo($evidencia['tipo']);
                return $evidencia;
            }, $evidencias);

            return response()->json([
                'success' => true,
                'evidencias' => $evidenciasConUrl
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener evidencias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar o visualizar evidencia (redirige a URL temporal de S3)
     */
    public function download($tareaId, $evidenciaId)
    {
        // Validar que la evidencia existe
        if (!Evidencia::existe($evidenciaId)) {
            abort(404, 'La evidencia no existe.');
        }

        // Obtener la evidencia
        $evidencia = Evidencia::obtenerPorId($evidenciaId);

        // Verificar que pertenece a la tarea
        if ($evidencia->tarea_id != $tareaId) {
            abort(403, 'La evidencia no pertenece a esta tarea.');
        }

        try {
            // Determinar si es visualizable (imagen o PDF) o descargable (Word, Excel, etc.)
            $extension = strtolower(pathinfo($evidencia->archivo, PATHINFO_EXTENSION));
            $esVisualizable = in_array($extension, array_merge(
                self::TIPOS_PERMITIDOS['imagen'], 
                self::TIPOS_PERMITIDOS['pdf']
            ));
            
            // Log para debugging
            Log::info('Intentando generar URL temporal para evidencia ID: ' . $evidenciaId . ', Ruta: ' . $evidencia->archivo);
            
            // Verificar si el archivo existe en S3 antes de generar URL
            if (!Storage::disk('s3')->exists($evidencia->archivo)) {
                Log::warning('Archivo no existe en S3 para evidencia ID: ' . $evidenciaId . ', Ruta: ' . $evidencia->archivo);
                abort(404, 'El archivo no está disponible en S3. Puede ser una evidencia antigua que no se subió correctamente.');
            }
            
            // Generar URL temporal desde S3 usando la ruta guardada
            $urlTemporal = Storage::disk('s3')->temporaryUrl($evidencia->archivo, now()->addMinutes(60));
            
            Log::info('URL temporal generada exitosamente: ' . substr($urlTemporal, 0, 100) . '...');
            
            // Si es visualizable, redirigir para ver en el navegador
            if ($esVisualizable) {
                return redirect($urlTemporal);
            } else {
                // Para archivos no visualizables, obtener el archivo y forzar descarga
                $archivoContenido = Storage::disk('s3')->get($evidencia->archivo);
                
                return response($archivoContenido)
                    ->header('Content-Type', $this->obtenerMimeType($extension))
                    ->header('Content-Disposition', 'attachment; filename="' . basename($evidencia->archivo) . '"')
                    ->header('Content-Length', strlen($archivoContenido));
            }

        } catch (\Exception $e) {
            Log::error('Error obteniendo evidencia ID: ' . $evidenciaId . ', Ruta: ' . ($evidencia->archivo ?? 'N/A') . ', Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(404, 'El archivo no está disponible. Error: ' . $e->getMessage());
        }
    }

    /**
     * Obtener URL temporal de una evidencia específica (API JSON)
     */
    public function show($tareaId, $evidenciaId)
    {
        // Validar que la evidencia existe
        if (!Evidencia::existe($evidenciaId)) {
            return response()->json([
                'success' => false,
                'message' => 'La evidencia no existe.'
            ], 404);
        }

        // Obtener la evidencia
        $evidencia = Evidencia::obtenerPorId($evidenciaId);

        // Verificar que pertenece a la tarea
        if ($evidencia->tarea_id != $tareaId) {
            return response()->json([
                'success' => false,
                'message' => 'La evidencia no pertenece a esta tarea.'
            ], 403);
        }

        try {
            // Generar URL temporal directamente (usando la ruta del download)
            $downloadUrl = route('tareas.evidencias.download', ['tareaId' => $tareaId, 'evidenciaId' => $evidenciaId]);
            
            // También obtener URL temporal directa para casos especiales
            $urlTemporal = Evidencia::obtenerUrlTemporal($evidencia->archivo, 60);

            return response()->json([
                'success' => true,
                'evidencia' => [
                    'id' => $evidencia->id,
                    'archivo' => $evidencia->archivo,
                    'tipo' => $evidencia->tipo,
                    'nombre_archivo' => basename($evidencia->archivo),
                    'url' => $downloadUrl, // URL del endpoint de descarga
                    'url_directa' => $urlTemporal, // URL directa de S3 (opcional)
                    'created_at' => $evidencia->created_at,
                    'created_at_formatted' => \Carbon\Carbon::parse($evidencia->created_at)->format('d/m/Y g:i A')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la evidencia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar evidencia
     */
    public function destroy($tareaId, $evidenciaId)
    {
        // Validar que la evidencia existe
        if (!Evidencia::existe($evidenciaId)) {
            return response()->json([
                'success' => false,
                'message' => 'La evidencia no existe.'
            ], 404);
        }

        // Obtener la evidencia para eliminar el archivo
        $evidencia = Evidencia::obtenerPorId($evidenciaId);

        // Verificar que pertenece a la tarea
        if ($evidencia->tarea_id != $tareaId) {
            return response()->json([
                'success' => false,
                'message' => 'La evidencia no pertenece a esta tarea.'
            ], 403);
        }

        try {
            // Intentar eliminar archivo de S3 directamente (sin verificar existencia)
            try {
                Storage::disk('s3')->delete($evidencia->archivo);
            } catch (\Exception $e) {
                // Si falla la eliminación del archivo, registrar pero continuar
                // El archivo puede no existir o haber sido eliminado previamente
                Log::warning('No se pudo eliminar el archivo de S3: ' . $e->getMessage());
            }

            // Eliminar registro de base de datos mediante modelo
            Evidencia::eliminarEvidencia($evidenciaId);

            return response()->json([
                'success' => true,
                'message' => 'Evidencia eliminada correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la evidencia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Determinar tipo de archivo según extensión
     */
    private function determinarTipoArchivo(string $extension): string
    {
        if (in_array($extension, self::TIPOS_PERMITIDOS['imagen'])) {
            return 'imagen';
        }
        
        if (in_array($extension, self::TIPOS_PERMITIDOS['pdf'])) {
            return 'pdf';
        }
        
        if (in_array($extension, self::TIPOS_PERMITIDOS['documento'])) {
            return 'documento';
        }
        
        return 'otro';
    }

    /**
     * Generar nombre único para el archivo
     */
    private function generarNombreUnico(int $tareaId, string $nombreOriginal, string $extension): string
    {
        $nombreSinExtension = pathinfo($nombreOriginal, PATHINFO_FILENAME);
        $nombreLimpio = Str::slug($nombreSinExtension);
        $timestamp = now()->timestamp;
        $random = Str::random(8);
        
        return "tarea_{$tareaId}_{$nombreLimpio}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Obtener icono según tipo de archivo
     */
    private function obtenerIconoTipoArchivo(?string $tipo): string
    {
        return match($tipo) {
            'imagen' => 'bi-image',
            'pdf' => 'bi-file-earmark-pdf',
            'documento' => 'bi-file-earmark-word',
            default => 'bi-file-earmark'
        };
    }

    /**
     * Obtener MIME type según extensión
     */
    private function obtenerMimeType(string $extension): string
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}
