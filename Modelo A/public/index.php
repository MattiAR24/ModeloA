<?php
/*
*****************************************************************
* INICIO DE LÓGICA PHP (Simulación)
*
* En una aplicación real, aquí deberías:
* 1. include '../config.php'; (para $pdo)
* 2. include '../models/Cliente.php';
* 3. include '../models/TipoCliente.php';
*
* 4. Obtener filtros de $_GET
* $search = $_GET['search'] ?? '';
* $tipo_id = $_GET['tipo_id'] ?? null;
* $etiqueta = $_GET['etiqueta'] ?? '';
*
* 5. ($tipoModel = new TipoCliente($pdo))->getAll();
* (Para llenar el <select> de tipos)
*
* 6. ($clienteModel = new Cliente($pdo))->find($search, $tipo_id, $etiqueta);
* (Para obtener la lista de clientes filtrada)
*****************************************************************
*/

require_once 'config.php';
require_once 'Cliente.php';
require_once 'TipoCliente.php';



// --- Simulación de datos ---
// Datos que vendrían de la tabla 'tipo_cliente'
$tipos_para_filtro = [
    (object)['id' => 1, 'tipo' => 'Particular'],
    (object)['id' => 2, 'tipo' => 'Pyme'],
    (object)['id' => 3, 'tipo' => 'SA'],
    (object)['id' => 4, 'tipo' => 'Asociación']
];


// Etiquetas definidas [cite: 7]
$etiquetas_para_filtro = ['activo', 'prospecto', 'inactivo'];


// Datos que vendrían de la tabla 'cliente' (con un JOIN a 'tipo_cliente')
$clientes_simulados = [
    [
        'id' => 1,
        'nombre' => 'Empresa Sol S.A.',
        'email' => 'contacto@sol.sa',
        'telefono' => '912345678',
        'etiqueta' => 'activo',
        'tipo_nombre' => 'SA',
        'nombre_fisico_imagen' => 'hash_abc123.jpg' // [cite: 9]
    ],
    [
        'id' => 2,
        'nombre' => 'Ana García (Prospecto)',
        'email' => 'ana.garcia@email.com',
        'telefono' => '600112233',
        'etiqueta' => 'prospecto',
        'tipo_nombre' => 'Particular',
        'nombre_fisico_imagen' => null
    ],
    [
        'id' => 3,
        'nombre' => 'Taller Mecánico Hermanos.',
        'email' => 'info@tallerhns.com',
        'telefono' => '933445566',
        'etiqueta' => 'inactivo',
        'tipo_nombre' => 'Pyme',
        'nombre_fisico_imagen' => 'hash_def456.png' // [cite: 9]
    ]
];


include '../templates/header.php';
include '../templates/body.php';
include '../templates/footer.php';
?>
