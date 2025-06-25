@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Role Management</h2>
            </div>
            <div class="pull-right">
                {{-- @can('role-create') --}}
                <a class="btn btn-success btn-sm mb-2" href="{{ route('roles.create') }}"><i class="fa fa-plus"></i> Create New
                    Role</a>
                {{-- @endcan --}}
            </div>
        </div>
    </div>

    @session('success')
        <div class="alert alert-success" role="alert">
            {{ $value }}
        </div>
    @endsession

    <table class="table table-bordered">
        {{-- @can('role-list') --}}
        <tr>
            <th width="100px">No</th>
            <th>Role Name</th>
            <th>Permissions Name</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($roles as $key => $role)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    @foreach ($role->getRolePermissions as $permission)
                        <li>{{ $permission->name }}</li>
                    @endforeach
                    {{-- @foreach ($role->permissions as $permission)
                                <li>{{ $permission->name }}</li>
                            @endforeach --}}
                </td>
                <td>
                    {{-- @can('role-show') --}}
                    <a class="btn btn-info btn-sm" href="{{ route('roles.show', $role->id) }}"><i
                            class="fa-solid fa-list"></i> Show</a>
                    {{-- @endcan --}}
                    {{-- @can('role-edit') --}}
                    <a class="btn btn-primary btn-sm" href="{{ route('roles.edit', $role->id) }}"><i
                            class="fa-solid fa-pen-to-square"></i> Edit</a>
                    {{-- @endcan --}}
                    {{-- @can('role-delete') --}}
                    <form method="POST" action="{{ route('roles.destroy', $role->id) }}" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i>
                            Delete</button>
                    </form>
                    {{-- @endcan --}}
                </td>
            </tr>
        @endforeach
        {{-- @endcan --}}
    </table>

    {!! $roles->links('pagination::bootstrap-5') !!}
@endsection
