@extends('layouts.main')

@section('content')

<style>
/* KUNCI TINGGI & STYLE FIELD */
.item-row input,
.item-row select,
.select2-container .select2-selection--single {
    height: 40px !important;
}

.select2-container--bootstrap4 .select2-selection--single {
    display: flex;
    align-items: center;
}

/* DESKTOP OPTIMIZATION */
@media (min-width: 768px) {
    .item-row small, .item-row br {
        display: none !important;
    }
    .item-no, .subtotal-container {
        line-height: 40px;
    }
}

/* MOBILE OPTIMIZATION */
@media (max-width: 767px) {
    .item-row {
        background: #fdfdfd;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 15px !important;
        margin-bottom: 15px !important;
    }
    .product-col {
        margin-bottom: 15px;
    }
    .item-no {
        background: #6c757d;
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        line-height: 25px;
        margin-bottom: 10px;
    }
}

.item-row { transition: all 0.2s; }
.item-row:hover { background: #f8f9fa; }
</style>

<div class="content-wrapper">
<section class="content pt-4 pb-5">
<div class="container-fluid">

<form method="POST" action="{{ route('user.orders.store') }}">
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
                    <input type="date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="small font-weight-bold text-muted">E-Commerce</label>
                    <select name="e_commerce" id="eCommerceSelect" class="form-control">
                        <option value="Shopee">Shopee</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="Tokopedia">Tokopedia</option>
                        <option value="TikTok Shop">TikTok Shop</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="small font-weight-bold text-muted">Nomor Pesanan</label>
                    <input type="text" name="order_number" id="orderNumberInput" 
                        class="form-control @error('order_number') is-invalid @enderror" 
                        value="{{ old('order_number') }}"
                        placeholder="Masukkan nomor pesanan">
                    
                    @error('order_number')
                        <div class="invalid-feedback">
                            Nomor pesanan ini sudah terdaftar, silakan gunakan nomor lain.
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="small font-weight-bold text-muted">Status</label>
                    {{-- Tambahkan id="statusSelect" di bawah ini --}}
                    <select name="status" id="statusSelect" class="form-control">
                        <option value="processing">Diproses</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label class="small font-weight-bold text-muted">Nama Customer</label>
                    <input type="text" name="customer_name" class="form-control">
                </div>
            </div>

            </div>
        </div>

    {{-- ITEM PESANAN --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-weight-bold mb-0">Item Pesanan</h5>
                <button type="button" id="addRow" class="btn btn-primary btn-sm px-3">
                    + Tambah Produk
                </button>
            </div>

            {{-- HEADER --}}
            <div class="row text-muted small font-weight-bold border-bottom pb-2 mb-3 d-none d-md-flex">
                <div class="col-md-1 text-center">No</div>
                <div class="col-md-4">Produk</div>
                <div class="col-md-2 text-center">Harga</div>
                <div class="col-md-2 text-center">Qty</div>
                <div class="col-md-2 text-right">Subtotal</div>
                <div class="col-md-1 text-center">Aksi</div>
            </div>

            {{-- BODY --}}
            <div id="itemsBody">

            <div class="row align-items-center mb-3 border-bottom pb-3 item-row">
                {{-- NO --}}
                <div class="col-12 col-md-1 text-md-center font-weight-bold mb-2 mb-md-0">
                    <div class="item-no mx-auto mx-md-0">1</div>
                </div>

                {{-- PRODUK --}}
                <div class="col-12 col-md-4 mb-3 mb-md-0 product-col">
                    <label class="small font-weight-bold d-md-none text-muted">Produk</label>
                    <select name="items[0][product_id]" class="form-control product-select">
                        <option value="">Pilih produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- HARGA --}}
                <div class="col-6 col-md-2 mb-3 mb-md-0">
                    <label class="small font-weight-bold d-md-none text-muted">Harga</label>
                    <input type="number" name="items[0][price]" class="form-control text-center price" value="0">
                </div>

                {{-- QTY --}}
                <div class="col-6 col-md-2 mb-3 mb-md-0">
                    <label class="small font-weight-bold d-md-none text-muted">Qty</label>
                    <input type="number" name="items[0][qty]" class="form-control text-center qty" value="1" min="1">
                </div>

                {{-- SUBTOTAL & AKSI (Digabung di Mobile agar rapi) --}}
                <div class="col-8 col-md-2 text-md-right subtotal-container">
                    <label class="small font-weight-bold d-md-none text-muted">Subtotal</label><br class="d-md-none">
                    <span class="font-weight-bold text-danger">Rp <span class="subtotal">0</span></span>
                </div>

                <div class="col-4 col-md-1 text-right text-md-center">
                    <label class="small font-weight-bold d-md-none text-muted">Hapus</label><br class="d-md-none">
                    <button type="button" class="btn btn-outline-danger btn-sm remove px-3">✕</button>
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

                {{-- INPUT HIDDEN INI HARUS ADA DI DALAM FORM --}}
                <input type="hidden" name="net_total" id="netTotalInput" value="0">

                <button type="submit" class="btn btn-danger btn-block font-weight-bold">
                    Simpan Pesanan
                </button>

                <a href="{{ route('user.orders.index') }}"
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

// =====================
// UPDATE NOMOR ITEM
// =====================
function updateItemNumber() {
    document.querySelectorAll('#itemsBody .item-row').forEach((row, i) => {
        row.querySelector('.item-no').innerText = i + 1;
    });
}

// =====================
// Kunci No Pesanan & Status WhatsApp
// =====================
document.getElementById('eCommerceSelect').addEventListener('change', function() {
    const orderInput = document.getElementById('orderNumberInput');
    const statusSelect = document.getElementById('statusSelect'); // Ambil element status
    
    if (this.value === 'WhatsApp') {
        // Logika Nomor Pesanan
        orderInput.value = ''; 
        orderInput.placeholder = 'Otomatis Sistem (WA-Tgl-Jam)';
        orderInput.readOnly = true;
        orderInput.classList.add('bg-light');

        // Logika Status (Kunci ke Selesai)
        statusSelect.value = 'completed'; // Set ke Selesai
        statusSelect.disabled = true;      // Kunci input
        
        // Tambahkan input hidden agar value 'status' tetap terkirim ke backend 
        // karena input yang 'disabled' tidak akan masuk ke request POST
        if (!document.getElementById('hiddenStatus')) {
            let hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'status';
            hiddenInput.value = 'completed';
            hiddenInput.id = 'hiddenStatus';
            statusSelect.parentNode.appendChild(hiddenInput);
        }
    } else {
        // Kembalikan Nomor Pesanan
        orderInput.placeholder = 'Masukkan nomor pesanan';
        orderInput.readOnly = false;
        orderInput.classList.remove('bg-light');

        // Kembalikan Status
        statusSelect.disabled = false;
        
        // Hapus input hidden jika ada
        const hiddenInput = document.getElementById('hiddenStatus');
        if (hiddenInput) {
            hiddenInput.remove();
        }
    }
});

// =====================
// Menampilkan total WA di index orders
// =====================
function calculate() {
    let total = 0;
    const eCommerce = document.getElementById('eCommerceSelect').value;
    const netTotalInput = document.getElementById('netTotalInput');

    // Hitung total dari baris produk
    document.querySelectorAll('#itemsBody .item-row').forEach(row => {
        const price = Number(row.querySelector('.price').value) || 0;
        const qty   = Number(row.querySelector('.qty').value) || 0;
        const sub   = price * qty;

        row.querySelector('.subtotal').innerText = sub.toLocaleString('id-ID');
        total += sub;
    });

    // Update teks Grand Total di layar kanan
    document.getElementById('grandTotal').innerText = total.toLocaleString('id-ID');

    // LOGIKA KRUSIAL: Isi input hidden
    if (netTotalInput) {
        if (eCommerce === 'WhatsApp') {
            netTotalInput.value = total;
            console.log("Input Hidden Terisi (WA): ", netTotalInput.value);
        } else {
            netTotalInput.value = 0;
            console.log("Input Hidden Terisi (Lain): ", netTotalInput.value);
        }
    } else {
        console.error("WADUH: Element netTotalInput nggak ketemu di HTML!");
    }
}

// Tambahkan listener agar saat pilih produk di Select2, harga otomatis terisi
$(document).on('select2:select', '.product-select', function(e) {
    const price = e.params.data.element.dataset.price;
    const row = $(this).closest('.item-row');
    row.find('.price').val(price); // Set harga otomatis
    calculate(); // Hitung ulang
});

//Allert Notif
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        timer: 3000
    });
