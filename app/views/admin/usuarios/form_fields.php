<div class="row">
    <!-- Nombres -->
    <div class="col-md-6 mb-3">
        <label class="form-label font-weight-bold">Nombres <span class="text-danger">*</span></label>
        <input type="text" 
               name="nombre" 
               class="form-control" 
               maxlength="40" 
               required 
               placeholder="Ej: Juan Carlos" 
               value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>">
    </div>

    <!-- Apellidos -->
    <div class="col-md-6 mb-3">
        <label class="form-label font-weight-bold">Apellidos <span class="text-danger">*</span></label>
        <input type="text" 
               name="apellidos" 
               class="form-control" 
               maxlength="80" 
               required 
               placeholder="Ej: Pérez Gómez" 
               value="<?= htmlspecialchars($usuario['apellidos'] ?? '') ?>">
    </div>
</div>

<div class="row">
    <!-- Correo Electrónico -->
    <div class="col-md-6 mb-3">
        <label class="form-label font-weight-bold">Correo Electrónico <span class="text-danger">*</span></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
            </div>
            <input type="email" 
                   name="email" 
                   class="form-control" 
                   maxlength="150" 
                   required 
                   placeholder="ejemplo@correo.com" 
                   value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
        </div>
    </div>

    <!-- Teléfono -->
    <div class="col-md-6 mb-3">
        <label class="form-label font-weight-bold">Teléfono / Celular</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="mdi mdi-phone-outline"></i></span>
            </div>
            <input type="text" 
                   name="telefono" 
                   class="form-control" 
                   maxlength="20" 
                   placeholder="Ej: 987654321" 
                   value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
        </div>
    </div>
</div>

<div class="row">
    <!-- Rol de Usuario -->
    <div class="col-md-6 mb-3">
        <label class="form-label font-weight-bold">Rol en el Sistema <span class="text-danger">*</span></label>
        <select name="rol_id" class="form-control" required>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>" 
                    <?= (!empty($usuario['rol_id']) && $usuario['rol_id'] == $r['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['nombre']) ?> <?= !empty($r['descripcion']) ? '— ' . htmlspecialchars($r['descripcion']) : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Estado -->
    <div class="col-md-6 mb-3">
        <label class="form-label font-weight-bold">Estado <span class="text-danger">*</span></label>
        <select name="activo" class="form-control" required>
            <option value="1" <?= (!isset($usuario['activo']) || $usuario['activo'] == 1) ? 'selected' : '' ?>>Activo (Permite inicio de sesión)</option>
            <option value="0" <?= (isset($usuario['activo']) && $usuario['activo'] == 0) ? 'selected' : '' ?>>Inactivo (Bloqueado)</option>
        </select>
    </div>
</div>

<div class="row">
    <!-- Contraseña -->
    <div class="col-md-12 mb-3">
        <label class="form-label font-weight-bold">
            Contraseña <?= empty($isEdit) ? '<span class="text-danger">*</span>' : '' ?>
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
            </div>
            <input type="password" 
                   name="password" 
                   class="form-control" 
                   minlength="6" 
                   placeholder="<?= !empty($isEdit) ? 'Dejar en blanco para conservar la contraseña actual' : 'Mínimo 6 caracteres' ?>" 
                   <?= empty($isEdit) ? 'required' : '' ?>>
        </div>
        <?php if (!empty($isEdit)): ?>
            <small class="form-text text-muted">Solo ingrese un valor si desea cambiar la contraseña del usuario.</small>
        <?php endif; ?>
    </div>
</div>
