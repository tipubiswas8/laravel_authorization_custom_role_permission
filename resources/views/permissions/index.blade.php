@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Permission Management</h2>
            </div>
            <div class="pull-right">
                {{-- @can('permission-create') --}}
                <a class="btn btn-success btn-sm mb-2" href="{{ route('permissions.create') }}"><i class="fa fa-plus"></i>
                    Create New Permission</a>
                {{-- @endcan --}}
            </div>
        </div>
    </div>

    @session('success')
        <div class="alert alert-success" permission="alert">
            {{ $value }}
        </div>
    @endsession

    <table class="table table-bordered">
        {{-- @can('permission-list') --}}
        <tr>
            <th width="100px">No</th>
            <th>Permission Name</th>
            <th>Roles Name</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($permissions as $key => $permission)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $permission->name }}</td>
                <td>
                    @foreach ($permission->getPermissionRoles as $role)
                        <li>{{ $role->name }}</li>
                    @endforeach
                    {{-- @foreach ($role->getRolePermissions as $permission)
                        <li>{{ $permission->name }}</li>
                    @endforeach --}}
                </td>
                <td>
                    {{-- @can('permission-show') --}}
                    <a class="btn btn-info btn-sm" href="{{ route('permissions.show', $permission->id) }}"><i
                            class="fa-solid fa-list"></i> Show</a>
                    {{-- @endcan --}}
                    {{-- @can('permission-edit') --}}
                    <a class="btn btn-primary btn-sm" href="{{ route('permissions.edit', $permission->id) }}"><i
                            class="fa-solid fa-pen-to-square"></i> Edit</a>
                    {{-- @endcan --}}
                    {{-- @can('permission-delete') --}}
                    <form method="POST" action="{{ route('permissions.destroy', $permission->id) }}"
                        style="display:inline">
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

    {!! $permissions->links('pagination::bootstrap-5') !!}
@endsection
