<form action="index.php" method="get" class="filters-form">
    
    <input type="text" name="search" placeholder="Buscar por nombre o email..." 
           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
    
    <select name="tipo_id">
        <option value="">-- Filtrar por Tipo --</option>
        <?php foreach ($tipos_para_filtro as $tipo): ?>
            <option value="<?php echo $tipo->id; ?>" 
                <?php echo (isset($_GET['tipo_id']) && $_GET['tipo_id'] == $tipo->id) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($tipo->tipo); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="etiqueta">
        <option value="">-- Filtrar por Etiqueta --</option>
        <?php foreach ($etiquetas_para_filtro as $etiqueta): ?>
            <option value="<?php echo htmlspecialchars($etiqueta); ?>"
                <?php echo (isset($_GET['etiqueta']) && $_GET['etiqueta'] == $etiqueta) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars(ucfirst($etiqueta)); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primary">Buscar</button>
</form>

<table class="client-table">
    <thead>
        <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Etiqueta</th>
            <th>Tipo Cliente</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php

        ?>

        <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="7" style="text-align: center;">No se encontraron clientes con los filtros seleccionados.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td>
                        <?php if (!empty($cliente['nombre_fisico_imagen'])): ?>
                            <img src="uploads/clients/<?php echo htmlspecialchars($cliente['nombre_fisico_imagen']); ?>" 
                                 alt="Logo de <?php echo htmlspecialchars($cliente['nombre']); ?>" 
                                 class="client-image">
                        <?php else: ?>
                            <span>(Sin imagen)</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($cliente['etiqueta'])); ?></td>
                    <td><?php echo htmlspecialchars($cliente['tipo_nombre']); ?></td>
                    <td class="actions">
                        <a href="cliente_editar.php?id=<?php echo $cliente['id']; ?>" class="btn btn-edit">Editar</a>
                        <a href="cliente_borrar.php?id=<?php echo $cliente['id']; ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('¿Está seguro de que desea eliminar este cliente?');">
                           Borrar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>