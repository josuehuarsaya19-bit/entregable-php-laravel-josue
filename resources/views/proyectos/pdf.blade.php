<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte PDF Proyectos</title>
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
            border-bottom: 2px solid #fd7e14;
            margin-bottom: 20px;
        }
        .footer {
            border-top: 2px solid #fd7e14;
            border-bottom: none;
            margin-top: 30px;
            color: #555;
            font-size: 1em;
        }
        .report-title {
            text-align: center;
            color: #fd7e14;
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
            font-size: 0.85em;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #fd7e14;
            color: #fff;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        td[colspan="9"] {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 20px;
        }
        .total-proyectos {
            text-align: right;
            font-weight: bold;
            color: #fd7e14;
            margin-top: 10px;
            font-size: 1.1em;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-en_progreso {
            background-color: #cce5ff;
            color: #004085;
        }
        .badge-completado {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-cancelado {
            background-color: #f8d7da;
            color: #721c24;
        }
        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat-card {
            flex: 1;
            min-width: 200px;
            margin: 5px;
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
            border-left: 4px solid #fd7e14;
        }
        .stat-title {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #fd7e14;
        }
        .text-muted {
            color: #6c757d;
            font-style: italic;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <main>
        <div class="header">Reporte de Proyectos</div>

        <div class="author">
            Generado por: <strong>Josue Huarsaya</strong><br>
            Fecha: <strong>{{ date('d/m/Y H:i:s') }}</strong>
        </div>

        <div class="report-title">Listado de Proyectos</div>
        
        <div class="report-info">
            Total de proyectos registrados: <strong>{{ $proyectos->count() }}</strong>
        </div>

        <!-- Estadísticas -->
        @php
            $pendientes = $proyectos->where('estado', 'pendiente')->count();
            $enProgreso = $proyectos->where('estado', 'en_progreso')->count();
            $completados = $proyectos->where('estado', 'completado')->count();
            $cancelados = $proyectos->where('estado', 'cancelado')->count();
        @endphp
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-title">Pendientes</div>
                <div class="stat-value">{{ $pendientes }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">En Progreso</div>
                <div class="stat-value">{{ $enProgreso }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Completados</div>
                <div class="stat-value">{{ $completados }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Cancelados</div>
                <div class="stat-value">{{ $cancelados }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cliente</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Duración</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                </tr>
            </thead>
            <tbody>
                @forelse ( $proyectos as $index => $proyecto )
                    @php
                        // Calcular duración
                        $duracion = 'N/A';
                        if ($proyecto->fecha_inicio && $proyecto->fecha_fin) {
                            $inicio = new DateTime($proyecto->fecha_inicio);
                            $fin = new DateTime($proyecto->fecha_fin);
                            $intervalo = $inicio->diff($fin);
                            $duracion = $intervalo->days . ' días';
                        }
                        
                        // Obtener nombre del cliente
                        $clienteNombre = $proyecto->cliente ? $proyecto->cliente->nombre : 'No asignado';
                        
                        // Clase CSS para el estado
                        $estadoClase = 'badge-' . $proyecto->estado;
                        
                        // Texto del estado
                        $estadoTexto = '';
                        switch ($proyecto->estado) {
                            case 'pendiente': $estadoTexto = 'PENDIENTE'; break;
                            case 'en_progreso': $estadoTexto = 'EN PROGRESO'; break;
                            case 'completado': $estadoTexto = 'COMPLETADO'; break;
                            case 'cancelado': $estadoTexto = 'CANCELADO'; break;
                            default: $estadoTexto = strtoupper($proyecto->estado);
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $proyecto->nombre }}</td>
                        <td>
                            @if($proyecto->descripcion)
                                {{ strlen($proyecto->descripcion) > 50 ? substr($proyecto->descripcion, 0, 50) . '...' : $proyecto->descripcion }}
                            @else
                                <span class="text-muted">Sin descripción</span>
                            @endif
                        </td>
                        <td>{{ $clienteNombre }}</td>
                        <td>
                            @if($proyecto->fecha_inicio)
                                {{ date('d/m/Y', strtotime($proyecto->fecha_inicio)) }}
                            @else
                                <span class="text-muted">No definida</span>
                            @endif
                        </td>
                        <td>
                            @if($proyecto->fecha_fin)
                                {{ date('d/m/Y', strtotime($proyecto->fecha_fin)) }}
                            @else
                                <span class="text-muted">No definida</span>
                            @endif
                        </td>
                        <td>{{ $duracion }}</td>
                        <td>
                            <span class="badge {{ $estadoClase }}">
                                {{ $estadoTexto }}
                            </span>
                        </td>
                        <td>{{ date('d/m/Y H:i', strtotime($proyecto->created_at)) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">No hay proyectos registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($proyectos->count() > 0)
        <div class="total-proyectos">
            Total de proyectos: {{ $proyectos->count() }} | 
            Pendientes: {{ $pendientes }} | 
            En progreso: {{ $enProgreso }} | 
            Completados: {{ $completados }} | 
            Cancelados: {{ $cancelados }}
        </div>
        @endif

        <div class="footer">
            Reporte generado automáticamente &copy; {{ date('Y') }} - Sistema de Gestión de Proyectos
        </div>
    </main>
</body>
</html>