@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit User</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary btn-sm mb-2" href="{{ route('users.index') }}">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
                </div>
                <div class="form-group">
                    <strong>Email:</strong>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
                </div>
                <div class="form-group">
                    <strong>Password:</strong>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                </div>
                <div class="form-group">
                    <strong>Confirm Password:</strong>
                    <input type="password" name="confirm-password" class="form-control">
                </div>
                <div class="form-group">
                    <strong>Roles:</strong>
                    <select name="roles[]" class="form-control" multiple>
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}" {{ in_array($value, $user->getRoleNames()->toArray()) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <strong>Direct Permissions:</strong><br>
                    @foreach ($permissions as $perm)
                        <label>
                            <input type="checkbox" name="permissions[{{ $perm->id }}]" value="{{ $perm->id }}"
                                {{ $user->permissions->contains('id', $perm->id) ? 'checked' : '' }}>
                            {{ $perm->name }}
                        </label><br>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-floppy-disk"></i> Update
            </button>
        </div>
    </form>
@endsection
