@extends('layouts.main')

@section('content')
 <div class="content-wrapper">
      <section class="content pt-3">
        <div class="container-fluid">
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Edit Produk</h4>
        </div>

        <div class="card-body">
            {{-- Alert Success --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Validation --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nama Produk --}}
                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input 
                        type="text" 
                        name="name" 
                        class="form-control" 
                        value="{{ old('name', $product->name) }}" 
                        required
                    >
                </div>

                {{-- Tipe Produk --}}
                <div class="mb-3">
                    <label class="form-label">Tipe Produk</label>
                    <select name="type"
                                            class="form-control @error('type') is-invalid @enderror"
                                            required>
                                        <option value="">-- Select Type --</option>
                                        <option value="strip" {{ old('type') == 'strip' ? 'selected' : '' }}>Strip</option>
                                        <option value="fls" {{ old('type') == 'fls' ? 'selected' : '' }}>Fls</option>
                                        <option value="pcs" {{ old('type') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                        <option value="box" {{ old('type') == 'box' ? 'selected' : '' }}>Box</option>
                                        <option value="botol" {{ old('type') == 'botol' ? 'selected' : '' }}>Botol</option>
                                        <option value="tube" {{ old('type') == 'tube' ? 'selected' : '' }}>Tube</option>
                                        <option value="pack" {{ old('type') == 'pac' ? 'selected' : '' }}>Pack</option>
                                        <option value="sch" {{ old('type') == 'sch' ? 'selected' : '' }}>Sch</option>
                                        <option value="amp" {{ old('type') == 'amp' ? 'selected' : '' }}>Amp</option>
                                    </select>
                </div>
                <div class="d-flex align-items-center gap-2 mt-3">
                    <a href="{{ route('user.products.index') }}" class="btn btn-secondary">
                        ← Kembali
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
        </div>
    </section>
</div>
@endsection
