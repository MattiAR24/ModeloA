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
  
    $stmt = $pdo->prepare("SELECT nombre, nombre_fisico_imagen FROM cliente WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        $nombre_cliente = $cliente['nombre'];
        $nombre_fisico = $cliente['nombre_fisico_imagen'];

        
        $stmt_delete = $pdo->prepare("DELETE FROM cliente WHERE id = ?");
        $stmt_delete->execute([$id]); 

       
        if ($stmt_delete->rowCount() > 0) {
            if (!empty($nombre_fisico)) {
                $path = __DIR__ . '/uploads/clients/' . $nombre_fisico; 
                if (file_exists($path)) {
                    unlink($path);
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
   
    $_SESSION['mensaje_error'] = "Error al eliminar el cliente: " . $e->getMessage();
}


header("Location: index.php");
exit;
?>