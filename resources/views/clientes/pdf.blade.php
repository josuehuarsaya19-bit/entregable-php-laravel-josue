<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte PDF Clientes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <style>
        body {
            background: #fff;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #222;
            margin: 40px;
        }
        .header, .footer {
            text-align: center;
            padding: 10px 0;
            font-weight: bold;
            font-size: 1.2em;
            border-bottom: 2px solid #28a745;
            margin-bottom: 20px;
        }
        .footer {
            border-top: 2px solid #28a745;
            border-bottom: none;
            margin-top: 30px;
            color: #555;
            font-size: 1em;
        }
        .report-title {
            text-align: center;
            color: #28a745;
            margin-bottom: 25px;
            font-size: 2em;
            font-weight: 600;
        }
        .author {
            text-align: right;
            color: #555;
            font-size: 1em;
            margin-bottom: 10px;
        }
        .report-info {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 0.9em;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px 8px;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: #fff;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        td[colspan="6"] {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 20px;
        }
        .total-clientes {
            text-align: right;
            font-weight: bold;
            color: #28a745;
            margin-top: 10px;
            font-size: 1.1em;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .badge-activo {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-inactivo {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <main>
        <div class="header">Reporte de Clientes</div>

        <div class="author">
            Generado por: <strong>Josue huarsaya</strong><br>
            Fecha: <strong>{{ date('d/m/Y H:i:s') }}</strong>
        </div>

        <div class="report-title">Listado de Clientes</div>
        
        <div class="report-info">
            Total de clientes registrados: <strong>{{ $clientes->count() }}</strong>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody>
                @forelse ( $clientes as $index => $cliente )
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->email }}</td>
                        <td>
                            @if($cliente->telefono)
                                {{ $cliente->telefono }}
                            @else
                                <span style="color: #888; font-style: italic;">No especificado</span>
                            @endif
                        </td>
                        <td>
                            @if($cliente->direccion)
                                {{ Str::limit($cliente->direccion, 50) }}
                            @else
                                <span style="color: #888; font-style: italic;">No especificada</span>
                            @endif
                        </td>
                        <td>{{ $cliente->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No hay clientes registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($clientes->count() > 0)
        <div class="total-clientes">
            Total de clientes: {{ $clientes->count() }}
        </div>
        @endif

        <div class="footer">
            Reporte generado automáticamente &copy; {{ date('Y') }} - Sistema de Gestión de Clientes
        </div>
    </main>
</body>
</html>