<?php
/* NOTA:
En un proyecto real, config.php (conexión PDO) [cite: 65] y los modelos
se incluirían aquí o al inicio de index.php para cargar los datos
necesarios, como los "tipos de cliente" para el filtro.
*/
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Básico - Gestión de Clientes</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0; 
            background-color: #f4f7f6; 
        }
        .container { 
            max-width: 1200px; 
            margin: 20px auto; 
            padding: 20px; 
            background-color: #ffffff; 
            border-radius: 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        }
        .header-actions { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
            flex-wrap: wrap; 
        }
        h1 { color: #2c3e50; }
        .btn { 
            padding: 10px 18px; 
            text-decoration: none; 
            border-radius: 5px; 
            color: #fff; 
            font-weight: bold;
            font-size: 0.9em;
            border: none;
            cursor: pointer;
        }
        .btn-primary { background-color: #3498db; }
        .btn-primary:hover { background-color: #2980b9; }
        .btn-edit { background-color: #f39c12; }
        .btn-delete { background-color: #e74c3c; }

        /* Estilos de Filtros */
        .filters-form { 
            display: flex; 
            gap: 15px; 
            margin-bottom: 20px; 
            flex-wrap: wrap;
        }
        .filters-form input[type="text"], 
        .filters-form select {
            padding: 10px;
            border: 1px solid #dfe6e9;
            border-radius: 5px;
            font-size: 1em;
        }
        
        /* Estilos de Tabla */
        .client-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        .client-table th, .client-table td { 
            padding: 12px 15px; 
            border: 1px solid #dfe6e9; 
            text-align: left; 
            vertical-align: middle;
        }
        .client-table th { 
            background-color: #ecf0f1; 
            color: #34495e;
        }
        .client-table tr:nth-child(even) { background-color: #f9f9f9; }
        .client-table .client-image { 
            max-width: 60px; 
            height: auto; 
            border-radius: 4px; 
        }
        .client-table .actions { 
            display: flex; 
            gap: 8px; 
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-actions">
        <h1>Gestión de Clientes</h1>
        <a href="cliente_crear.php" class="btn btn-primary">Añadir Nuevo Cliente</a>
    </div>