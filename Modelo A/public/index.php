<?php
session_start(); // Para manejar mensajes de feedback
require_once '../config.php';
require_once '../models/TipoCliente.php';
// El modelo Cliente.php no se usa activamente aquí, pero se incluye por coherencia
require_once '../models/Cliente.php';

// --- Lógica de Filtros y Búsqueda ---
$search = $_GET['search'] ?? '';
$tipo_id_filtro = $_GET['tipo_id'] ?? null;
$etiqueta_filtro = $_GET['etiqueta'] ?? '';

// --- Obtener datos para los filtros ---
$stmt_tipos = $pdo->query("SELECT id, tipo FROM tipo_cliente ORDER BY tipo");
$tipos_para_filtro = $stmt_tipos->fetchAll(PDO::FETCH_OBJ);

// Etiquetas definidas [cite: 7]
$etiquetas_para_filtro = ['activo', 'prospecto', 'inactivo'];


// --- Construir la consulta principal ---
// Usamos LEFT JOIN para que los clientes sin tipo asignado (tipo_id = NULL) también aparezcan
$sql = "SELECT c.*, t.tipo as tipo_nombre 
        FROM cliente c 
        LEFT JOIN tipo_cliente t ON c.tipo_id = t.id
        WHERE 1=1";

$params = [];

if (!empty($search)) {
    // Buscar por nombre o email 
    $sql .= " AND (c.nombre LIKE ? OR c.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($tipo_id_filtro)) {
    // Filtrar por tipo 
    $sql .= " AND c.tipo_id = ?";
    $params[] = $tipo_id_filtro;
}

if (!empty($etiqueta_filtro)) {
    // Filtrar por etiqueta 
    $sql .= " AND c.etiqueta = ?";
    $params[] = $etiqueta_filtro;
}

$sql .= " ORDER BY c.nombre ASC";

$stmt_clientes = $pdo->prepare($sql);
$stmt_clientes->execute($params);
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

// --- Incluir la Vista ---
include '../templates/header.php';

// Mostrar mensajes de sesión (feedback al usuario)
if (isset($_SESSION['mensaje_exito'])) {
    echo '<div style="color: green; background: #e0f8e0; border: 1px solid green; padding: 10px; margin-bottom: 15px; border-radius: 5px;">' . htmlspecialchars($_SESSION['mensaje_exito']) . '</div>';
    unset($_SESSION['mensaje_exito']);
}
if (isset($_SESSION['mensaje_error'])) {
    echo '<div style="color: red; background: #f8e0e0; border: 1px solid red; padding: 10px; margin-bottom: 15px; border-radius: 5px;">' . htmlspecialchars($_SESSION['mensaje_error']) . '</div>';
    unset($_SESSION['mensaje_error']);
}

// El fichero body.php ahora usará las variables $clientes, $tipos_para_filtro y $etiquetas_para_filtro
include '../templates/body.php';

include '../templates/footer.php';
?>