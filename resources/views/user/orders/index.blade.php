@extends('layouts.main')

@section('content')

<style>
    /* Mengatasi bug teks turun ke bawah */
    .text-nowrap {
        white-space: nowrap !important;
    }

    /* Membuat jarak antar tombol seragam */
    .gap-2 {
        gap: 0.5rem !important;
    }

    /* Memastikan semua input & button punya tinggi yang sama */
    .form-control-sm, 
    .btn-sm, 
    .input-group-text,
    .dropdown-toggle {
        height: 31px !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Khusus untuk dropdown agar teks e-commerce rata kiri */
    .dropdown-toggle {
        justify-content: space-between;
    }

    /* Jarak vertikal saat layar mengecil (mobile) */
    @media (max-width: 768px) {
        .mb-1 {
            margin-bottom: 0.5rem !important;
        }
    }

    /* Style row untuk data yang sudah dicetak agar kelihatan clean & beda warna */
    .row-printed {
        background-color: rgba(220, 224, 230, 0.5) !important;
        color: #6c757d !important;
    }

    /* Merapikan input date agar ikon tidak menumpuk */
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.6;
    }

    input[type="date"]:hover::-webkit-calendar-picker-indicator {
        opacity: 1;
    }

    /* Opsional: Membuat input lebih compact */
    .input-group-sm .form-control {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    /* UBAH DARI GRID KE FLEXBOX AGAR LEBIH FLEXIBLE */
    .filter-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    /* Berikan lebar fleksibel agar tidak dempet */
    .filter-item {
        flex: 1 1 150px; /* Minimal 150px, bisa melebar */
        min-width: 120px;
    }

    /* Khusus untuk pembungkus tanggal agar tidak terpotong */
    .filter-date-range {
        flex: 1 1 250px; /* Tanggal butuh ruang lebih luas */
        min-width: 220px;
    }

    /* Memastikan tombol filter & reset tidak ikut membesar */
    .filter-actions {
        flex: 0 0 auto;
        display: flex;
        gap: 5px;
    }

    @media (max-width: 768px) {
        .filter-item, .filter-date-range {
            flex: 1 1 100%; /* Di mobile, semua jadi full width */
        }
    }

    .row-printed:hover {
    background-color: rgba(220, 224, 230, 0.8) !important; /* Lebih gelap dikit pas dihover */
    }
</style>

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
        <div class="container-fluid">
            <div class="row align-items-center mb-2">

                {{-- JUDUL --}}
                <div class="col-md-4 col-12">
                    <h1 class="mb-0">Daftar Order</h1>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="col-md-8 col-12 d-flex justify-content-md-end gap-2 mt-2 mt-md-0 flex-wrap align-items-center">
                    
                    {{-- INDIKATOR BUFFER DATA TERPILIH --}}
                    <span id="pilihan-counter" class="badge badge-warning py-2 px-3 text-dark shadow-sm d-none align-items-center">
                        <i class="fas fa-check-square mr-1"></i> Terpilih: <strong id="count-terpilih" class="mx-1">0</strong> order
                    </span>

                    {{-- Tombol Excel --}}
                    <a href="/user/orders/export/excel?{{ http_build_query(request()->query()) }}" 
                       id="btn-excel" class="btn btn-success btn-sm shadow-sm">
                        Excel
                    </a>

                    {{-- Tombol Cetak PDF Stock --}}
                    <a href="/user/orders/export/pdf?{{ http_build_query(request()->query()) }}" 
                       id="btn-pdf-stok" class="btn btn-danger btn-sm shadow-sm">
                        Cetak PDF Stok
                    </a>

                    {{-- Tombol Cetak PDF All --}}
                    <a href="/user/orders/print?{{ http_build_query(request()->query()) }}" 
                       id="btn-pdf-all" target="_blank" class="btn btn-primary btn-sm shadow-sm">
                        Cetak PDF All
                    </a>

                    {{-- Tombol Tambah Order --}}
                    <a href="/user/orders/create" class="btn btn-primary btn-sm shadow-sm">
                        <i class="fas fa-plus"></i> Tambah Order
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- CONTENT --}}
    <section class="content">
        <div class="container-fluid">
            <div class="card shadow-sm">

                {{-- HEADER TABEL & FILTER --}}
                <div class="card-header">
                    <form method="GET" action="/user/orders" id="form-filter">
                        <div class="filter-wrapper">
                            
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="form-control form-control-sm filter-item">

                            <select name="e_commerce" class="form-control form-control-sm filter-item">
                                <option value="">E-Commerce</option>
                                <option value="Shopee" {{ request('e_commerce') == 'Shopee' ? 'selected' : '' }}>Shopee</option>
                                <option value="WhatsApp" {{ request('e_commerce') == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="Tokopedia" {{ request('e_commerce') == 'Tokopedia' ? 'selected' : '' }}>Tokopedia</option>
                                <option value="TikTok Shop" {{ request('e_commerce') == 'TikTok Shop' ? 'selected' : '' }}>TikTok Shop</option>
                            </select>

                            <select name="status" class="form-control form-control-sm filter-item">
                                <option value="">Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Proses</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>

                            <select name="print_status" class="form-control form-control-sm filter-item">
                                <option value="">Status Cetak</option>
                                <option value="belum" {{ request('print_status') == 'belum' ? 'selected' : '' }}>Belum Dicetak</option>
                                <option value="sudah" {{ request('print_status') == 'sudah' ? 'selected' : '' }}>Sudah Dicetak</option>
                            </select>

                            <div class="input-group input-group-sm filter-date-range">
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                                <span class="input-group-text px-1">s/d</span>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                            </div>

                            <div class="filter-actions">
                                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                                <a href="/user/orders" class="btn btn-outline-secondary btn-sm btn-reset">Reset</a>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="mb-4"></div>

                {{-- TABLE --}}
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0" id="table-orders">
                        <thead class="thead-light">
                            <tr>
                                <th width="40" class="text-center">
                                    <input type="checkbox" id="check-all-orders">
                                </th>
                                <th width="60">No</th>
                                <th>No Order</th>
                                <th>Tanggal</th>
                                <th>E-Commerce</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th class="text-right">Total Bersih</th>
                                <th class="text-center" width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $i => $order)
                                <tr class="{{ $order->is_printed ? 'row-printed' : '' }}">
                                    <td class="text-center">
                                       <input type="checkbox" class="order-item-checkbox" value="{{ $order->order_number }}">
                                    </td>
                                    <td>{{ $orders->firstItem() + $i }}</td>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                        <br>
                                        @if($order->is_printed)
                                            <small class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Sudah Pernah Dicetak</small>
                                        @else
                                            <small class="text-muted"><i class="fas fa-clock"></i> Belum Pernah Dicetak</small>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}</td>
                                    <td>{{ $order->e_commerce }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>
                                        <span class="badge
                                            @if ($order->status == 'pending') badge-warning
                                            @elseif($order->status == 'processing') badge-info
                                            @elseif($order->status == 'shipped') badge-primary
                                            @else badge-success @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        Rp {{ number_format($order->net_total ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="/user/orders/{{ $order->order_number }}?{{ request()->getQueryString() }}" class="btn btn-xs btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('user.orders.edit', $order->order_number) }}?{{ request()->getQueryString() }}" class="btn btn-xs btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('user.orders.destroy', $order->order_number) }}?{{ request()->getQueryString() }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus order ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-xs btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Tidak ada data order
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER --}}
                <div class="card-footer d-flex align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} data
                    </small>
                    <div class="ml-auto">
                        {{ $orders->links() }}
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

@endsection

{{-- JAVASCRIPT HANDLER --}}
@push('scripts')
<script>
$(document).ready(function() {
    console.log("=== SCRIPT TRACKING ORDERS AMELYS SHOP AKTIF ===");

    const STORAGE_KEY = 'amelys_selected_order_ids';
    let isPreventLoop = false;

    // Ambil URL dasar dari Laravel Blade
    const urlAsliExcel   = "/user/orders/export/excel?{!! http_build_query(request()->except('selected_ids')) !!}";
    const urlAsliPdfStok = "/user/orders/export/pdf?{!! http_build_query(request()->except('selected_ids')) !!}";
    const urlAsliPdfAll  = "/user/orders/print?{!! http_build_query(request()->except('selected_ids')) !!}";
    const urlPrintMassal = "/user/orders/print-massal"; 

    // Cegah dropdown filter e-commerce menutup otomatis pas diklik di area dalam dropdown
    $('.dropdown-menu-ecom').on('click', function(e) {
        e.stopPropagation();
    });

    $('#form-filter').on('submit', function() {
    // Saat user klik filter, hapus pilihan lama agar data baru bersih
    localStorage.removeItem(STORAGE_KEY);
    });

    $('.btn-reset').on('click', function() {
    localStorage.removeItem(STORAGE_KEY);
    });

    // Otomatis kirim form filter jika isi checkbox e-commerce berubah status
    // $(document).on('change', '.filter-ecom-checkbox', function() {
    //     $('#form-filter').submit();
    // });

    function getStoredIds() {
        let stored = localStorage.getItem(STORAGE_KEY);
        if (!stored) return [];
        try {
            let parsed = JSON.parse(stored);
            return Array.isArray(parsed) ? [...new Set(parsed.map(String))].filter(Boolean) : [];
        } catch (e) {
            return [];
        }
    }

    function setStoredIds(ids) {
        let uniqueIds = [...new Set(ids.map(String))].filter(Boolean);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(uniqueIds));
        console.log("LocalStorage Berhasil Terupdate:", uniqueIds);
        sinkronisasiTombolAksi();
    }

    function handleIdSelection(id, isChecked) {
        let storedIds = getStoredIds();
        if (isChecked) {
            if (!storedIds.includes(id)) storedIds.push(id);
        } else {
            storedIds = storedIds.filter(item => item !== id);
        }
        setStoredIds(storedIds);
    }

    function isICheckActive($el) {
        // Memastikan plugin ter-load DAN elemen tersebut memang sudah di-render oleh iCheck
        return typeof $.fn.iCheck !== 'undefined' && $el.parent().hasClass('icheckbox_minimal-blue');
    }

    function updateMasterCheckboxStatus() {
        let totalCheckboxOnPage = $('.order-item-checkbox').length;
        let totalCheckedOnPage = $('.order-item-checkbox:checked').length;
        let $masterCheckbox = $('#check-all-orders');
        
        if (totalCheckboxOnPage > 0 && totalCheckedOnPage === totalCheckboxOnPage) {
            $masterCheckbox.prop('checked', true);
            if (isICheckActive($masterCheckbox)) {
                $masterCheckbox.iCheck('update');
            }
        } else {
            $masterCheckbox.prop('checked', false);
            if (isICheckActive($masterCheckbox)) {
                $masterCheckbox.iCheck('update');
            }
        }
    }

    function pulihkanCentangan() {
        isPreventLoop = true;
        let storedIds = getStoredIds();
        console.log("Memulai Sinkronisasi Centangan Visual. Memori:", storedIds);

        let $checkboxes = $('.order-item-checkbox');

        // Reset state awal DOM
        $checkboxes.prop('checked', false);
        $checkboxes.each(function() {
            if (isICheckActive($(this))) {
                $(this).iCheck('uncheck');
            }
        });

        // Ceklist ulang baris data yang ID-nya ada di memori
        if (storedIds.length > 0) {
            $checkboxes.each(function() {
                let id = String($(this).val());
                if (storedIds.includes(id)) {
                    $(this).prop('checked', true);
                    if (isICheckActive($(this))) {
                        $(this).iCheck('check');
                    }
                }
            });
        }

        updateMasterCheckboxStatus();
        isPreventLoop = false; 
        sinkronisasiTombolAksi();
    }

    // ========================================================
    // LOGIKA PENANGKAPAN EVENT HYBRID (ICHECK & NATIVE BROWSER)
    // ========================================================
    
    // 1. Handler untuk Checkbox Baris Data (Row)
    $(document).on('ifChanged change', '.order-item-checkbox', function(e) {
        if (isPreventLoop) return;
        
        let id = String($(this).val());
        let isChecked = $(this).prop('checked');
        
        console.log("Event Checkbox Row (" + e.type + ")! ID:", id, "Checked:", isChecked);
        handleIdSelection(id, isChecked);
        
        isPreventLoop = true;
        updateMasterCheckboxStatus();
        isPreventLoop = false;
    });

    // 2. Handler untuk Checkbox Master (Head / All)
    $(document).on('ifClicked change', '#check-all-orders', function(e) {
        if (isPreventLoop) return;
        
        // iCheck 'ifClicked' terpicu SEBELUM prop checked berubah, maka harus di-invert (!)
        // Sedangkan native 'change' terpicu SETELAH prop checked berubah.
        let isChecked = (e.type === 'change') ? this.checked : !$(this).prop('checked');
        
        console.log("Event Master Checkbox (" + e.type + ")! Menuju State:", isChecked);
        
        isPreventLoop = true;
        $('.order-item-checkbox').each(function() {
            $(this).prop('checked', isChecked);
            if (isICheckActive($(this))) {
                $(this).iCheck(isChecked ? 'check' : 'uncheck');
            }
            handleIdSelection(String($(this).val()), isChecked);
        });
        isPreventLoop = false;
        
        sinkronisasiTombolAksi();
    });

    // ========================================================
    // LOGIKA SINKRONISASI MANIPULASI URL TOMBOL
    // ========================================================
    function sinkronisasiTombolAksi() {
        let storedIds = getStoredIds();
        let totalDicentang = storedIds.length;

        if (totalDicentang > 0) {
            let stringIds = storedIds.join(',');
            
            $('#pilihan-counter').removeClass('d-none').addClass('d-inline-flex');
            $('#count-terpilih').text(totalDicentang);

            let separatorExcel = urlAsliExcel.includes('?') ? '&' : '?';
            let separatorPdfStok = urlAsliPdfStok.includes('?') ? '&' : '?';

            $('#btn-excel').attr('href', urlAsliExcel + separatorExcel + 'selected_ids=' + stringIds);
            $('#btn-pdf-stok').attr('href', urlAsliPdfStok + separatorPdfStok + 'selected_ids=' + stringIds);
            $('#btn-pdf-all').attr('href', urlPrintMassal + '?selected_ids=' + stringIds);
            
            $('#btn-pdf-all').text('Cetak Terpilih (' + totalDicentang + ')').removeClass('btn-primary').addClass('btn-info');
        } else {
            $('#pilihan-counter').addClass('d-none').removeClass('d-inline-flex');
            $('#count-terpilih').text('0');

            $('#btn-excel').attr('href', urlAsliExcel);
            $('#btn-pdf-stok').attr('href', urlAsliPdfStok);
            $('#btn-pdf-all').attr('href', urlAsliPdfAll);
            
            $('#btn-pdf-all').text('Cetak PDF All').removeClass('btn-info').addClass('btn-primary');
        }
    }

    // Reset via klik tombol Reset atau pencarian baru
    $(document).on('click', '#btn-reset-filter', function() {
        localStorage.removeItem(STORAGE_KEY);
    });

    // Reset memori sehabis cetak / ekspor massal berhasil
    $(document).on('click', '#btn-pdf-stok, #btn-pdf-all', function() {
        let storedIds = getStoredIds();
        if (storedIds.length > 0) {
            setTimeout(function() {
                localStorage.removeItem(STORAGE_KEY);
                location.reload();
            }, 1000);
        }
    });

    // Beri sedikit jeda aman biar DOM stabil baru pulihkan state centangan
    setTimeout(pulihkanCentangan, 300);
});
</script>
@endpush