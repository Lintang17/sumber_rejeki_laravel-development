@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title">History Hapus</h4>
                    <p class="card-description">
                        Riwayat data yang dihapus dari seluruh fitur aplikasi.
                    </p>

                    {{-- Filter Bulan dan Tahun --}}
                    <form method="GET" action="{{ route('owner.historyhapus') }}" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label>Bulan</label>
                                <select name="bulan" class="form-control">
                                    <option value="">Semua Bulan</option>
                                    @foreach ([
                                        1 => 'Januari',
                                        2 => 'Februari',
                                        3 => 'Maret',
                                        4 => 'April',
                                        5 => 'Mei',
                                        6 => 'Juni',
                                        7 => 'Juli',
                                        8 => 'Agustus',
                                        9 => 'September',
                                        10 => 'Oktober',
                                        11 => 'November',
                                        12 => 'Desember'
                                    ] as $angka => $nama)
                                        <option value="{{ $angka }}"
                                            {{ request('bulan') == $angka ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Tahun</label>
                                <select name="tahun" class="form-control">
                                    <option value="">Semua Tahun</option>
                                    @for ($tahun = date('Y'); $tahun >= date('Y') - 5; $tahun--)
                                        <option value="{{ $tahun }}"
                                            {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                            {{ $tahun }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-3 mt-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>

                                <a href="{{ route('owner.historyhapus') }}"
                                    class="btn btn-secondary">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Tabel History --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Data</th>
                                    <th>Nama Data</th>
                                    <th>Dihapus Oleh</th>
                                    <th>Tanggal Dihapus</th>
                                    <th>Batas Pemulihan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
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

                                        <td>{{ $item->jenis_data }}</td>

                                        <td>{{ $item->nama_data ?? '-' }}</td>

                                        <td>{{ $item->nama_user ?? '-' }}</td>

                                        <td>
                                            {{ \Carbon\Carbon::parse($item->dihapus_pada)->format('d-m-Y H:i') }}
                                        </td>

                                        <td>
                                            {{ $batasPemulihan->format('d-m-Y H:i') }}
                                        </td>

                                        <td>
                                            @if ($sudahDipulihkan)
                                                <span class="badge badge-success">Dipulihkan</span>
                                            @elseif ($sudahPermanen)
                                                <span class="badge badge-dark">Dihapus Permanen</span>
                                            @elseif ($melewatiBatas)
                                                <span class="badge badge-warning">Melewati 30 Hari</span>
                                            @else
                                                <span class="badge badge-info">Dalam Masa Pemulihan</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if (!$sudahDipulihkan && !$sudahPermanen)
                                                @if (!$melewatiBatas)
                                                    <form action="{{ route('owner.historyhapus.pulihkan', $item->id) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Pulihkan data ini?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="mdi mdi-restore"></i> Pulihkan
                                                        </button>
                                                    </form>
                                                @endif

                                                @if (!in_array($item->jenis_data, ['Penjualan', 'Pembelian']) && $melewatiBatas)
                                                    <form action="{{ route('owner.historyhapus.permanen', $item->id) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Hapus data ini secara permanen? Data tidak dapat dikembalikan.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="mdi mdi-delete-forever"></i> Hapus Permanen
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Belum ada riwayat penghapusan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $history->links() }}
                    </div>

                    <div class="alert alert-info mt-3">
                        <strong>Informasi:</strong>
                        Data dapat dipulihkan selama 30 hari sejak tanggal penghapusan.
                        Data Penjualan dan Pembelian tidak dapat dihapus permanen karena
                        diperlukan untuk laporan dan pembukuan.
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection