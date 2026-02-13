@extends('layouts.main')

@section('content')
<div class="content-wrapper">
<section class="content pt-4 pb-5">
<div class="container-fluid">

<form method="POST" action="{{ route('orders.store') }}">
@csrf

{{-- HEADER --}}
<div class="mb-4">
    <h2 class="font-weight-bold mb-1">Buat Pesanan Baru</h2>
    <p class="text-muted mb-0">Lengkapi informasi pesanan di bawah ini</p>
</div>

<div class="row">

    {{-- KIRI --}}
    <div class="col-lg-8">

        {{-- INFORMASI PESANAN --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="font-weight-bold mb-3">Informasi Pesanan</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-muted">Tanggal Pesanan</label>
                        <input type="date" name="order_date"
                               class="form-control"
                               value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-muted">Nomor Pesanan</label>
                        <input type="text" name="order_number"
                               class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-muted">E-Commerce</label>
                        <select name="e_commerce" class="form-control">
                            <option value="Shopee">Shopee</option>
                            <option value="Tokopedia">Tokopedia</option>
                            <option value="TikTok Shop">TikTok Shop</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-muted">Status</label>
                        <select name="status" class="form-control">
                            <option value="processing">Diproses</option>
                            <option value="completed">Selesai</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="small font-weight-bold text-muted">Nama Customer</label>
                        <input type="text" name="customer_name"
                               class="form-control">
                    </div>
                </div>
            </div>
        </div>

        {{-- ITEM PESANAN --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold mb-0">Item Pesanan</h5>
                    <button type="button"
                            id="addRow"
                            class="btn btn-primary btn-sm px-3">
                        + Tambah Produk
                    </button>
                </div>

                {{-- HEADER --}}
                <div class="row text-muted small font-weight-bold border-bottom pb-2 mb-3">
                    <div class="col-md-5">Produk</div>
                    <div class="col-md-2 text-center">Harga</div>
                    <div class="col-md-2 text-center">Qty</div>
                    <div class="col-md-2 text-right">Subtotal</div>
                    <div class="col-md-1 text-center"></div>
                </div>

                {{-- BODY --}}
                <div id="itemsBody">

                    <div class="row align-items-center mb-3 border-bottom pb-3 item-row">

<div class="col-md-5">
    <select name="items[0][product_id]"
            class="form-control product-select">
        <option value="">Pilih produk</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}"
                    data-price="{{ $product->price }}">
                {{ $product->name }}
            </option>
        @endforeach
    </select>
</div>

                        <div class="col-md-2">
                            <input type="number"
                                   name="items[0][price]"
                                   class="form-control text-center price"
                                   value="0">
                        </div>

                        <div class="col-md-2">
                            <input type="number"
                                   name="items[0][qty]"
                                   class="form-control text-center qty"
                                   value="1" min="1">
                        </div>

                        <div class="col-md-2 text-right font-weight-bold">
                            Rp <span class="subtotal">0</span>
                        </div>

                        <div class="col-md-1 text-center">
                            <button type="button"
                                    class="btn btn-danger btn-sm remove">
                                ✕
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

    {{-- KANAN --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <h6 class="text-muted text-uppercase small mb-3">
                    Ringkasan
                </h6>

                <div class="text-muted mb-2">
                    Total Keseluruhan
                </div>

                <h2 class="text-danger font-weight-bold mb-4">
                    Rp <span id="grandTotal">0</span>
                </h2>

                <button type="submit"
                        class="btn btn-danger btn-block font-weight-bold">
                    Simpan Pesanan
                </button>

                <a href="{{ route('orders.index') }}"
                   class="btn btn-light btn-block mt-2">
                    Kembali
                </a>

            </div>
        </div>
    </div>

</div>

</form>
</div>
</section>
</div>
@endsection


@push('scripts')
<script>
let index = 1;

function calculate() {
    let total = 0;

    document.querySelectorAll('#itemsBody .item-row').forEach(row => {

        const price = Number(row.querySelector('.price').value) || 0;
        const qty   = Number(row.querySelector('.qty').value) || 0;
        const sub   = price * qty;

        row.querySelector('.subtotal').innerText =
            sub.toLocaleString('id-ID');

        total += sub;
    });

    document.getElementById('grandTotal').innerText =
        total.toLocaleString('id-ID');
}

// trigger hitung
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('price') ||
        e.target.classList.contains('qty')) {
        calculate();
    }
});

// tambah row
document.getElementById('addRow').addEventListener('click', function () {

    const options = document.querySelector('.product').innerHTML;

    const row = `
    <div class="row align-items-center mb-3 border-bottom pb-3 item-row">
        <div class="col-md-5">
            <select name="items[${index}][product_id]"
                    class="form-control product">
                ${options}
            </select>
        </div>

        <div class="col-md-2">
            <input type="number"
                   name="items[${index}][price]"
                   class="form-control text-center price"
                   value="0">
        </div>

        <div class="col-md-2">
            <input type="number"
                   name="items[${index}][qty]"
                   class="form-control text-center qty"
                   value="1" min="1">
        </div>

        <div class="col-md-2 text-right font-weight-bold">
            Rp <span class="subtotal">0</span>
        </div>

        <div class="col-md-1 text-center">
            <button type="button"
                    class="btn btn-danger btn-sm remove">
                ✕
            </button>
        </div>
    </div>
    `;

    document.getElementById('itemsBody')
        .insertAdjacentHTML('beforeend', row);

    index++;
});

// hapus row
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove')) {
        e.target.closest('.item-row').remove();
        calculate();
    }
});

    $(function () {
        $('.product-select').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih produk',
            allowClear: true,
            width: '100%'
        });
    });


</script>

@endpush
