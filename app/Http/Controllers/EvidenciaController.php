<?php

namespace App\Http\Controllers;

use App\Models\Evidencia;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            
            // Ruta donde se guardará el archivo
            $rutaArchivo = 'evidencias/' . $nombreArchivo;
            
            // Guardar archivo en storage
            $archivo->storeAs('evidencias', $nombreArchivo, 'public');
            
            // Guardar registro en base de datos mediante modelo
            $evidenciaId = Evidencia::crearEvidencia($tareaId, $rutaArchivo, $tipoArchivo);
            
            // Obtener la evidencia creada
            $evidencia = Evidencia::obtenerPorId($evidenciaId);
            
            return response()->json([
                'success' => true,
                'message' => 'Evidencia subida correctamente.',
                'evidencia' => [
                    'id' => $evidencia->id,
                    'archivo' => $evidencia->archivo,
                    'tipo' => $evidencia->tipo,
                    'nombre_archivo' => basename($evidencia->archivo),
                    'url' => Storage::disk('public')->url($evidencia->archivo),
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
            
            // Agregar URL pública para cada evidencia
            $evidenciasConUrl = array_map(function($evidencia) {
                $evidencia['url'] = Storage::disk('public')->url($evidencia['archivo']);
                $evidencia['es_imagen'] = in_array($evidencia['tipo'], ['imagen']);
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
            // Eliminar archivo físico
            if (Storage::disk('public')->exists($evidencia->archivo)) {
                Storage::disk('public')->delete($evidencia->archivo);
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
}
