@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #2c3e50;">
                                <i class="mdi mdi-account-group" style="color: #3498db;"></i>
                                Data Internal
                            </h5>
                            <span style="font-size: 13px; color: #7f8c8d;">
                                <i class="mdi mdi-information-outline"></i>
                                Kelola akun pengguna sistem
                            </span>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" 
                             style="border-radius: 8px; border-left: 4px solid #27ae60; background: #f0fff4;">
                            <i class="mdi mdi-check-circle" style="color: #27ae60;"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="mdi mdi-information-outline"></i>
                        <strong>Info Update</strong>

                        <div class="mt-2">
                            {!! nl2br(e(session('info'))) !!}
                        </div>

                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" 
                             style="border-radius: 8px; border-left: 4px solid #dc3545; background: #fdf0f0;">
                            <i class="mdi mdi-alert-circle" style="color: #dc3545;"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table" id="table" style="font-size: 14px;">
                            <thead style="background: #f8f9fa; border-top: 2px solid #e9ecef;">
                                <tr>
                                    <th class="text-center" width="50" style="font-weight: 600; color: #495057;">No</th>
                                    <th style="font-weight: 600; color: #495057;">Nama</th>
                                    <th style="font-weight: 600; color: #495057;">Email</th>
                                    <th class="text-center" style="font-weight: 600; color: #495057;">Level</th>
                                    <th class="text-center" style="font-weight: 600; color: #495057;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $nomor = 1; @endphp
                                @foreach ($users as $user)
                                    <tr style="border-bottom: 1px solid #f1f3f5;">
                                        <td class="text-center fw-bold" style="color: #6c757d;">{{ $nomor++ }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div style="width: 32px; height: 32px; background: {{ $user->role == 'Owner' ? '#27ae60' : ($user->role == 'Admin' ? '#3498db' : ($user->role == 'Gudang' ? '#f39c12' : '#9b59b6')) }}; 
                                                          border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 13px; margin-right: 10px;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <span>{{ $user->name }}</span>
                                                @if($user->id == auth()->user()->id)
                                                    <span style="background: #e9ecef; color: #495057; padding: 2px 10px; border-radius: 4px; font-size: 10px; margin-left: 8px; font-weight: 500;">
                                                        <i class="mdi mdi-account-check"></i> Anda
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td style="color: #636e72;">
                                            <i class="mdi mdi-email-outline" style="font-size: 14px; margin-right: 5px; color: #b2bec3;"></i>
                                            {{ $user->email }}
                                        </td>
                                         <td class="text-center">
                                            @if($user->role == 'Owner')
                                                <span style="background: #27ae60; color: white; padding: 4px 14px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                    <i class="mdi mdi-shield-accoun" style="font-size: 12px;"></i> Owner
                                                </span>
                                            @elseif($user->role == 'Admin')
                                                <span style="background: #3498db; color: white; padding: 4px 14px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                    <i class="mdi mdi-shield-account" style="font-size: 12px;"></i> Admin
                                                </span>
                                            @elseif($user->role == 'Gudang')
                                                <span style="background: #f39c12; color: white; padding: 4px 14px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                    <i class="mdi mdi-warehouse" style="font-size: 12px;"></i> Gudang
                                                </span>
                                            @else
                                                <span style="background: #9b59b6; color: white; padding: 4px 14px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                    <i class="mdi mdi-cash-register" style="font-size: 12px;"></i> Kasir
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" style="gap:4px;">
                                                <a href="{{ url('owner/internaledit', $user->id) }}"
                                                   class="btn btn-sm btn-warning"
                                                   style="width:32px;height:32px;padding:0;border-radius:6px;display:flex;align-items:center;justify-content:center;"
                                                   title="Edit {{ $user->name }}">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger btn-delete"
                                                        data-id="{{ $user->id }}"
                                                        data-name="{{ $user->name }}"
                                                        style="width:32px;height:32px;padding:0;border-radius:6px;display:flex;align-items:center;justify-content:center;"
                                                        title="Hapus {{ $user->name }}">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 pt-2" style="border-top: 1px solid #f1f3f5; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 12px; color: #b2bec3;">
                            <i class="mdi mdi-database"></i>
                            Total: {{ $users->count() }} pengguna
                        </span>
                        <span style="font-size: 12px; color: #b2bec3;">
                            <i class="mdi mdi-clock-outline"></i>
                            {{ now()->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom: none; padding-bottom: 0;">
                <h5 class="modal-title" id="deleteModalLabel" style="color: #dc3545; font-weight: 600;">
                    <i class="mdi mdi-alert-circle" style="font-size: 24px;"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px 24px;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="width: 70px; height: 70px; background: #f8d7da; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                        <i class="mdi mdi-delete" style="font-size: 35px; color: #dc3545;"></i>
                    </div>
                </div>
                <p style="font-size: 15px; text-align: center; margin-bottom: 5px;">
                    Apakah Anda yakin ingin menghapus user:
                </p>
                <p style="font-size: 18px; font-weight: 600; text-align: center; color: #2c3e50;" id="deleteUserName">
                    -
                </p>
                <div style="background: #fff3cd; border-radius: 8px; padding: 12px 16px; margin-top: 15px; border-left: 4px solid #ffc107;">
                    <p style="margin: 0; font-size: 13px; color: #856404;">
                        <i class="mdi mdi-information-outline"></i>
                        <strong>Peringatan:</strong> Data yang dihapus tidak dapat dikembalikan!
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding-top: 0; justify-content: center; gap: 10px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 30px; font-weight: 500;">
                    Batal
                </button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="border-radius: 8px; padding: 8px 30px; font-weight: 500;">
                        <i class="mdi mdi-delete"></i>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 12px !important;
        border: 1px solid #e9ecef !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
    }

    .table {
        margin-bottom: 0;
    }
    .table thead th {
        border-bottom: 2px solid #dee2e6;
        padding: 12px 10px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table tbody td {
        padding: 12px 10px;
        vertical-align: middle;
    }
    .table tbody tr {
        transition: background 0.2s ease;
    }
    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .btn[data-toggle="tooltip"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .alert {
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
    }
    .alert .btn-close {
        padding: 8px;
        font-size: 12px;
    }

    .modal-content {
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 15px !important;
        }
        .table {
            font-size: 12px !important;
        }
        .table thead th {
            font-size: 10px !important;
        }
        .table tbody td {
            padding: 8px 6px !important;
        }
        .badge {
            font-size: 10px !important;
            padding: 3px 10px !important;
        }
        .btn {
            font-size: 12px !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded');
        
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        console.log('Tooltip initialized: ' + tooltipTriggerList.length);

        var deleteModalEl = document.getElementById('deleteModal');
        if (deleteModalEl) {
            var deleteModal = new bootstrap.Modal(deleteModalEl);
            console.log('Modal initialized');
            
            document.querySelectorAll('.btn-delete').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var userId = this.getAttribute('data-id');
                    var userName = this.getAttribute('data-name');
                    
                    console.log('Delete button clicked - ID: ' + userId + ', Name: ' + userName);
                    
                    document.getElementById('deleteUserName').textContent = userName;
                    document.getElementById('deleteForm').action = '/owner/internalhapus/' + userId;
                    deleteModal.show();
                });
            });
        } else {
            console.error('Modal element not found!');
        }
    });
</script>
@endsection