<?php
session_start();
require_once '../config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['mensaje_error'] = "ID de cliente no válido.";
    header("Location: index.php");
    exit;
}

try {
    // 1. Obtener el nombre del fichero físico antes de borrar el registro [cite: 133]
    $stmt = $pdo->prepare("SELECT nombre, nombre_fisico_imagen FROM cliente WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        $nombre_cliente = $cliente['nombre'];
        $nombre_fisico = $cliente['nombre_fisico_imagen'];

        // 2. Borrar el registro de la base de datos [cite: 136]
        $stmt_delete = $pdo->prepare("DELETE FROM cliente WHERE id = ?");
        $stmt_delete->execute([$id]); // [cite: 137]

        // 3. Si el borrado en BD fue exitoso, borrar el fichero físico
        if ($stmt_delete->rowCount() > 0) {
            if (!empty($nombre_fisico)) {
                $path = __DIR__ . '/uploads/clients/' . $nombre_fisico; // [cite: 134]
                if (file_exists($path)) {
                    unlink($path); // [cite: 135]
                }
            }
            $_SESSION['mensaje_exito'] = "Cliente '" . htmlspecialchars($nombre_cliente) . "' y su imagen asociada han sido eliminados.";
        } else {
            $_SESSION['mensaje_error'] = "No se pudo eliminar el cliente (quizás ya fue borrado).";
        }

    } else {
        $_SESSION['mensaje_error'] = "Cliente no encontrado (ID: $id).";
    }

} catch (PDOException $e) {
    // Manejar violaciones de clave foránea (aunque la BBDD está en 'ON DELETE SET NULL')
    $_SESSION['mensaje_error'] = "Error al eliminar el cliente: " . $e->getMessage();
}

// Redirigir siempre a index.php
header("Location: index.php");
exit;
?>