@endif

@if($errors->has('order_number'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal Simpan!',
        text: 'Nomor pesanan sudah ada di sistem. Mohon cek kembali.',
    });
@endif


// =====================
// INIT SELECT2
// =====================
function initSelect2(el) {
    $(el).each(function () {
        if ($(this).data('select2')) return;

        $(this).select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih produk',
            allowClear: true,
            width: '100%'
        });
    });
}

// =====================
// INPUT LISTENER
// =====================
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('price') ||
        e.target.classList.contains('qty')) {
        calculate();
    }
});

// =====================
// TAMBAH ROW
// =====================
document.getElementById('addRow').addEventListener('click', function () {

    const firstSelect = document.querySelector(
        '#itemsBody .item-row select'
    );

    const cloned = firstSelect.cloneNode(true);
    cloned.value = '';
    cloned.name = `items[${index}][product_id]`;

    const row = document.createElement('div');
    row.className = 'row align-items-center mb-3 border-bottom pb-3 item-row';

    row.innerHTML = `
        <div class="col-12 col-md-1 text-md-center font-weight-bold mb-2 mb-md-0">
            <div class="item-no mx-auto mx-md-0"></div>
        </div>

        <div class="col-12 col-md-4 mb-3 mb-md-0 product-col">
            <label class="small font-weight-bold d-md-none text-muted">Produk</label>
        </div>

        <div class="col-6 col-md-2 mb-3 mb-md-0">
            <label class="small font-weight-bold d-md-none text-muted">Harga</label>
            <input type="number" name="items[${index}][price]" class="form-control text-center price" value="0">
        </div>

        <div class="col-6 col-md-2 mb-3 mb-md-0">
            <label class="small font-weight-bold d-md-none text-muted">Qty</label>
            <input type="number" name="items[${index}][qty]" class="form-control text-center qty" value="1" min="1">
        </div>

        <div class="col-8 col-md-2 text-md-right subtotal-container">
            <label class="small font-weight-bold d-md-none text-muted">Subtotal</label><br class="d-md-none">
            <span class="font-weight-bold text-danger">Rp <span class="subtotal">0</span></span>
        </div>

        <div class="col-4 col-md-1 text-right text-md-center">
            <label class="small font-weight-bold d-md-none text-muted">Hapus</label><br class="d-md-none">
            <button type="button" class="btn btn-outline-danger btn-sm remove px-3">✕</button>
        </div>
    `;

    row.querySelector('.product-col').appendChild(cloned);
    document.getElementById('itemsBody').appendChild(row);

    initSelect2(cloned);
    index++;

    updateItemNumber();
});

// =====================
// HAPUS ROW
// =====================
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove')) {
        e.target.closest('.item-row').remove();
        calculate();
        updateItemNumber();
    }
});

// =====================
// INIT AWAL
// =====================
$(document).ready(function () {
    initSelect2('.product-select');
    updateItemNumber();
    
    // TAMBAHKAN INI: Panggil hitung saat awal biar input hidden sinkron
    calculate(); 

    // Tambahan: Pastikan setiap ada perubahan Select2, fungsi hitung jalan
    $('.product-select').on('change', function() {
        calculate();
    });
});
</script>
@endpush
