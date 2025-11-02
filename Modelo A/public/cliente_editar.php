<?php
session_start();
require_once '../config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['mensaje_error'] = "ID de cliente no válido.";
    header("Location: index.php");
    exit;
}

// Cargar datos actuales del cliente
$stmt = $pdo->prepare("SELECT * FROM cliente WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    $_SESSION['mensaje_error'] = "Cliente no encontrado.";
    header("Location: index.php");
    exit;
}

// Cargar datos para los <select>
$tipos_cliente = $pdo->query("SELECT id, tipo FROM tipo_cliente ORDER BY tipo")->fetchAll(PDO::FETCH_ASSOC);
$etiquetas = ['activo', 'prospecto', 'inactivo'];

$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y sanear datos
    $datos_post = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'direccion' => trim($_POST['direccion'] ?? ''),
        'etiqueta' => trim($_POST['etiqueta'] ?? 'activo'),
        'tipo_id' => filter_var($_POST['tipo_id'] ?? '', FILTER_VALIDATE_INT) ?: null,
        'comentarios' => trim($_POST['comentarios'] ?? '')
    ];

    // Mantener las imágenes existentes por defecto
    $imagen_original = $cliente['imagen'];
    $imagen_fisica = $cliente['nombre_fisico_imagen'];

    // 2. Validar datos básicos [cite: 12]
    if (empty($datos_post['nombre'])) {
        $errores[] = "El nombre es obligatorio.";
    }
    if (!empty($datos_post['email']) && !filter_var($datos_post['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido.";
    }

    // 3. Procesar subida de *nueva* imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['imagen'];
        $max_size = 3 * 1024 * 1024; // 3 MB [cite: 12]
        $allowed_types = ['image/jpeg', 'image/png']; // [cite: 12]

        if ($file['size'] > $max_size) {
            $errores[] = "La imagen supera el límite de 3 MB.";
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->file($file['tmp_name']);

            if (in_array($mime_type, $allowed_types)) {
                // Borrar el fichero físico antiguo si existe
                if (!empty($cliente['nombre_fisico_imagen'])) {
                    $path_antiguo = __DIR__ . '/uploads/clients/' . $cliente['nombre_fisico_imagen'];
                    if (file_exists($path_antiguo)) {
                        unlink($path_antiguo); // [cite: 90]
                    }
                }

                // Preparar el nuevo fichero
                $imagen_original = basename($file['name']);
                $extension = pathinfo($imagen_original, PATHINFO_EXTENSION);
                $imagen_fisica = bin2hex(random_bytes(16)) . '.' . $extension; // [cite: 87]
                $destino = __DIR__ . '/uploads/clients/' . $imagen_fisica; // 

                if (!move_uploaded_file($file['tmp_name'], $destino)) { // [cite: 88]
                    $errores[] = "Error al mover el nuevo fichero subido.";
                    // Revertir a los valores antiguos si falla la subida
                    $imagen_original = $cliente['imagen'];
                    $imagen_fisica = $cliente['nombre_fisico_imagen'];
                }
            } else {
                $errores[] = "Tipo de fichero no permitido. Solo JPG o PNG.";
            }
        }
    }

    // 4. Actualización en BD (si no hay errores)
    if (empty($errores)) {
        try {
            $sql = "UPDATE cliente SET 
                    nombre = :nombre, 
                    email = :email, 
                    telefono = :telefono, 
                    direccion = :direccion, 
                    etiqueta = :etiqueta, 
                    imagen = :imagen, 
                    nombre_fisico_imagen = :nombre_fisico_imagen, 
                    tipo_id = :tipo_id, 
                    comentarios = :comentarios
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos_post['nombre'],
                ':email' => $datos_post['email'],
                ':telefono' => $datos_post['telefono'],
                ':direccion' => $datos_post['direccion'],
                ':etiqueta' => $datos_post['etiqueta'],
                ':imagen' => $imagen_original,
                ':nombre_fisico_imagen' => $imagen_fisica,
                ':tipo_id' => $datos_post['tipo_id'],
                ':comentarios' => $datos_post['comentarios'],
                ':id' => $id
            ]);

            $_SESSION['mensaje_exito'] = "Cliente '" . htmlspecialchars($datos_post['nombre']) . "' actualizado correctamente.";
            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $errores[] = "Error al actualizar en la base de datos: " . $e->getMessage();
            // Si hay error, los datos del formulario se recargarán desde $datos_post
            $cliente = array_merge($cliente, $datos_post); 
        }
    } else {
         // Si hay errores de validación, fusionar los datos del post para repoblar el formulario
        $cliente = array_merge($cliente, $datos_post);
    }
}

