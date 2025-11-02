<?php
session_start(); 
require_once '../config.php';
require_once '../models/TipoCliente.php';

require_once '../models/Cliente.php';


$search = $_GET['search'] ?? '';
$tipo_id_filtro = $_GET['tipo_id'] ?? null;
$etiqueta_filtro = $_GET['etiqueta'] ?? '';


$stmt_tipos = $pdo->query("SELECT id, tipo FROM tipo_cliente ORDER BY tipo");
$tipos_para_filtro = $stmt_tipos->fetchAll(PDO::FETCH_OBJ);


$etiquetas_para_filtro = ['activo', 'prospecto', 'inactivo'];



$sql = "SELECT c.*, t.tipo as tipo_nombre 
        FROM cliente c 
        LEFT JOIN tipo_cliente t ON c.tipo_id = t.id
        WHERE 1=1";

$params = [];

if (!empty($search)) {
    
    $sql .= " AND (c.nombre LIKE ? OR c.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($tipo_id_filtro)) {
  
    $sql .= " AND c.tipo_id = ?";
    $params[] = $tipo_id_filtro;
}

if (!empty($etiqueta_filtro)) {
  
    $sql .= " AND c.etiqueta = ?";
    $params[] = $etiqueta_filtro;
}

$sql .= " ORDER BY c.nombre ASC";

$stmt_clientes = $pdo->prepare($sql);
$stmt_clientes->execute($params);
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

include '../templates/header.php';


if (isset($_SESSION['mensaje_exito'])) {
    echo '<div style="color: green; background: #e0f8e0; border: 1px solid green; padding: 10px; margin-bottom: 15px; border-radius: 5px;">' . htmlspecialchars($_SESSION['mensaje_exito']) . '</div>';
    unset($_SESSION['mensaje_exito']);
}
if (isset($_SESSION['mensaje_error'])) {
    echo '<div style="color: red; background: #f8e0e0; border: 1px solid red; padding: 10px; margin-bottom: 15px; border-radius: 5px;">' . htmlspecialchars($_SESSION['mensaje_error']) . '</div>';
    unset($_SESSION['mensaje_error']);
}


include '../templates/body.php';

include '../templates/footer.php';
?>