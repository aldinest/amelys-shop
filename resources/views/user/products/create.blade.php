@extends('layouts.main')

@section('content')
<div class="content-wrapper">

    {{-- HEADER --}}
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!-- <h1>Tambah Produk</h1> -->
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Tambah Produk</li>
                </ol>
            </div>
        </div>
    </div>
</section>


    {{-- CONTENT --}}
    <section class="content">
        <div class="container-fluid">

            <div class="row justify-content-center">
                <div class="col-md-6">

                    <div class="card">

                        <div class="card-header">
                            <strong>Form Produk</strong>
                        </div>

                        <form action="{{ route('user.products.store') }}" method="POST">
                            @csrf

                            <div class="card-body">

                                {{-- NAMA PRODUK --}}
                                <div class="form-group">
                                    <label>Nama Produk</label>
                                    <input type="text"
                                           name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}"
                                           placeholder="Contoh: Paracetamol"
                                           required>

                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- JENIS PRODUK --}}
                                <div class="form-group">
                                    <label>Jenis Produk</label>
                                    <select name="type"
                                            class="form-control @error('type') is-invalid @enderror"
                                            required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="botol" {{ old('type') == 'botol' ? 'selected' : '' }}>Botol</option>
                                        <option value="box" {{ old('type') == 'box' ? 'selected' : '' }}>Box</option>
                                        <option value="strip" {{ old('type') == 'strip' ? 'selected' : '' }}>Strip</option>
                                        <option value="tablet" {{ old('type') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                                        <option value="pcs" {{ old('type') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                    </select>

                                    @error('type')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                            </div>

                            {{-- FOOTER --}}
                            <div class="card-footer d-flex justify-content-start">
                                <a href="{{ route('user.products.index') }}" class="btn btn-secondary mr-2">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>

                                <button class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
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