// --- Vista (Formulario HTML) ---
include '../templates/header.php';
?>

<h2>Editar Cliente: <?php echo htmlspecialchars($cliente['nombre']); ?></h2>

<?php if (!empty($errores)): ?>
    <div style="color: red; background: #f8e0e0; border: 1px solid red; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
        <strong>Error al procesar:</strong>
        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="cliente_editar.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data" style="max-width: 600px; display: grid; gap: 15px; grid-template-columns: 1fr 1fr;">

    <div style="grid-column: 1 / -1;">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required style="width: 100%; padding: 8px;">
    </div>

    <div style="grid-column: 1 / 2;">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" style="width: 100%; padding: 8px;">
    </div>
    
    <div style="grid-column: 2 / -1;">
        <label for="telefono">Teléfono:</label>
        <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" style="width: 100%; padding: 8px;">
    </div>

    <div style="grid-column: 1 / -1;">
        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion']); ?>" style="width: 100%; padding: 8px;">
    </div>

    <div style="grid-column: 1 / 2;">
        <label for="tipo_id">Tipo de Cliente:</label>
        <select id="tipo_id" name="tipo_id" style="width: 100%; padding: 8px;">
            <option value="">-- Seleccione un tipo --</option>
            <?php foreach ($tipos_cliente as $tipo): ?>
                <option value="<?php echo $tipo['id']; ?>" <?php echo ($cliente['tipo_id'] == $tipo['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($tipo['tipo']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="grid-column: 2 / -1;">
        <label for="etiqueta">Etiqueta:</label>
        <select id="etiqueta" name="etiqueta" style="width: 100%; padding: 8px;">
            <?php foreach ($etiquetas as $etiqueta): ?>
                <option value="<?php echo htmlspecialchars($etiqueta); ?>" <?php echo ($cliente['etiqueta'] == $etiqueta) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars(ucfirst($etiqueta)); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="grid-column: 1 / -1;">
        <label for="imagen">Cambiar Imagen (Logo/Foto):</label>
        <input type="file" id="imagen" name="imagen" accept=".jpg,.jpeg,.png" style="width: 100%; padding: 8px;">
        <small>Límite 3MB. Dejar en blanco para conservar la imagen actual.</small>
        
        <?php if (!empty($cliente['nombre_fisico_imagen'])): ?>
             <div style="margin-top: 10px;">
                <strong>Imagen actual:</strong><br>
                <img src="uploads/clients/<?php echo htmlspecialchars($cliente['nombre_fisico_imagen']); ?>" alt="Logo actual" style="max-width: 100px; max-height: 100px; border: 1px solid #ccc; padding: 5px; margin-top: 5px;">
                (<?php echo htmlspecialchars($cliente['imagen']); ?>)
             </div>
        <?php endif; ?>
    </div>

    <div style="grid-column: 1 / -1;">
        <label for="comentarios">Comentarios:</label>
        <textarea id="comentarios" name="comentarios" rows="4" style="width: 100%; padding: 8px;"><?php echo htmlspecialchars($cliente['comentarios']); ?></textarea>
    </div>

    <div style="grid-column: 1 / -1; text-align: right;">
        <a href="index.php" class="btn" style="background-color: #777;">Cancelar</a>
        <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
    </div>
</form>

<?php
include '../templates/footer.php';
?>