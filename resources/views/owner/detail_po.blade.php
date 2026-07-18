@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    @php
        $gudangSelesai = !empty($po->keterangan);
        foreach ($po->detail as $detail) {
            if (empty($detail->hpp_estimasi) || $detail->hpp_estimasi <= 0) {
                $gudangSelesai = false;
                break;
            }
        }
        $disabled = !$gudangSelesai || $po->status != 'Pending';

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
            @if(!$gudangSelesai)
            <div class="alert alert-warning border-0 mb-4">
                <strong>Gudang belum mengisi keterangan dan HPP Gudang.</strong><br>
                Owner dapat mengisi HPP Final, Harga Jual, dan melakukan approval setelah Gudang mengisi HPP Gudang serta Keterangan.
            </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="border rounded p-3" style="border-color:#e2e8f0 !important; background:#fafbfc;">
                        <h6 class="fw-bold mb-3" style="color:#2563eb; font-size:15px; border-bottom:2px solid #2563eb; padding-bottom:8px;">
                            Informasi Customer
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
                        <h6 class="fw-bold mb-3" style="color:#2563eb; font-size:15px; border-bottom:2px solid #2563eb; padding-bottom:8px;">
                            Informasi Produksi
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
                                    <th class="text-center" style="width:90px;">Foto</th>
                                    <th style="width:220px; min-width:220px; max-width:220px;">Deskripsi</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#475569; text-align:center; width:60px;">Qty</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#475569; text-align:right; width:140px;">HPP Admin</th>
                                    <th style="padding:10px 12px; font-weight:700; color:#475569; text-align:right; width:140px;">HPP Gudang</th>
                                    <th style="background:#ffe082; color:#8a5a00; text-align:right; width:110px; min-width:110px; max-width:110px;">HPP Final</th>
                                    <th style="background:#ffe082; color:#8a5a00; text-align:right; width:110px; min-width:110px; max-width:110px;">Harga Jual</th>
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
                                        <td class="text-center">
                                            @if($detail->foto)
                                                <a href="#"
                                                    data-toggle="modal"
                                                    data-target="#fotoModal{{ $detail->id }}">
                                                    <img src="{{ asset('assets/foto/po/'.$detail->foto) }}"
                                                        style="width:55px; height:55px; object-fit:cover; border-radius:8px; border:2px solid #dee2e6; cursor:pointer;">
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
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
                                        <td style="padding:10px 12px; background:#fff8db; text-align:right; vertical-align:middle;">
                                            <input type="text" name="hpp_final[]" class="form-control form-control-owner rupiah" value="{{ number_format($detail->hpp_final ?? 0,0,',','.') }}" {{ $disabled ? 'readonly' : '' }} style="background:#fff8db; border:1.5px solid #f4c430; border-radius:6px;
                                                padding:4px 8px; height:32px; max-width:120px; margin-left:auto; font-weight:700; font-size:12px; text-align:right; color:#8a5a00;">
                                        </td>
                                        <td style="padding:10px 12px; background:#fff8db; text-align:right; vertical-align:middle;">
                                            <input type="text" name="harga_jual[]" class="form-control form-control-owner rupiah" value="{{ number_format($detail->harga_jual,0,',','.') }}" {{ $disabled ? 'readonly' : '' }} autocomplete="off" required style="background:#fff8db; border:1.5px solid #f4c430; border-radius:6px;
                                                padding:4px 8px; height:32px; max-width:120px; margin-left:auto; font-weight:700; font-size:12px; text-align:right; color:#8a5a00;">
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @foreach($po->detail as $detail)
                                    <div class="modal fade"
                                         id="fotoModal{{ $detail->id }}"
                                         tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        {{ $detail->produk }}
                                                    </h5>
                                                    <button class="close"
                                                            data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                <img src="{{ asset('assets/foto/po/'.$detail->foto) }}"
                                                     class="img-fluid rounded shadow">
                                                        <div class="mt-3">
                                                            <span class="badge badge-primary">
                                                                Qty:
                                                                {{ $detail->qty }}
                                                             </span>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 border-top pt-3">
                    <div class="d-flex justify-content-between py-2">
                        <span style="font-weight:700; font-size:15px; color:#1e293b;">
                            Total HPP Admin
                        </span>
                        <strong class="text-primary">
                            Rp {{ number_format($totalHppAdmin,0,',','.') }}
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span style="font-weight:700; font-size:15px; color:#1e293b;">
                            Total HPP Gudang
                        </span>
                        <strong class="text-success">
                            Rp {{ number_format($totalHppGudang,0,',','.') }}
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3"
                         style="border-top:2px dashed #dee2e6;">
                        <span style="font-size:17px;font-weight:700;">
                            Total Harga Jual
                        </span>
                        <span style="font-size:22px;font-weight:700;color:#d97706;">
                            Rp {{ number_format($totalHargaJual,0,',','.') }}
                        </span>
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
                                    <option value="Approve" style="color:#059669;">Approve PO</option>
                                    <option value="Reject" style="color:#dc2626;">Reject PO</option>
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