
<?php
session_start();
require_once '../config.php';

$tipos_cliente = $pdo->query("SELECT id, tipo FROM tipo_cliente ORDER BY tipo")->fetchAll(PDO::FETCH_ASSOC);
$etiquetas = ['activo', 'prospecto', 'inactivo']; // [cite: 7]

$errores = [];
$datos = [
    'nombre' => '', 'email' => '', 'telefono' => '', 'direccion' => '',
    'etiqueta' => 'activo', 'tipo_id' => '', 'comentarios' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $datos['nombre'] = trim($_POST['nombre'] ?? '');
    $datos['email'] = trim($_POST['email'] ?? '');
    $datos['telefono'] = trim($_POST['telefono'] ?? '');
    $datos['direccion'] = trim($_POST['direccion'] ?? '');
    $datos['etiqueta'] = trim($_POST['etiqueta'] ?? 'activo');
    $datos['tipo_id'] = filter_var($_POST['tipo_id'] ?? '', FILTER_VALIDATE_INT);
    $datos['comentarios'] = trim($_POST['comentarios'] ?? '');

    $imagen_original = null;
    $imagen_fisica = null;


    if (empty($datos['nombre'])) {
        $errores[] = "El nombre es obligatorio.";
    }
    if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido.";
    }
    if (empty($datos['tipo_id']) || $datos['tipo_id'] === false) {
        $datos['tipo_id'] = null; 
    }

    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['imagen'];
        $max_size = 3 * 1024 * 1024; 
        $allowed_types = ['image/jpeg', 'image/png'];

        
        if ($file['size'] > $max_size) {
            $errores[] = "La imagen supera el límite de 3 MB.";
        } else {
           
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->file($file['tmp_name']);

            if (in_array($mime_type, $allowed_types)) {
                $imagen_original = basename($file['name']); 
                $extension = pathinfo($imagen_original, PATHINFO_EXTENSION);
            
                $imagen_fisica = bin2hex(random_bytes(16)) . '.' . $extension;
                $destino = __DIR__ . '/uploads/clients/' . $imagen_fisica; 

               
                if (!move_uploaded_file($file['tmp_name'], $destino)) {
                    $errores[] = "Error al mover el fichero subido.";
                    $imagen_original = null;
                    $imagen_fisica = null;
                }
            } else {
                $errores[] = "Tipo de fichero no permitido. Solo JPG o PNG.";
            }
        }
    }

    
    if (empty($errores)) {
        try {
            $sql = "INSERT INTO cliente 
                    (nombre, email, telefono, direccion, etiqueta, imagen, nombre_fisico_imagen, tipo_id, comentarios)
                    VALUES 
                    (:nombre, :email, :telefono, :direccion, :etiqueta, :imagen, :nombre_fisico_imagen, :tipo_id, :comentarios)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':email' => $datos['email'],
                ':telefono' => $datos['telefono'],
                ':direccion' => $datos['direccion'],
                ':etiqueta' => $datos['etiqueta'],
                ':imagen' => $imagen_original,
                ':nombre_fisico_imagen' => $imagen_fisica, 
                ':tipo_id' => $datos['tipo_id'],
                ':comentarios' => $datos['comentarios']
            ]); 

            $_SESSION['mensaje_exito'] = "Cliente '" . htmlspecialchars($datos['nombre']) . "' creado correctamente.";
            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $errores[] = "Error al guardar en la base de datos: " . $e->getMessage();
        }
    }
}


include '../templates/header.php';
?>

<h2>Crear Nuevo Cliente</h2>

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

<form action="cliente_crear.php" method="post" enctype="multipart/form-data" style="max-width: 600px; display: grid; gap: 15px; grid-template-columns: 1fr 1fr;">

    <div style="grid-column: 1 / -1;">
        <label for="nombre">Nombre:</label> <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos['nombre']); ?>" required style="width: 100%; padding: 8px;">
    </div>

    <div style="grid-column: 1 / 2;">
        <label for="email">Email:</label> <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($datos['email']); ?>" style="width: 100%; padding: 8px;">
    </div>
    
    <div style="grid-column: 2 / -1;">
        <label for="telefono">Teléfono:</label>
        <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($datos['telefono']); ?>" style="width: 100%; padding: 8px;">
    </div>

    <div style="grid-column: 1 / -1;">
        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($datos['direccion']); ?>" style="width: 100%; padding: 8px;">
    </div>

    <div style="grid-column: 1 / 2;">
        <label for="tipo_id">Tipo de Cliente:</label> <select id="tipo_id" name="tipo_id" style="width: 100%; padding: 8px;"> <option value="">-- Seleccione un tipo --</option>
            <?php foreach ($tipos_cliente as $tipo): ?>
                <option value="<?php echo $tipo['id']; ?>" <?php echo ($datos['tipo_id'] == $tipo['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($tipo['tipo']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="grid-column: 2 / -1;">
        <label for="etiqueta">Etiqueta:</label>
        <select id="etiqueta" name="etiqueta" style="width: 100%; padding: 8px;">
            <?php foreach ($etiquetas as $etiqueta): ?>
                <option value="<?php echo htmlspecialchars($etiqueta); ?>" <?php echo ($datos['etiqueta'] == $etiqueta) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars(ucfirst($etiqueta)); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="grid-column: 1 / -1;">
        <label for="imagen">Imagen (Logo/Foto):</label> <input type="file" id="imagen" name="imagen" accept=".jpg,.jpeg,.png" style="width: 100%; padding: 8px;">
        <small>Límite 3MB. Tipos permitidos: JPG, PNG.</small>
    </div>

    <div style="grid-column: 1 / -1;">
        <label for="comentarios">Comentarios:</label>
        <textarea id="comentarios" name="comentarios" rows="4" style="width: 100%; padding: 8px;"><?php echo htmlspecialchars($datos['comentarios']); ?></textarea> </div>

    <div style="grid-column: 1 / -1; text-align: right;">
        <a href="index.php" class="btn" style="background-color: #777;">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar Cliente</button> </div>
</form>

<?php
include '../templates/footer.php';
?>




?>