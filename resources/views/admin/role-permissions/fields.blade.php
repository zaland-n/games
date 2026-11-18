<div class="form-group">
    <label for="role_id">Rol</label>
    <select id="role_id" name="role_id" class="form-control" required>
        <option value="">Selecteer een rol</option>
        @foreach ($roles as $role)
            <option value="{{ $role->id }}" @selected((string) old('role_id', $assignment->role_id ?? '') === (string) $role->id)>
                {{ $role->name }} (ID: {{ $role->id }})
            </option>
        @endforeach
    </select>
    @error('role_id')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="form-group">
    <label for="permission_id">Permissie</label>
    <select id="permission_id" name="permission_id" class="form-control" required>
        <option value="">Selecteer een permissie</option>
        @foreach ($permissions as $permission)
            <option value="{{ $permission->id }}" @selected((string) old('permission_id', $assignment->permission_id ?? '') === (string) $permission->id)>
                {{ $permission->name }} (ID: {{ $permission->id }})
            </option>
        @endforeach
    </select>
    @error('permission_id')<div class="text-danger">{{ $message }}</div>@enderror
</div>
