@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    @php
        $disabled = !$bolehApprove || $po->status != 'Pending';

        $totalQty = $po->detail->sum('qty');
        $totalHppAdmin = $po->detail->sum(function($item){
            return ($item->hpp_estimasi_admin ?? 0) * $item->qty;
        });
        $totalHppGudang = $po->detail->sum(function($item){
            return $item->hpp_estimasi * $item->qty;
        });
        $totalHargaJual = $po->detail->sum(function($item){
            return $item->harga_jual * $item->qty;
        });
    @endphp

    <div class="card border-0 shadow" style="border-radius:12px;">

        <div class="card-header border-0 py-3 px-4" style="background:linear-gradient(#d47e89bc, #7c663a); border-radius:12px 12px 0 0;">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="text-white">
                    <h4 class="fw-bold mb-0">
                        Detail Purchase Order
                    </h4>
                    <span style="font-size:14px; opacity:0.85;">
                        <strong>{{ $po->kode_po }}</strong> &bull;
                        {{ \Carbon\Carbon::parse($po->tanggal)->translatedFormat('d F Y') }}
                    </span>
                </div>
                <div>
                    @php
                        $statusBadge = [
                            'Pending'   => ['bg' => '#fef3c7', 'text' => '#b45309'],
                            'Disetujui' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                            'Diproses'  => ['bg' => '#d1fae5', 'text' => '#065f46'],
                            'Diambil'   => ['bg' => '#f1f5f9', 'text' => '#475569'],
                            'Dikirim'   => ['bg' => '#ede9fe', 'text' => '#5b21b6'],
                            'Selesai'   => ['bg' => '#d1fae5', 'text' => '#065f46'],
                        ];
                    @endphp
                    <span class="badge px-4 py-2" style="background:{{ $statusBadge[$po->status]['bg'] ?? '#e5e7eb' }}; color:{{ $statusBadge[$po->status]['text'] ?? '#111827' }}; font-size:14px; font-weight:700; border-radius:30px;">
                        <i class="fas {{ $po->status == 'Selesai' ? 'fa-check-double' : ($po->status == 'Pending' ? 'fa-clock' : 'fa-check-circle') }} mr-1"></i>
                        {{ $po->status }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body p-4">

            {{-- ALERT --}}
            @if(!$bolehApprove && $po->status=='Pending')
                <div class="alert border-0 mb-4" style="border-radius:10px; background:#fffbeb; border-left:5px solid #f59e0b; padding:14px 20px;">
                    <span style="color:#78350f; font-weight:700; font-size:14px;">
                        <i class="fas fa-exclamation-triangle mr-2" style="color:#d97706;"></i>
                        Gudang harus mengisi Estimasi Akhir, HPP Gudang, dan Keterangan terlebih dahulu sebelum Owner dapat mengisi Harga Jual & HPP Final.
                    </span>
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="border rounded p-3" style="border-color:#e2e8f0 !important; background:#fafbfc;">
                        <h6 class="fw-bold mb-2" style="color:#1e293b; font-size:15px; border-bottom:2px solid #4f46e5; padding-bottom:6px;">
                            <i class="fas fa-user mr-2" style="color:#4f46e5;"></i> Informasi Customer
                        </h6>
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px dashed #e2e8f0;">
                            <span style="color:#64748b; font-weight:700; font-size:14px;">Kode PO</span>
                            <span style="color:#1e293b; font-weight:700; font-size:14px;">{{ $po->kode_po }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px dashed #e2e8f0;">
                            <span style="color:#64748b; font-weight:700; font-size:14px;">Customer</span>
                            <span style="color:#1e293b; font-weight:700; font-size:14px;">{{ $po->customer }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px dashed #e2e8f0;">
                            <span style="color:#64748b; font-weight:700; font-size:14px;">No HP</span>
                            <span style="color:#1e293b; font-weight:700; font-size:14px;">{{ $po->no_hp }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span style="color:#64748b; font-weight:700; font-size:14px;">Alamat</span>
                            <span style="color:#1e293b; font-weight:700; font-size:14px;">{{ $po->alamat }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3" style="border-color:#e2e8f0 !important; background:#fafbfc;">
                        <h6 class="fw-bold mb-2" style="color:#1e293b; font-size:15px; border-bottom:2px solid #4f46e5; padding-bottom:6px;">
                            <i class="fas fa-clock mr-2" style="color:#4f46e5;"></i> Informasi Produksi
                        </h6>
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px dashed #e2e8f0;">
                            <span style="color:#64748b; font-weight:700; font-size:14px;">Estimasi Awal</span>
                            <span style="color:#1e293b; font-weight:700; font-size:14px;">{{ $po->estimasi_awal ? \Carbon\Carbon::parse($po->estimasi_awal)->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px dashed #e2e8f0;">
                            <span style="color:#64748b; font-weight:700; font-size:14px;">Estimasi Akhir</span>
                            <span style="color:#1e293b; font-weight:700; font-size:14px;">{{ $po->estimasi_akhir ? \Carbon\Carbon::parse($po->estimasi_akhir)->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px dashed #e2e8f0;">
                            <span style="color:#64748b; font-weight:700; font-size:14px;">Keterangan</span>
                            <span style="color:#1e293b; font-weight:700; font-size:14px;">{{ $po->keterangan ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span style="color:#64748b; font-weight:700; font-size:14px;">DP Customer</span>
                            <span style="color:#1e293b; font-weight:700; font-size:14px;">{{ $po->dp ? 'Rp ' . number_format($po->dp,0,',','.') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border rounded p-3 mb-4" style="border-color:#e2e8f0 !important; background:#fafbfc;">
                <h6 class="fw-bold mb-3" style="color:#1e293b; font-size:15px; border-bottom:2px solid #4f46e5; padding-bottom:6px;">
                    <i class="fas fa-images mr-2" style="color:#4f46e5;"></i> Produk
                </h6>
                <div class="row g-3">
                    @foreach($po->detail as $detail)
                        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                            <div class="border rounded" style="border-color:#e2e8f0 !important; overflow:hidden; background:#fff;">
                                @if($detail->foto)
                                    <a href="{{ asset('assets/foto/po/'.$detail->foto) }}" target="_blank">
                                        <img src="{{ asset('assets/foto/po/'.$detail->foto) }}" style="width:100%; height:140px; object-fit:cover; display:block;">
                                    </a>
                                @else
                                    <div style="height:140px; display:flex; align-items:center; justify-content:center; background:#f8fafc; color:#cbd5e1;">
                                        <i class="fas fa-image fa-3x"></i>
                                    </div>
                                @endif
                                <div class="p-2 text-center">
                                    <div style="font-weight:700; font-size:13px; color:#1e293b;">{{ $detail->produk }}</div>
                                    <span class="badge" style="background:#eef2ff; color:#4f46e5; font-weight:700; font-size:11px; border-radius:20px; padding:4px 12px;">Qty {{ $detail->qty }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <form action="{{ url('owner/po/' . $po->id . '/approve') }}" method="POST">
                @csrf

                <div class="border rounded" style="border-color:#e2e8f0 !important; overflow:hidden; background:#fff;">
                    <div class="p-3" style="background:linear-gradient(135deg, #f8fafc, #eef2ff); border-bottom:2px solid #4f46e5;">
                        <h6 class="fw-bold mb-0" style="color:#1e293b; font-size:15px;">
                            Detail Produk
                        </h6>
                        <small style="color:#64748b; font-weight:600; font-size:12px;">Owner mengisi Harga Jual &amp; HPP Final</small>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="table mb-0" style="border-collapse:collapse; font-size:13px;">
                            <thead style="background:#f1f5f9; border-bottom:2px solid #e2e8f0;">
                                <tr>
                                    <th style="padding:10px 12px; font-weight:700; color:#475569; text-align:center; width:45px;">No</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#475569; width:160px;">Produk</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#475569;">Deskripsi</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#475569; text-align:center; width:60px;">Qty</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#475569; text-align:right; width:140px;">HPP Admin</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#475569; text-align:right; width:140px;">HPP Gudang</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#4f46e5; text-align:right; width:140px;">HPP Final</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#475569; text-align:right; width:140px;">Harga Jual</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#d97706; text-align:right; width:130px;">Subtotal Harga Jual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalHppAdminDisplay = 0;
                                    $totalHppGudangDisplay = 0;
                                @endphp
                                @foreach($po->detail as $index => $detail)
                                    @php
                                        $subtotalJual = $detail->harga_jual * $detail->qty;
                                    @endphp
                                    <tr style="border-bottom:1px solid #f1f3f5;">
                                        <td style="padding:10px 12px; text-align:center; vertical-align:middle;">
                                            <span style="display:inline-block; width:28px; height:28px; border-radius:50%; background:#4f46e5; color:#fff; font-weight:700; font-size:12px; text-align:center; line-height:28px;">{{ $index+1 }}</span>
                                        </td>
                                        <td style="padding:10px 12px; vertical-align:middle; font-weight:700; color:#1e293b; font-size:13px;">{{ $detail->produk }}</td>
                                        <td style="padding:10px 12px; vertical-align:middle; color:#64748b; font-weight:600; font-size:13px;">{{ $detail->deskripsi ?? '-' }}</td>
                                        <td style="padding:10px 12px; text-align:center; vertical-align:middle;">
                                            <span class="badge" style="background:#1e293b; color:#fff; font-weight:700; border-radius:20px; padding:4px 12px; font-size:12px;">{{ $detail->qty }}</span>
                                        </td>
                                        <td style="padding:10px 12px; text-align:right; vertical-align:middle; font-weight:700; color:#475569; font-size:13px;">
                                            Rp {{ number_format($detail->hpp_estimasi_admin ?? 0,0,',','.') }}
                                        </td>
                                        <td style="padding:10px 12px; text-align:right; vertical-align:middle; font-weight:700; color:#475569; font-size:13px;">
                                            Rp {{ number_format($detail->hpp_estimasi,0,',','.') }}
                                        </td>
                                        <td style="padding:10px 12px; text-align:right; vertical-align:middle;">
                                            <input type="text" name="hpp_final[]" class="form-control form-control-owner rupiah" value="{{ number_format($detail->hpp_final ?? 0,0,',','.') }}" {{ $disabled ? 'readonly' : '' }} style="border-radius:8px; border:1.5px solid #c7d2fe; padding:6px 10px; font-weight:700; font-size:13px; width:100%; text-align:right; height:36px; background:#fafbfc;">
                                        </td>
                                        <td style="padding:10px 12px; text-align:right; vertical-align:middle;">
                                            <input type="text" name="harga_jual[]" class="form-control form-control-owner rupiah" value="{{ number_format($detail->harga_jual,0,',','.') }}" {{ $disabled ? 'readonly' : '' }} autocomplete="off" required style="border-radius:8px; border:1.5px solid #fde68a; padding:6px 10px; font-weight:700; font-size:13px; width:100%; text-align:right; height:36px; background:#fffbeb;">
                                        </td>
                                        <td style="padding:10px 12px; text-align:right; vertical-align:middle; font-weight:700; color:#d97706; font-size:13px;">
                                            Rp {{ number_format($detail->harga_jual*$detail->qty,0,',','.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-3">
                        <div class="p-3 text-center border rounded" style="border-color:#e2e8f0 !important; background:linear-gradient(135deg, #f8fafc, #fff);">
                            <small style="color:#94a3b8; font-size:11px; font-weight:700; text-transform:uppercase;">Total Qty</small>
                            <h4 class="fw-bold mb-0" style="color:#1e293b;">{{ $totalQty }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 text-center border rounded" style="border-color:#e2e8f0 !important; background:linear-gradient(135deg, #eef2ff, #fff);">
                            <small style="color:#94a3b8; font-size:11px; font-weight:700; text-transform:uppercase;">Total HPP Admin</small>
                            <h5 class="fw-bold mb-0" style="color:#4f46e5;">Rp {{ number_format($totalHppAdmin,0,',','.') }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 text-center border rounded" style="border-color:#e2e8f0 !important; background:linear-gradient(135deg, #d1fae5, #fff);">
                            <small style="color:#94a3b8; font-size:11px; font-weight:700; text-transform:uppercase;">Total HPP Gudang</small>
                            <h5 class="fw-bold mb-0" style="color:#059669;">Rp {{ number_format($totalHppGudang,0,',','.') }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 text-center border rounded" style="border-color:#e2e8f0 !important; background:linear-gradient(135deg, #fef3c7, #fff);">
                            <small style="color:#94a3b8; font-size:11px; font-weight:700; text-transform:uppercase;">Total Harga Jual</small>
                            <h5 class="fw-bold mb-0" style="color:#d97706;">Rp {{ number_format($totalHargaJual,0,',','.') }}</h5>
                        </div>
                    </div>
                </div>

                {{-- APPROVAL --}}
                @if(!$disabled)
                    <div class="border rounded p-3 mt-4" style="border-color:#e2e8f0 !important; background:#fafbfc;">
                        <h6 class="fw-bold mb-3" style="color:#1e293b; font-size:15px; border-bottom:2px solid #4f46e5; padding-bottom:6px;">
                            <i class="fas fa-check-circle mr-2" style="color:#4f46e5;"></i> Approval Owner
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <label style="font-weight:700; color:#475569; font-size:14px;">Keputusan Approval</label>
                                <select name="status" class="form-select" required style="border-radius:8px; border:1.5px solid #e2e8f0; padding:8px 12px; font-size:14px; width:100%; height:40px; background:#fff; font-weight:700;">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Approve" style="color:#059669;">✓ Approve PO</option>
                                    <option value="Reject" style="color:#dc2626;">✗ Reject PO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top:2px solid #e2e8f0;">
                    <a href="{{ url('owner/po') }}" class="btn px-4" style="border-radius:10px; font-weight:700; background:#4f46e5; border:1.5px solid #4f46e5; color:#fff; padding:8px 20px; font-size:14px;">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    @if(!$disabled)
                        <button type="submit" class="btn px-4" style="border-radius:10px; font-weight:700; background:linear-gradient(135deg, #4f46e5, #6366f1); color:#fff; border:none; padding:8px 24px; font-size:14px;">
                            <i class="fas fa-save mr-2"></i> Simpan Review
                        </button>
                    @endif
                </div>

            </form>

        </div>
    </div>
</div>

<style>
    .table th, .table td {
        padding: 10px 12px !important;
        font-size: 13px !important;
    }
    .table thead th {
        border-bottom: 2px solid #e2e8f0 !important;
        background: #f1f5f9 !important;
    }
    .table tbody tr:last-child {
        border-bottom: none !important;
    }
    .table tbody tr:hover {
        background: #f8fafc !important;
    }
    .form-control-owner:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79,70,229,0.15) !important;
        outline: none !important;
    }
    .btn:hover {
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }
    .btn:first-child:hover {
        background: #4338ca !important;
        border-color: #4338ca !important;
        color: #fff !important;
    }
    .btn:last-child:hover {
        background: linear-gradient(135deg, #4338ca, #4f46e5) !important;
        box-shadow: 0 4px 16px rgba(79,70,229,0.40);
    }
    .border {
        border-color: #e2e8f0 !important;
    }
    .rounded {
        border-radius: 10px !important;
    }
    .form-select {
        font-size: 14px !important;
        height: 40px !important;
        padding: 8px 12px !important;
        border-radius: 8px !important;
    }
    .card {
        border-radius: 12px !important;
        overflow: hidden !important;
    }
    .card-header {
        border-radius: 0 !important;
    }
    .badge {
        border-radius: 30px !important;
    }
    .alert {
        border-radius: 10px !important;
    }
</style>

<script>
    function formatRupiah(angka){
        let number_string = angka.replace(/[^0-9]/g,'');
        if(number_string=="") return "";
        return new Intl.NumberFormat('id-ID').format(number_string);
    }

    document.querySelectorAll('.rupiah').forEach(function(input){
        input.addEventListener('input',function(){
            this.value = formatRupiah(this.value);
        });
        input.addEventListener('keydown',function(e){
            if(!((e.key>='0' && e.key<='9') || ['Backspace','Delete','ArrowLeft','ArrowRight','Tab','Enter'].includes(e.key))){
                e.preventDefault();
            }
        });
    });

    document.querySelector('form').addEventListener('submit',function(){
        document.querySelectorAll('input[name="harga_jual[]"], input[name="hpp_final[]"]').forEach(function(input){
            input.value = input.value.replace(/[^0-9]/g,'');
        });
    });
</script>
@endsection