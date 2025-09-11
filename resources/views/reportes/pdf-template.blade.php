<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2563eb;
        }
        .meta {
            text-align: right;
            font-size: 10px;
            color: #666;
            margin-bottom: 20px;
        }
        .description {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 20px;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
    </div>
    
    <div class="meta">
        <p><strong>Generado:</strong> {{ $generado_en }}</p>
    </div>
    
    @if($reporte->descripcion)
    <div class="description">
        <p><strong>Descripción:</strong> {{ $reporte->descripcion }}</p>
    </div>
    @endif
    
    @if(count($datos) > 0)
        <table>
            <thead>
                <tr>
                    @if(is_array($datos[0]))
                        @foreach(array_keys($datos[0]) as $header)
                            <th>{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                        @endforeach
                    @elseif(is_object($datos[0]))
                        @foreach(array_keys((array)$datos[0]) as $header)
                            <th>{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                        @endforeach
                    @else
                        <th>Valor</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $fila)
                    <tr>
                        @if(is_array($fila))
                            @foreach($fila as $valor)
                                <td>{{ $valor }}</td>
                            @endforeach
                        @elseif(is_object($fila))
                            @foreach((array)$fila as $valor)
                                <td>{{ $valor }}</td>
                            @endforeach
                        @else
                            <td>{{ $fila }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="meta">
            <p><strong>Total de registros:</strong> {{ count($datos) }}</p>
        </div>
    @else
        <div class="no-data">
            <p>No hay datos disponibles para este reporte.</p>
        </div>
    @endif
    
    <div class="footer">
        <p>Generado por SmartKet ERP - {{ date('Y') }}</p>
    </div>
</body>
</html>

