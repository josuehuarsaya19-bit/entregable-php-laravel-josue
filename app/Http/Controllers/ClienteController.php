<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    //CRUD
    //LISTAR
    public function listarCliente()
    {
        $clientes = Cliente::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'nombre' => 'Josue Huarsaya',
            'data' => $clientes
        ]);
    }

    public function guardarCliente(Request $request)
    {
        $validate = $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:clientes,email',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string'
        ]);

        try {
            $cliente = Cliente::create($request->all());

            return response()->json([
                'success' => true,
                'nombre' => 'Josue Huarsaya',
                'data' => $cliente
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'nombre' => 'Josue Huarsaya',
                'data' => $e->getMessage(),
            ]);
        }
    }

    public function editarCliente(Request $request, $id_cliente) {
        $validate = $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:clientes,email,' . $id_cliente,
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string'
        ]);

        $cliente = Cliente::find($id_cliente);

        $cliente->update([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ]);
        
        return response()->json([
            'success' => true,
            'nombre' => 'Josue Huarsaya',
            'data' => $cliente
        ]);
    }

    public function eliminarCliente($id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);
        $cliente->delete();

        return response()->json([
            'success' => true,
            'nombre' => 'Josue Huarsaya',
            'data' => $cliente
        ]);
    }

    public function exportarPdfCliente(){
        $clientes = Cliente::orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('clientes.pdf', compact('clientes'));
        $pdf->setPaper('a4');

        return $pdf->download('reporte_clientes.pdf');
   }
}