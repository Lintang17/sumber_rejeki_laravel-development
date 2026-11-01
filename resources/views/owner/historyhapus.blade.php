@extends('layouts.admin')

@section('content')
<div class="content-wrapper history-page">
    <div class="row">
        <div class="col-12">
            <div class="card history-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                        <div>
                            <h4 class="history-title">
                                <i class="mdi mdi-history"></i> History Hapus
                            </h4>
                            <p class="text-muted mb-0">
                                Riwayat data yang dihapus dari seluruh fitur aplikasi.
                            </p>
                        </div>
                    </div>

                    <div class="filter-card mb-4">
                        <form method="GET" action="{{ route('owner.historyhapus') }}">
                            <div class="filter-content">
                                <div class="filter-fields">
                                    <div class="filter-item">
                                        <label for="bulan">Bulan</label>
                                        <select name="bulan" id="bulan" class="form-control">
                                            <option value="">Semua Bulan</option>
                                            @foreach ([
                                                1 => 'Januari', 2 => 'Februari',
                                                3 => 'Maret', 4 => 'April',
                                                5 => 'Mei', 6 => 'Juni',
                                                7 => 'Juli', 8 => 'Agustus',
                                                9 => 'September', 10 => 'Oktober',
                                                11 => 'November', 12 => 'Desember'
                                            ] as $angka => $nama)
                                                <option value="{{ $angka }}"
                                                    {{ request('bulan') == $angka ? 'selected' : '' }}>
                                                    {{ $nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="filter-item">
                                        <label for="tahun">Tahun</label>
                                        <select name="tahun" id="tahun" class="form-control">
                                            <option value="">Semua Tahun</option>
                                            @for ($tahun = date('Y'); $tahun >= date('Y') - 5; $tahun--)
                                                <option value="{{ $tahun }}"
                                                    {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                                    {{ $tahun }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-filter">
                                        <i class="mdi mdi-magnify"></i> Filter
                                    </button>

                                    <a href="{{ route('owner.historyhapus') }}" class="btn btn-reset">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-heading">
                        <div>
                            <h5>Daftar Riwayat Penghapusan</h5>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table history-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Jenis Data</th>
                                    <th>Nama Data</th>
                                    <th>Dihapus Oleh</th>
                                    <th>Tanggal Dihapus</th>
                                    <th>Batas Pemulihan</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($history as $item)
                                    @php
                                        $batasPemulihan = \Carbon\Carbon::parse($item->dihapus_pada)->addDays(30);
                                        $sudahDipulihkan = !is_null($item->dipulihkan_pada);
                                        $sudahPermanen = !is_null($item->dihapus_permanen_pada);
                                        $melewatiBatas = now()->greaterThan($batasPemulihan);
                                    @endphp

                                    <tr>
                                        <td>{{ $history->firstItem() + $loop->index }}</td>
                                        <td>
                                            <span class="type-label">
                                                {{ $item->jenis_data }}
                                            </span>
                                        </td>
                                        <td class="font-weight-bold text-dark">
                                            {{ $item->nama_data ?? '-' }}
                                        </td>
                                        <td>
                                            <i class="mdi mdi-account-outline text-muted"></i>
                                            {{ $item->nama_user ?? '-' }}
                                        </td>
                                        <td>
                                            <div class="date-text">
                                                <strong>{{ \Carbon\Carbon::parse($item->dihapus_pada)->format('d-m-Y') }}</strong>
                                                <small>
                                                    {{ \Carbon\Carbon::parse($item->dihapus_pada)->format('H:i') }} WIB
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="date-text recovery-date">
                                                <strong>{{ $batasPemulihan->format('d-m-Y') }}</strong>
                                                <small>
                                                    {{ $batasPemulihan->format('H:i') }} WIB
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($sudahDipulihkan)
                                                <span class="status-label status-success">
                                                    <i class="mdi mdi-check-circle"></i> Dipulihkan
                                                </span>
                                            @elseif ($sudahPermanen)
                                                <span class="status-label status-dark">
                                                    <i class="mdi mdi-delete-forever"></i> Dihapus Permanen
                                                </span>
                                            @elseif ($melewatiBatas)
                                                <span class="status-label status-warning">
                                                    <i class="mdi mdi-alert-circle"></i> Melewati 30 Hari
                                                </span>
                                            @else
                                                <span class="status-label status-info">
                                                    <i class="mdi mdi-clock-outline"></i> Dalam Masa Pemulihan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (!$sudahDipulihkan && !$sudahPermanen)
                                                {{-- Tombol Pulihkan hanya tersedia selama 30 hari --}}
                                                @if (!$melewatiBatas)
                                                    <form action="{{ route('owner.historyhapus.pulihkan', $item->id) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Pulihkan data ini?')">
                                                        @csrf

                                                        <button type="submit" class="btn btn-restore btn-sm">
                                                            <i class="mdi mdi-restore"></i> Pulihkan
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Owner boleh menghapus permanen kapan saja --}}
                                                @if (!in_array($item->jenis_data, ['Penjualan', 'Pembelian']))
                                                    <form action="{{ route('owner.historyhapus.permanen', $item->id) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Hapus data ini secara permanen? Data tidak dapat dikembalikan!')">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-permanent btn-sm">
                                                            <i class="mdi mdi-delete-forever"></i> Hapus Permanen
                                                        </button>
                                                    </form>
                                                @endif

                                                @if (in_array($item->jenis_data, ['Penjualan', 'Pembelian']))
                                                    <span class="text-muted small">Tidak tersedia</span>
                                                @endif

                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="empty-state">
                                                <i class="mdi mdi-delete-empty-outline"></i>
                                                <h5>Belum Ada Riwayat</h5>
                                                <p>Belum terdapat data penghapusan sesuai filter yang dipilih.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-area">
                        <div class="pagination-text">
                            Menampilkan {{ $history->firstItem() ?? 0 }}–{{ $history->lastItem() ?? 0 }}
                            dari {{ $history->total() }} data
                        </div>

                        {{ $history->links() }}
                    </div>

                    <div class="info-box">
                        <div class="info-icon">
                            <i class="mdi mdi-information"></i>
                        </div>
                        <div>
                            <strong>Informasi Penghapusan</strong>
                            <p>
                                Data dapat dipulihkan selama 30 hari sejak tanggal penghapusan.
                                Setelah melewati batas tersebut, Owner dapat menghapus data secara permanen.
                                Data Penjualan dan Pembelian tidak dapat dihapus permanen karena diperlukan
                                untuk laporan dan pembukuan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .history-page {
        padding-bottom: 25px;
    }
    .history-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(122, 79, 79, .09);
        overflow: hidden;
    }
    .history-card .card-body {
        padding: 25px;
    }
    .history-title {
        color: #7A4F4F;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 7px;
    }
    .history-title i {
        color: #A97474;
        margin-right: 5px;
    }
    .filter-card {
        background: #fcf8f8;
        border: 1px solid #eadbdb;
        border-radius: 10px;
        padding: 18px 20px;
    }
    .filter-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 20px;
    }
    .filter-fields {
        display: flex;
        align-items: flex-end;
        gap: 15px;
        flex: 1;
    }
    .filter-item {
        width: 210px;
        max-width: 100%;
    }
    .filter-item label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #665050;
        margin-bottom: 7px;
    }
    .filter-item .form-control {
        height: 42px;
        border: 1px solid #dfcaca;
        border-radius: 7px;
        background: white;
        font-size: 13px;
    }
    .filter-item .form-control:focus {
        border-color: #A97474;
        box-shadow: 0 0 0 2px rgba(169, 116, 116, .12);
    }
    .filter-buttons {
        display: flex;
        gap: 9px;
        flex-shrink: 0;
    }
    .filter-buttons .btn {
        height: 42px;
        padding: 0 19px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
    }
    .btn-filter {
        background: #7A4F4F;
        color: white;
        border: 1px solid #7A4F4F;
    }
    .btn-filter:hover {
        background: #613c3c;
        color: white;
    }
    .btn-reset {
        background: white;
        color: #7A4F4F;
        border: 1px solid #A97474;
    }
    .btn-reset:hover {
        background: #f4e8e8;
        color: #613c3c;
    }
    .table-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        gap: 10px;
    }
    .table-heading h5 {
        color: #514040;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .table-heading span {
        color: #999;
        font-size: 12px;
    }
    .history-table {
        min-width: 1100px;
        margin-bottom: 0;
    }
    .history-table thead th {
        background: #7A4F4F;
        color: white;
        font-size: 12px;
        font-weight: 600;
        padding: 15px 12px;
        border: none;
        white-space: nowrap;
        vertical-align: middle;
    }
    .history-table thead th:first-child {
        border-radius: 7px 0 0 0;
    }
    .history-table thead th:last-child {
        border-radius: 0 7px 0 0;
    }
    .history-table tbody td {
        padding: 14px 12px;
        border-bottom: 1px solid #f0e7e7;
        vertical-align: middle;
        font-size: 12px;
        color: #555;
    }
    .history-table tbody td .date-text {
        min-width: 115px;
        white-space: nowrap;
    }
    .history-table tbody tr:nth-child(even) {
        background: #fcf9f9;
    }
    .history-table tbody tr:hover {
        background: #f7eeee;
    }
    .type-label {
        display: inline-block;
        background: #f0e0e0;
        color: #7A4F4F;
        border-radius: 5px;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 700;
    }
    .date-text strong {
        display: block;
        color: #493737;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: .2px;
    }
   .date-text small {
        display: block;
        margin-top: 5px;
        padding-left: 0;
        color: #777;
        font-size: 12px;
        font-weight: 700;
    }
    .status-label {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 7px 10px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }
    .status-success {
        background: #d9f2e2;
        color: #21834b;
    }
    .status-dark {
        background: #e3e3e3;
        color: #555;
    }
    .status-warning {
        background: #fff0ce;
        color: #a16c00;
    }
    .status-info {
        background: #dceefa;
        color: #2876a6;
    }
    .action-btn {
        border: none;
        border-radius: 5px;
        padding: 7px 10px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .btn-restore {
        background: #25975a;
        color: white;
    }
    .btn-restore:hover {
        background: #197743;
        color: white;
    }
    .btn-permanent {
        background: #c94343;
        color: white;
    }
    .btn-permanent:hover {
        background: #a92e2e;
        color: white;
    }
    .empty-state {
        text-align: center;
        padding: 45px 15px;
    }
    .empty-state i {
        font-size: 50px;
        color: #c9a7a7;
    }
    .empty-state h5 {
        color: #7A4F4F;
        font-weight: 700;
        margin-top: 10px;
    }
    .empty-state p {
        color: #999;
        font-size: 12px;
        margin: 0;
    }
    .pagination-area {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
    }
    .pagination-text {
        color: #888;
        font-size: 12px;
    }
    .pagination-area nav {
        margin-bottom: 0;
    }
    .pagination-area .pagination {
        margin-bottom: 0;
    }
    .pagination-area .page-link {
        color: #7A4F4F;
    }
    .pagination-area .page-item.active .page-link {
        background: #7A4F4F;
        border-color: #7A4F4F;
        color: white;
    }
    .info-box {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        background: #f3f7fc;
        border: 1px solid #d9e8f5;
        border-left: 4px solid #428bca;
        border-radius: 7px;
        padding: 15px;
        margin-top: 25px;
    }
    .info-icon {
        color: #428bca;
        font-size: 25px;
    }
    .info-box strong {
        color: #3e6381;
        font-size: 13px;
    }
    .info-box p {
        color: #607d94;
        font-size: 12px;
        line-height: 1.7;
        margin: 5px 0 0;
    }

    @media (max-width: 767px) {
        .history-card .card-body {
            padding: 16px;
        }
        .history-title {
            font-size: 19px;
        }
        .total-box {
            margin-top: 15px;
            width: 100%;
        }
        .filter-content {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }
        .filter-fields {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
            gap: 12px;
        }
        .filter-item {
            width: 100%;
        }
        .filter-buttons {
            display: flex;
            width: 100%;
        }
        .filter-buttons .btn {
            flex: 1;
        }
        .table-heading {
            align-items: flex-start;
            flex-direction: column;
        }
        .pagination-area {
            align-items: flex-start;
            flex-direction: column;
        }
        .info-box {
            padding: 12px;
        }
    }

    @media (min-width: 768px) and (max-width: 991px) {
        .filter-content {
            align-items: flex-end;
        }
        .filter-item {
            width: 160px;
        }
        .filter-buttons .btn {
            padding: 0 12px;
        }
    }
</style>
@endsection