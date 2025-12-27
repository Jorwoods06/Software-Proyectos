@csrf
<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $user->nombre ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Password @if(isset($user)) <small>(dejar vacío si no cambia)</small> @endif</label>
    <input type="password" name="password" class="form-control" @if(!isset($user)) required @endif>
</div>

<div class="mb-3">
    <label class="form-label">Confirmar Password</label>
    <input type="password" name="password_confirmation" class="form-control" @if(!isset($user)) required @endif>
</div>

<div class="mb-3">
    <label class="form-label">Estado</label>
    <select name="estado" class="form-select" required>
        <option value="activo" {{ (old('estado', $user->estado ?? '') == 'activo') ? 'selected' : '' }}>Activo</option>
        <option value="inactivo" {{ (old('estado', $user->estado ?? '') == 'inactivo') ? 'selected' : '' }}>Inactivo</option>
    </select>
</div>

<div class="mb-3">
    <label for="departamento" class="form-label">Departamento</label>
    <select name="departamento" id="departamento" class="form-control" required>
        <option value="">-- Seleccione un departamento --</option>
        @foreach($departamentos as $d)
            <option value="{{ $d->id }}" 
                {{ old('departamento', $user->departamento ?? '') == $d->id ? 'selected' : '' }}>
                {{ $d->nombre }}
            </option>
        @endforeach
    </select>
</div>


<div class="mb-3">
    <label class="form-label">Roles</label>
    <div>
        @foreach($roles as $role)
            <?php
                $checked = false;
                if (isset($user)) {
                    $checked = $user->roles->contains('id', $role->id);
                } else {
                    $checked = in_array($role->id, old('roles', []));
                }
            ?>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->nombre }}</label>
            </div>
        @endforeach
    </div>
</div>


<button class="btn btn-primary" type="submit">Guardar</button>
<a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
