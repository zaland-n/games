<div class="form-group">
    <label for="user_id">Gebruiker</label>
    <select id="user_id" name="user_id" class="form-control" required>
        <option value="">Selecteer een gebruiker</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected((string) old('user_id', $assignment->user_id ?? '') === (string) $user->id)>
                {{ $user->name }} — {{ $user->email }} (ID: {{ $user->id }})
            </option>
        @endforeach
    </select>
    @error('user_id')<div class="text-danger">{{ $message }}</div>@enderror
</div>
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
