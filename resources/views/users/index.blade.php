@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Users Management</h2>
            </div>
            @hasPermission('user-create')
                <div class="pull-right">
                    <a class="btn btn-success mb-2" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> Create New
                        User</a>
                </div>
            @endhasPermission
        </div>
    </div>

    @session('success')
        <div class="alert alert-success" role="alert">
            {{ $value }}
        </div>
    @endsession
    <table class="table table-bordered">
        @hasPermission('user-list')
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Diredc Permissions</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($data as $key => $user)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if (!empty($user->getRoleNames()))
                            @foreach ($user->getRoleNames() as $v)
                                <label class="badge bg-success">{{ $v }}</label>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if ($user->permissions->isNotEmpty())
                            @foreach ($user->permissions as $permission)
                                <label style="background-color: gray" class="badge badge-info">{{ $permission->name }}</label>
                            @endforeach
                        @else
                            <p>No direct permissions</p>
                        @endif
                    </td>
                    <td>
                        @hasPermission('user-show')
                            <a class="btn btn-info btn-sm" href="{{ route('users.show', $user->id) }}"><i
                                    class="fa-solid fa-list"></i> Show</a>
                        @endhasPermission
                        @hasPermission('user-edit')
                            <a class="btn btn-primary btn-sm" href="{{ route('users.edit', $user->id) }}"><i
                                    class="fa-solid fa-pen-to-square"></i> Edit</a>
                        @endhasPermission
                        @hasPermission('user-delete')
                            <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display:inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i>
                                    Delete</button>
                            </form>
                        @endhasPermission
                    </td>
                </tr>
            @endforeach
        @endhasPermission
    </table>

    {!! $data->links('pagination::bootstrap-5') !!}
@endsection
