@extends('layouts.main')

@section('content')
<div class="content-wrapper">

    {{-- ALERT --}}
    <div class="mx-3 mt-3">
        @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
            @if (session($key))
                <div class="alert alert-{{ $type }} alert-dismissible fade show">
                    {{ session($key) }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
        @endforeach
    </div>

    {{-- HEADER --}}
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between">
            <h1>Products</h1>

            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    </section>

    {{-- CONTENT --}}
    <section class="content">
        <div class="container-fluid">
            <div class="card">

<div class="card-header">

    <div class="d-flex align-items-center">
        <strong>Product List</strong>

        <form method="GET"
              action="{{ route('products.index') }}"
              class="ml-auto d-flex">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control form-control-sm mr-2"
                   placeholder="Search product...">

            <button type="submit" class="btn btn-sm btn-primary mr-2">
                <i class="fas fa-search"></i>
            </button>

            @if(request('search'))
                <a href="{{ route('products.index') }}"
                   class="btn btn-sm btn-secondary">
                    Reset
                </a>
            @endif

        </form>
    </div>

</div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Product Name</th>
                                <th>Type</th>
                                <th width="150" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td class="text-center">
                                        {{ $products->firstItem() + $loop->index }}
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ ucfirst($product->type) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('products.edit', $product->id) }}"
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('products.destroy', $product->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No products found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <small class="text-muted">
                        Showing {{ $products->firstItem() }} - {{ $products->lastItem() }}
                        of {{ $products->total() }}
                    </small>

                    {{ $products->links() }}
                </div>

            </div>
        </div>
    </section>
</div>
@endsection
