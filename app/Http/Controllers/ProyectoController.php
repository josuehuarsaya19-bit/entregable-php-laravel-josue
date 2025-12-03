<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    //CRUD
    //LISTAR
    public function listarProyecto()
    {
        $proyectos = Proyecto::with('Cliente')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'nombre' => 'josue huarsaya',
            'data' => $proyectos
        ]);
    }

    public function guardarProyecto(Request $request)
    {
        $validate = $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:pendiente,en_progreso,completado,cancelado',
            'cliente_id' => 'required|exists:clientes,id'
        ]);

        try {
            $proyecto = Proyecto::create($request->all());

            return response()->json([
                'success' => true,
                'nombre' => 'Josue Huarsaya',
                'data' => $proyecto
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'nombre' => 'Josue Huarsaya',
                'data' => $e->getMessage(),
            ]);
        }
    }

    public function editarProyecto(Request $request, $id_proyecto) {
        $validate = $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:pendiente,en_progreso,completado,cancelado',
            'cliente_id' => 'required|exists:clientes,id'
        ]);

        $proyecto = Proyecto::find($id_proyecto);

        $proyecto->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado' => $request->estado,
            'cliente_id' => $request->cliente_id,
        ]);
        
        return response()->json([
            'success' => true,
            'nombre' => 'Josue Huarsaya',
            'data' => $proyecto
        ]);
    }

    public function eliminarProyecto($id_proyecto)
    {
        $proyecto = Proyecto::findOrFail($id_proyecto);
        $proyecto->delete();

        return response()->json([
            'success' => true,
            'nombre' => 'Josue Huarsaya',
            'data' => $proyecto
        ]);
    }

    public function exportarPdfProyecto(){
        $proyectos = Proyecto::with('cliente')
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('proyectos.pdf', compact('proyectos'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('reporte_proyectos.pdf');
    }
}