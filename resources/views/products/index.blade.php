@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Products</h2>
            </div>
            <div class="pull-right">
                @hasPermission('product-create')
                    <a class="btn btn-success btn-sm mb-2" href="{{ route('products.create') }}"><i class="fa fa-plus"></i> Create
                        New Product</a>
                @endhasPermission
            </div>
        </div>
    </div>

    @session('success')
        <div class="alert alert-success" role="alert">
            {{ $value }}
        </div>
    @endsession

    <table class="table table-bordered">
        @hasPermission('product-list')
        <tr>
            <th>No</th>
            <th>Product Name</th>
            <th>Details</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($products as $product)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->detail }}</td>
                <td>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                        @hasPermission('product-show')
                            <a class="btn btn-info btn-sm" href="{{ route('products.show', $product->id) }}"><i
                                    class="fa-solid fa-list"></i> Show</a>
                        @endhasPermission
                        @hasPermission('product-edit')
                            <a class="btn btn-primary btn-sm" href="{{ route('products.edit', $product->id) }}"><i
                                    class="fa-solid fa-pen-to-square"></i> Edit</a>
                        @endhasPermission
                        @csrf
                        @method('DELETE')
                        @hasPermission('product-delete')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i>
                                Delete</button>
                        @endhasPermission
                    </form>
                </td>
            </tr>
        @endforeach
        @endhasPermission
    </table>

    {!! $products->links() !!}
@endsection
